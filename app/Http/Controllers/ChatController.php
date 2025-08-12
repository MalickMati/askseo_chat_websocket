<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use App\Models\Group;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupMessageRead;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

class ChatController extends Controller
{
    public function sendprivatemessage($message, $senderid, $recieverid)
    {
        try {
            $response = Http::post('https://socket.askseo.me/receive_json', [
                'event' => 'json_data',
                'channel' => 'private',
                'data' => [
                    'chatapp_data' => [
                        'message' => $message,
                        'user' => $senderid,
                        'reciever' => $recieverid,
                        'sent_at' => now()->toDateTimeString()
                    ]
                ],
                'key' => 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function sendPushToUser(int $userId, string $title, string $body, string $url = '/chat', ?string $tag = null): void
    {
        try {
            $subs = PushSubscription::where('user_id', $userId)
                ->get()
                ->unique('endpoint'); // <— dedupe

            if ($subs->isEmpty())
                return;

            $payload = json_encode([
                'title' => $title,
                'body' => \Illuminate\Support\Str::limit(strip_tags($body), 180),
                'url' => url($url),
                'tag' => $tag ?? 'chat',
            ], JSON_UNESCAPED_UNICODE);

            $webPush = new \Minishlink\WebPush\WebPush([
                'VAPID' => [
                    'subject' => config('services.vapid.subject'),
                    'publicKey' => config('services.vapid.public_key'),
                    'privateKey' => config('services.vapid.private_key'),
                ],
            ], ['TTL' => 300, 'urgency' => 'high']);

            foreach ($subs as $s) {
                $webPush->queueNotification(
                    \Minishlink\WebPush\Subscription::create([
                        'endpoint' => $s->endpoint,
                        'keys' => ['p256dh' => $s->public_key, 'auth' => $s->auth_token],
                    ]),
                    $payload
                );
            }

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    $endpoint = (string) $report->getRequest()->getUri();
                    $status = optional($report->getResponse())->getStatusCode();
                    if (in_array($status, [404, 410])) {
                        \App\Models\PushSubscription::where('endpoint', $endpoint)->delete();
                    } else {
                        \Log::warning('WebPush failed', [
                            'endpoint' => $endpoint,
                            'status' => $status,
                            'reason' => $report->getReason(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('WebPush exception: ' . $e->getMessage());
        }
    }

    public function markreadprivatemessage($senderid, $recieverid)
    {
        try {
            $response = Http::post('https://socket.askseo.me//receive_json', [
                'event' => 'json_data',
                'channel' => 'private_message_read',
                'data' => [
                    'chatapp_data' => [
                        'user' => $senderid,
                        'reciever' => $recieverid,
                        'sent_at' => now()->toDateTimeString()
                    ]
                ],
                'key' => 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendgroupsocketmessage($message, $senderid, array $receiverIds)
    {
        try {
            $response = Http::post('https://socket.askseo.me/receive_json', [
                'event' => 'json_data',
                'channel' => 'group',
                'data' => [
                    'chatapp_data' => [
                        'message' => $message,
                        'user' => $senderid,
                        'receivers' => $receiverIds,
                        'sent_at' => now()->toDateTimeString()
                    ]
                ],
                'key' => 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
            ]);

            return true;
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Socket send error: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllUnreadMessages()
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // 🔹 Unread personal messages
        $unreadPersonalCount = Message::where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->count();

        // 🔹 Groups the user is in
        $groupIds = Group::whereHas('members', function ($q) use ($currentUser) {
            $q->where('user_id', $currentUser->id);
        })->pluck('id');

        // 🔹 Unread group messages
        $unreadGroupCount = DB::table('messages')
            ->leftJoin('group_message_reads', function ($join) use ($currentUser) {
                $join->on('messages.id', '=', 'group_message_reads.message_id')
                    ->where('group_message_reads.user_id', '=', $currentUser->id);
            })
            ->whereIn('messages.group_id', $groupIds)
            ->where('messages.sender_id', '!=', $currentUser->id)
            ->whereNull('group_message_reads.read_at')
            ->count();

        return response()->json([
            'unread_personal_count' => $unreadPersonalCount,
            'unread_group_count' => $unreadGroupCount,
            'total_unread' => $unreadPersonalCount + $unreadGroupCount
        ]);
    }

    public function getMessages($receiver_id)
    {
        $sender = User::where("email", session("user_email"))->first();

        if (!$sender || $sender->status !== 'active') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = Message::where(function ($query) use ($sender, $receiver_id) {
            $query->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver_id);
        })
            ->orWhere(function ($query) use ($sender, $receiver_id) {
                $query->where('sender_id', $receiver_id)
                    ->where('receiver_id', $sender->id);
            })
            ->orderBy('created_at', 'desc') // get latest messages first
            ->paginate(50);

        // Reverse to show oldest first (like a normal chat window)
        $reversed = collect($messages->items())->reverse()->values();

        return response()->json([
            'messages' => $reversed,
            'has_more' => $messages->hasMorePages(),
            'next_page' => $messages->nextPageUrl()
        ]);
    }

    public function getPrivatefilter(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $filter = $request->input('filter');

        $query = Message::where(function ($query) use ($receiverId) {
            $query->where(function ($q) use ($receiverId) {
                $q->where('sender_id', auth()->id())
                    ->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)
                    ->where('receiver_id', auth()->id());
            });
        });

        $this->applyFilter($query, $filter); // query passed by reference

        return response()->json([
            'messages' => $query->latest()->paginate(20)
        ]);
    }

    public function getGroupMessages($groupId)
    {
        $messages = Message::where('group_id', $groupId)
            ->orderBy('sent_at', 'desc') // get latest 100 messages
            ->paginate(100);

        $orderedMessages = collect($messages->items())->reverse()->values(); // reverse for chronological order

        return response()->json([
            'success' => true,
            'messages' => $orderedMessages,
            'has_more' => $messages->hasMorePages(),
            'next_page' => $messages->nextPageUrl()
        ]);
    }

    public function getGroupfilter(Request $request)
    {
        $groupId = $request->input('group_id');
        $filter = $request->input('filter'); // 'media', 'documents', 'links'

        $query = Message::where('group_id', $groupId);

        $this->applyFilter($query, $filter);

        return response()->json([
            'messages' => $query->latest()->paginate(20)
        ]);
    }

    private function applyFilter(&$query, $filter)
    {
        if ($filter === 'media') {
            $query->whereIn('file_extension', ['mp3', 'mp4', 'mkv', 'avi', 'webm', 'wav', 'ogg', 'png', 'jpg', 'jpeg', 'gif', 'webp']);
        } elseif ($filter === 'documents') {
            $query->whereIn('file_extension', ['pdf', 'docx', 'doc', 'txt', 'xls', 'xlsx', 'apk', 'zip', 'rar']);
        } elseif ($filter === 'links') {
            $query->where('message', 'REGEXP', 'https?://|www\\.|\\b[a-z0-9.-]+\\.(com|net|org|pk|me|info)\\b');
        }
    }

    public function sendGroupMessage(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'group_id' => 'required|exists:groups,id',
                'message' => 'nullable|string|max:50000',
                'file' => 'nullable|file|max:153600',
                'subtitle' => 'nullable|string|max:50000', // Add validation for subtitle
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        // Verify if the user is part of the group
        $verified_user = GroupMember::where('group_id', '=', $request->group_id)
            ->where('user_id', '=', Auth::user()->id)
            ->first();

        if (!$verified_user) {
            return response()->json([
                'success' => false,
                'message' => 'You are not in the group! Redirecting...',
                'redirect' => '/chat'
            ]);
        }

        try {
            // Get the current authenticated user
            $user = User::where('email', session('user_email'))->firstOrFail();
            $messages = [];

            // Check if both message and file are provided
            if ($request->filled('message') && $request->hasFile('file')) {
                // Both message and file are provided, store them in one message
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $filename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $uniqueName = $filename . '_' . Str::random(8) . '.' . $extension;

                // Store the file in the 'chat_files' directory
                $path = $file->storeAs('chat_files', $uniqueName, 'public');

                // Create a combined message
                $message = Message::create([
                    'sender_id' => $user->id,
                    'group_id' => $request->group_id,
                    'message' => $request->message,  // Text message
                    'file_path' => $path,  // File message
                    'subtitle' => $request->subtitle,  // Optional subtitle for the file
                    'sent_at' => now(),
                    'type' => 'file_with_message',  // A new type indicating file with message
                    'file_extension' => $extension,
                ]);

                // Mark the message as read by the sender
                GroupMessageRead::create([
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ]);

                // Get group members except the sender
                $receiverIds = GroupMember::where('group_id', $request->group_id)
                    ->where('user_id', '!=', $user->id)
                    ->pluck('user_id')
                    ->toArray();

                // Send the combined message to other group members
                if (!empty($receiverIds)) {
                    $this->sendgroupsocketmessage($message->message, $user->name, $receiverIds);
                }

                $messages[] = $message;
            }
            // If only a message is provided (no file)
            elseif ($request->filled('message')) {
                // Save the text message
                $message = Message::create([
                    'sender_id' => $user->id,
                    'group_id' => $request->group_id,
                    'message' => $request->message,  // Only text message
                    'subtitle' => $request->subtitle,  // Optional subtitle for the message
                    'sent_at' => now(),
                    'type' => 'text',  // Message type is text
                ]);

                // Mark the message as read by the sender
                GroupMessageRead::create([
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ]);

                // Get group members except the sender
                $receiverIds = GroupMember::where('group_id', $request->group_id)
                    ->where('user_id', '!=', $user->id)
                    ->pluck('user_id')
                    ->toArray();

                // Send the text message to other group members
                if (!empty($receiverIds)) {
                    $this->sendgroupsocketmessage($request->message, $user->name, $receiverIds);
                }

                $messages[] = $message;
            }
            // If only a file is provided (no text message)
            elseif ($request->hasFile('file')) {
                // Handle file message only
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $filename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $uniqueName = $filename . '_' . Str::random(8) . '.' . $extension;

                // Store the file in the 'chat_files' directory
                $path = $file->storeAs('chat_files', $uniqueName, 'public');

                // Create the file message
                $message = Message::create([
                    'sender_id' => $user->id,
                    'group_id' => $request->group_id,
                    'file_path' => $path,  // File path
                    'message' => $originalName,  // Original file name as message
                    'subtitle' => $request->subtitle,  // Subtitle (optional)
                    'sent_at' => now(),
                    'type' => 'file',  // Message type is file
                    'file_extension' => $extension,
                ]);

                // Mark the message as read by the sender
                GroupMessageRead::create([
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ]);

                // Get group members except the sender
                $receiverIds = GroupMember::where('group_id', $request->group_id)
                    ->where('user_id', '!=', $user->id)
                    ->pluck('user_id')
                    ->toArray();

                // Send the file message to other group members
                if (!empty($receiverIds)) {
                    $this->sendgroupsocketmessage($originalName, $user->name, $receiverIds);
                }

                $messages[] = $message;
            }

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong while sending group message.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getChatList(Request $request)
    {
        $currentUser = User::where("email", session("user_email"))->first();

        if (!$currentUser || $currentUser->status !== 'active') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Users list
        $users = User::where('email', '!=', $currentUser->email)->get();
        $userList = $users->map(function ($user) use ($currentUser) {
            $lastMessage = Message::where(function ($query) use ($user, $currentUser) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id);
            })
                ->orWhere(function ($query) use ($user, $currentUser) {
                    $query->where('sender_id', $currentUser->id)
                        ->where('receiver_id', $user->id);
                })
                ->orderByDesc('sent_at')
                ->first();

            $formattedTime = null;
            $messageText = null;

            if ($lastMessage) {
                $sentAt = \Carbon\Carbon::parse($lastMessage->sent_at);
                $now = \Carbon\Carbon::now();

                if ($sentAt->isToday()) {
                    $formattedTime = $sentAt->format('h:i A'); // e.g., 04:35 PM
                } elseif ($sentAt->isYesterday()) {
                    $formattedTime = 'Yesterday';
                } elseif ($sentAt->diffInDays($now) < 7) {
                    $formattedTime = $sentAt->format('l'); // e.g., Monday
                } else {
                    $formattedTime = $sentAt->format('M d'); // e.g., Jul 21
                }

                $messageText = $lastMessage->message ?? '[File]';
            }

            return [
                'id' => $user->id,
                'username' => $user->name,
                'img' => $user->image ?? asset('assets/images/default.png'),
                'status' => $user->status_mode ?? 'offline',
                'last_message' => $messageText ?? 'Type a message to get started',
                'last_time' => $formattedTime,
            ];
        });

        // Groups list (simplified for now)
        $groups = Group::all()->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
            ];
        });

        return response()->json([
            'groups' => $groups,
            'users' => $userList,
        ]);
    }

    public function sendMessage(Request $request)
    {
        try {
            // Get the message content
            $messageRaw = $request->message ?? null;
            $subtitle = $request->subtitle ?? null;

            // Validate basic input first
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|exists:users,id',
                'message' => 'nullable|string|max:50000',
                'file' => 'nullable|file|max:153600', // 150 MB limit for the file
                'subtitle' => 'nullable|string|max:50000', // Subtitle length limit
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Retrieve sender information based on email
            $sender = User::where('email', session('user_email'))->first();

            if (!$sender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sender not found.',
                ], 403);
            }

            $messages = [];

            // Handle file upload first, if present
            if ($request->hasFile('file')) {
                try {
                    // Handle file upload and storing the file
                    $file = $request->file('file');
                    $originalName = $file->getClientOriginalName();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = $filename . '_' . Str::random(8) . '.' . $extension;

                    // Store the file in the 'chat_files' directory
                    $path = $file->storeAs('chat_files', $uniqueName, 'public');

                    // Create file message with subtitle
                    $fileMessage = Message::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $request->receiver_id,
                        'file_path' => $path, // Path to the stored file
                        'message' => $originalName, // Original file name as message
                        'type' => 'file',
                        'file_extension' => $extension,
                        'subtitle' => $subtitle, // Save the subtitle if provided
                    ]);

                    // Send the file message (using your existing sendprivatemessage method)
                    if (!$this->sendprivatemessage($originalName, $sender->name, $request->receiver_id)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to deliver the message to the user!',
                        ]);
                    }
                    $currentuser = User::where('id', '=', Auth::user()->id)->first();

                    Log::info('name: ' . $currentuser->name);

                    $this->sendPushToUser(
                        $request->receiver_id,
                        'New message from ' . $currentuser->name,
                        'File received',
                        '/chat',
                        'chat-' . $sender->id
                    );
                    $messages[] = $fileMessage;

                } catch (\Exception $e) {
                    Log::error('File upload or message save failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload and save file.',
                    ], 500);
                }
            }

            // Handle text message, if present
            if ($request->filled('message') && !$request->hasFile('file')) {
                try {
                    // Save text message without a file
                    $textMessage = Message::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $request->receiver_id,
                        'message' => $messageRaw,
                        'type' => 'text',
                        'subtitle' => null, // Text message doesn't need a subtitle
                    ]);

                    // Send the text message (using your existing sendprivatemessage method)
                    if (!$this->sendprivatemessage($messageRaw, $sender->name, $request->receiver_id)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to deliver the message to the user!',
                        ]);
                    }
                    $currentuser = User::where('id', '=', Auth::user()->id)->first();

                    Log::info('name: ' . $currentuser->name);
                    $this->sendPushToUser(
                        $request->receiver_id,
                        'New message from ' . $currentuser->name,
                        $messageRaw,
                        '/chat',
                        'chat-' . $sender->id
                    );
                    $messages[] = $textMessage;

                } catch (\Exception $e) {
                    Log::error('Text message save failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save text message.',
                    ], 500);
                }
            }

            // If no messages or files were processed
            if (empty($messages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No message or file provided.',
                ], 400);
            }

            // Respond with success and the saved messages
            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            Log::error('Unexpected error in sendMessage: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }



    public function getSidebarData()
    {
        if (!session()->has('user_email')) {
            return redirect('/')->with('error', 'Session expired! Login Again');
        }

        $currentUser = User::where('email', session('user_email'))->first();

        // Get individual users (excluding current user)
        $users = User::where('id', '!=', $currentUser->id)
            ->where('status', '=', 'active')
            ->get()
            ->map(function ($user) use ($currentUser) {
                // Last message between current user and this user
                $lastMessage = Message::where(function ($q) use ($user, $currentUser) {
                    $q->where('sender_id', $currentUser->id)->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($user, $currentUser) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $currentUser->id);
                })->latest()->first();

                // Count unread messages FROM this user TO current user
                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id)
                    ->whereNull('read_at')
                    ->count();

                // Format timestamp
                $formattedTime = '';
                if ($lastMessage) {
                    $sentAt = \Carbon\Carbon::parse($lastMessage->sent_at ?? $lastMessage->created_at);
                    $now = \Carbon\Carbon::now();

                    if ($sentAt->isToday()) {
                        $formattedTime = $sentAt->format('h:i A');
                    } elseif ($sentAt->isYesterday()) {
                        $formattedTime = 'Yesterday';
                    } elseif ($sentAt->diffInDays($now) < 7) {
                        $formattedTime = $sentAt->format('l');
                    } else {
                        $formattedTime = $sentAt->format('M d');
                    }
                }

                return [
                    'id' => $user->id,
                    'username' => $user->name,
                    'img' => $user->image,
                    'status' => $user->status_mode,
                    'last_message' => $lastMessage ? ($lastMessage->message ?? '[File]') : '',
                    'last_time' => $formattedTime,
                    'unread_count' => $unreadCount,
                    'last_timestamp' => $lastMessage ? ($lastMessage->sent_at ?? $lastMessage->created_at) : null, // for sorting
                ];
            })
            // Sort users by latest message timestamp descending
            ->sortByDesc(function ($user) {
                return $user['last_timestamp'];
            })
            // Re-index to reset numeric keys (important if you're returning JSON)
            ->values();

        // Get groups the current user is in
        $groups = Group::whereHas('members', function ($q) use ($currentUser) {
            $q->where('user_id', $currentUser->id);
        })
            ->with('messages')
            ->get()
            ->map(function ($group) use ($currentUser) {
                $lastMessage = $group->messages()->latest()->first();

                $unreadCount = DB::table('messages')
                    ->leftJoin('group_message_reads', function ($join) use ($currentUser) {
                        $join->on('messages.id', '=', 'group_message_reads.message_id')
                            ->where('group_message_reads.user_id', '=', $currentUser->id);
                    })
                    ->where('messages.group_id', $group->id)
                    ->where('messages.sender_id', '!=', $currentUser->id)
                    ->whereNull('group_message_reads.read_at')
                    ->count();

                $formattedTime = '';
                if ($lastMessage) {
                    $sentAt = \Carbon\Carbon::parse($lastMessage->sent_at ?? $lastMessage->created_at);
                    $now = \Carbon\Carbon::now();

                    if ($sentAt->isToday()) {
                        $formattedTime = $sentAt->format('h:i A');
                    } elseif ($sentAt->isYesterday()) {
                        $formattedTime = 'Yesterday';
                    } elseif ($sentAt->diffInDays($now) < 7) {
                        $formattedTime = $sentAt->format('l');
                    } else {
                        $formattedTime = $sentAt->format('M d');
                    }
                }

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'last_message' => $lastMessage ? ($lastMessage->message ?? '[File]') : 'Group Chat',
                    'last_time' => $formattedTime,
                    'unread_count' => $unreadCount,
                ];
            });

        return response()->json([
            'users' => $users,
            'groups' => $groups
        ]);
    }

    public function markAsRead(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $senderId = $request->sender_id;
        $currentUser = User::where('email', session('user_email'))->first();

        Message::where('sender_id', $senderId)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->markreadprivatemessage($senderId, $currentUser->id);

        return response()->json(['status' => 'success']);
    }
    public function markGroupMessagesRead($groupId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }

        $verified_user = GroupMember::where('group_id', '=', $groupId)->where('user_id', '=', Auth::user()->id)->first();
        if (!$verified_user) {
            return response()->json([
                'success' => false,
                'message' => 'You are removed from group! Redirecting...',
                'redirect' => '/chat'
            ]);
        }

        $currentUser = User::where('email', session('user_email'))->first();

        $unreadMessages = Message::where('group_id', $groupId)
            ->where('sender_id', '!=', $currentUser->id)
            ->leftJoin('group_message_reads', function ($join) use ($currentUser) {
                $join->on('messages.id', '=', 'group_message_reads.message_id')
                    ->where('group_message_reads.user_id', '=', $currentUser->id);
            })
            ->whereNull('group_message_reads.read_at')
            ->select('messages.id')
            ->get();

        foreach ($unreadMessages as $msg) {
            GroupMessageRead::updateOrInsert(
                ['message_id' => $msg->id, 'user_id' => $currentUser->id],
                ['read_at' => Carbon::now()]
            );
        }

        return response()->json(['success' => true]);
    }

    public function leavegroup(Request $request, Group $group)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $user = User::where('email', '=', session('user_email'))->first();

        $group->members()->detach($user->id);

        return response()->json(['success' => true]);
    }

    public function membersList(Group $group)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $user = User::where('email', '=', session('user_email'))->first();
        if ($user->type === 'super_admin' || $user->type === 'admin' || $user->type === 'moderator') {
            $allUsers = User::select('id', 'name')->get();
            $groupMemberIds = $group->members()->pluck('users.id')->toArray();

            return response()->json([
                'all_users' => $allUsers,
                'group_member_ids' => $groupMemberIds
            ]);
        }
    }

    public function getMembers($id)
    {
        $group = Group::with(['members:id,name,email,image'])->findOrFail($id);

        $members = $group->members->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->image
                    ? asset($user->image)
                    : asset('assets/images/default.png'),
            ];
        });

        return response()->json([
            'members' => $members
        ]);
    }


    public function addMembers(Request $request, Group $group)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $user = User::where('email', '=', session('user_email'))->first();

        if ($user->type === 'super_admin' || $user->type === 'admin' || $user->type === 'moderator') {
            $userIds = $request->input('users', []);
            foreach ($userIds as $userId) {
                $group->members()->syncWithoutDetaching([$userId]);
            }

            return response()->json(['message' => 'Users added successfully.']);
        } else {
            return response()->json(['message' => 'Error while adding users!']);
        }

    }

    public function removeMembers(Request $request, Group $group)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Session expired!'], 401);
        }

        $user = User::where('email', '=', session('user_email'))->first();

        if (in_array($user->type, ['super_admin', 'admin', 'moderator'])) {
            $userIds = $request->input('users', []);
            $group->members()->detach($userIds);

            return response()->json(['message' => 'Members removed successfully.']);
        }

        return response()->json(['message' => 'You do not have permission.'], 403);
    }
}
