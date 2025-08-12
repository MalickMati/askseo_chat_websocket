<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocketBroadcastController extends Controller
{
    public function showForm()
    {
        return view('socket-broadcast');
    }

    public function sendJson($message, $senderid, $recieverid)
    {
        try {
            $response = Http::post('http://173.208.156.154:3000/receive_json', [
                'event' => 'json_data',
                'data' => [
                    'chatapp_data' => [
                        'message' => $message,
                        'user' => $senderid,
                        'reciever' => $recieverid,
                        'sent_at' => now()->toDateTimeString()
                    ]
                ],
                'key' => 'askseo-chat-key-123'
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
