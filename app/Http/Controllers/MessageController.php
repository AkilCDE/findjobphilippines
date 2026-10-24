<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Display messaging inbox and active conversation thread.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // Find all distinct contacts that user has exchanged messages with
        $contactIds = Message::where('sender_id', $userId)
            ->pluck('receiver_id')
            ->merge(
                Message::where('receiver_id', $userId)->pluck('sender_id')
            )
            ->unique()
            ->values();

        $contacts = User::whereIn('id', $contactIds)->get();

        // If target user specified in query string
        $targetUserId = $request->query('user_id');
        $activeContact = null;

        if ($targetUserId) {
            $activeContact = User::find($targetUserId);
            if ($activeContact && ! $contacts->contains('id', $activeContact->id)) {
                $contacts->push($activeContact);
            }
        } elseif ($contacts->isNotEmpty()) {
            $activeContact = $contacts->first();
        }

        $messages = collect();
        if ($activeContact) {
            // Mark incoming messages as read
            Message::where('sender_id', $activeContact->id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Fetch chat history
            $messages = Message::where(function ($q) use ($userId, $activeContact) {
                $q->where('sender_id', $userId)
                    ->where('receiver_id', $activeContact->id);
            })->orWhere(function ($q) use ($userId, $activeContact) {
                $q->where('sender_id', $activeContact->id)
                    ->where('receiver_id', $userId);
            })
                ->with(['jobPost'])
                ->oldest()
                ->get();
        }

        // Optional prefilled job post context
        $relatedJob = null;
        if ($request->query('job_id')) {
            $relatedJob = JobPost::find($request->query('job_id'));
        }

        return view('messages.index', compact('contacts', 'activeContact', 'messages', 'relatedJob'));
    }

    /**
     * Send a direct message to another user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:3000'],
            'job_post_id' => ['nullable', 'exists:job_posts,id'],
        ]);

        if ((int) $validated['receiver_id'] === (int) Auth::id()) {
            return back()->with('error', 'You cannot message yourself.');
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'job_post_id' => $validated['job_post_id'] ?? null,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()->route('messages.index', ['user_id' => $validated['receiver_id']])
            ->with('success', 'Message sent.');
    }
}
