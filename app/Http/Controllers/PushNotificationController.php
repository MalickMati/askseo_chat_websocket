<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'endpoint' => 'required|string',
                'keys.p256dh' => 'required|string',
                'keys.auth' => 'required|string',
            ]);

            $user = User::where('email', session('user_email'))->first();
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'Not authenticated'], 401);
            }

            // Keep a single subscription per user (latest wins)
            $existing = PushSubscription::where('user_id', $user->id)->first();

            if ($existing) {
                $noChange =
                    $existing->endpoint === $data['endpoint'] &&
                    $existing->public_key === $data['keys']['p256dh'] &&
                    $existing->auth_token === $data['keys']['auth'];

                if ($noChange) {
                    return response()->json(['ok' => true, 'message' => 'Already up to date'], 200);
                }

                $existing->fill([
                    'endpoint' => $data['endpoint'],
                    'public_key' => $data['keys']['p256dh'],
                    'auth_token' => $data['keys']['auth'],
                ])->save();

                // Clean any legacy duplicates for this user
                PushSubscription::where('user_id', $user->id)
                    ->where('id', '!=', $existing->id)
                    ->delete();

                return response()->json(['ok' => true, 'message' => 'Updated'], 200);
            }

            // First time: create
            $sub = PushSubscription::create([
                'user_id' => $user->id,
                'endpoint' => $data['endpoint'],
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
            ]);

            return response()->json(['ok' => true, 'id' => $sub->id], 201);

        } catch (\Throwable $e) {
            Log::error('Push subscribe failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'message' => 'Server error'], 500);
        }
    }
}
