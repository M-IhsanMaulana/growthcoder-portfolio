<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Jobs\SendTelegramNotificationJob;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            ...$request->validated(),
            'sender_ip' => $request->ip(),
            'status' => 'unread',
        ]);

        SendTelegramNotificationJob::dispatch($message);

        return response()->json([
            'message' => __('Pesan Anda berhasil terkirim! Saya akan segera menghubungi Anda.'),
        ], 201);
    }
}
