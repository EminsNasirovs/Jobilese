<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get all conversations for the current user
     */
    public function conversations(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::where('employer_id', $user->id)
            ->orWhere('applicant_id', $user->id)
            ->with([
                'employer:id,username,firstname,lastname',
                'applicant:id,username,firstname,lastname',
                'vacancy:id,title,company',
                'latestMessage',
            ])
            ->latest('updated_at')
            ->get();

        // Attach unread count for each conversation
        $conversations->each(function ($conv) use ($user) {
            $conv->unread_count = $conv->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        });

        return response()->json($conversations);
    }

    /**
     * Get or create a conversation between two users about a vacancy
     */
    public function startOrGet(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'other_user_id' => 'required|exists:users,id',
            'vacancy_id'    => 'nullable|exists:job_vacancies,id',
        ]);

        $otherUser = User::find($validated['other_user_id']);

        // Figure out who is employer / applicant
        $employerId  = ($user->role === 'uzņēmējs') ? $user->id : $otherUser->id;
        $applicantId = ($user->role === 'uzņēmējs') ? $otherUser->id : $user->id;
        $vacancyId   = $validated['vacancy_id'] ?? null;

        $conversation = Conversation::firstOrCreate([
            'employer_id'  => $employerId,
            'applicant_id' => $applicantId,
            'vacancy_id'   => $vacancyId,
        ]);

        $conversation->load(
            'employer:id,username,firstname,lastname',
            'applicant:id,username,firstname,lastname',
            'vacancy:id,title,company',
            'messages.sender:id,username,firstname,lastname'
        );

        return response()->json($conversation);
    }

    /**
     * Get messages in a conversation, mark received ones as read
     */
    public function messages(Request $request, $conversationId)
    {
        $user = $request->user();

        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($q) use ($user) {
                $q->where('employer_id', $user->id)
                  ->orWhere('applicant_id', $user->id);
            })
            ->firstOrFail();

        // Mark unread messages from the other person as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender:id,username,firstname,lastname')
            ->oldest()
            ->get();

        return response()->json($messages);
    }

    /**
     * Send a message in a conversation
     */
    public function send(Request $request, $conversationId)
    {
        $user = $request->user();

        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($q) use ($user) {
                $q->where('employer_id', $user->id)
                  ->orWhere('applicant_id', $user->id);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'body' => 'required|string|max:3000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => $validated['body'],
        ]);

        $conversation->touch(); // bump updated_at so it sorts to top

        $message->load('sender:id,username,firstname,lastname');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    /**
     * Total unread message count for the current user (for nav badge)
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $convIds = Conversation::where('employer_id', $user->id)
            ->orWhere('applicant_id', $user->id)
            ->pluck('id');

        $count = Message::whereIn('conversation_id', $convIds)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
