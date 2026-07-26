<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request): Response
    {
        $query = ContactMessage::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $statusCounts = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('status', 'unread')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
        ];

        return Inertia::render('inbox/Index', [
            'messages' => $messages,
            'filters' => $request->only(['search', 'status']),
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Mark the specified message as read.
     */
    public function markAsRead(ContactMessage $contactMessage): RedirectResponse
    {
        if ($contactMessage->status === 'unread') {
            $contactMessage->update(['status' => 'read']);

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Pesan ditandai sebagai dibaca.'),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Mark the specified message as replied.
     */
    public function markAsReplied(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['status' => 'replied']);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Pesan ditandai sebagai sudah direspons.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified message.
     */
    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Pesan berhasil dihapus.'),
        ]);

        return redirect()->back();
    }
}
