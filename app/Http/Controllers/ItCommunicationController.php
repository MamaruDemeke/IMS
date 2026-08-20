<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralMessageStoreRequest;
use App\Models\AdminSetting;
use App\Models\Department;
use App\Models\GeneralMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItCommunicationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->authorizedUser($request);
        $departments = Department::query()
            ->whereHas('users', fn ($query) => $query->where('is_active', true)->whereKeyNot($user->getKey()))
            ->orderBy('name')
            ->get();
        $selectedDepartmentId = $request->integer('department_id');
        $contacts = $this->staffContacts($user, $selectedDepartmentId);
        $contact = $contacts->firstWhere('id', $request->integer('contact'));
        $messages = $contact === null ? collect() : $this->conversation($user, $contact);

        if ($contact !== null) {
            GeneralMessage::query()
                ->where('sender_id', $contact->getKey())
                ->where('receiver_id', $user->getKey())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $conversationHistory = GeneralMessage::query()
            ->with(['sender:id,name,department_id', 'receiver:id,name,department_id'])
            ->where(fn ($query) => $query->where('sender_id', $user->getKey())->orWhere('receiver_id', $user->getKey()))
            ->latest()
            ->get()
            ->map(fn (GeneralMessage $message) => $message->sender_id === $user->getKey() ? $message->receiver : $message->sender)
            ->filter()
            ->unique('id')
            ->values();

        $unreadPerContact = GeneralMessage::query()
            ->where('receiver_id', $user->getKey())
            ->whereNull('read_at')
            ->selectRaw('sender_id, count(*) as unread')
            ->groupBy('sender_id')
            ->pluck('unread', 'sender_id');

        return view('it-communications.index', compact('departments', 'selectedDepartmentId', 'contacts', 'contact', 'messages', 'conversationHistory', 'unreadPerContact'));
    }

    public function store(GeneralMessageStoreRequest $request): RedirectResponse
    {
        $sender = $this->authorizedUser($request);
        $validated = $request->validated();
        $recipient = User::query()->findOrFail($validated['recipient_id']);

        abort_unless($recipient->getKey() !== $sender->getKey() && $recipient->is_active, 422);

        if (! empty($validated['reply_to_id'])) {
            $replyTo = GeneralMessage::query()->findOrFail($validated['reply_to_id']);
            abort_unless($this->isParticipant($replyTo, $sender) && $this->isParticipant($replyTo, $recipient), 422);
        }

        GeneralMessage::query()->create([
            'sender_id' => $sender->getKey(),
            'receiver_id' => $recipient->getKey(),
            'reply_to_id' => $validated['reply_to_id'] ?? null,
            'message' => $validated['message'],
        ]);

        return redirect()->route('it-communications.index', ['department_id' => $recipient->department_id, 'contact' => $recipient->getKey()]);
    }

    public function destroy(Request $request, GeneralMessage $message): RedirectResponse
    {
        $user = $this->authorizedUser($request);
        abort_unless($message->sender_id === $user->getKey(), 403);

        $recipient = $message->receiver;
        $message->delete();

        return redirect()->route('it-communications.index', ['department_id' => $recipient?->department_id, 'contact' => $recipient?->getKey()]);
    }

    public function updates(Request $request): JsonResponse
    {
        $user = $this->authorizedUser($request);
        $contact = User::query()->find($request->integer('contact'));

        abort_unless($contact !== null && $contact->is_active, 404);

        return response()->json([
            'unread_count' => GeneralMessage::query()->where('receiver_id', $user->getKey())->whereNull('read_at')->count(),
            'latest_message_id' => $this->conversation($user, $contact)->last()?->getKey(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $user = $this->authorizedUser($request);

        $totalUnread = GeneralMessage::query()
            ->where('receiver_id', $user->getKey())
            ->whereNull('read_at')
            ->count();

        $perContact = GeneralMessage::query()
            ->where('receiver_id', $user->getKey())
            ->whereNull('read_at')
            ->selectRaw('sender_id, count(*) as unread')
            ->groupBy('sender_id')
            ->pluck('unread', 'sender_id');

        return response()->json([
            'total_unread' => $totalUnread,
            'per_contact' => $perContact,
        ]);
    }

    private function authorizedUser(Request $request): User
    {
        $user = $request->user();
        abort_unless($user !== null, 403);
        abort_unless(AdminSetting::query()->where('key', 'staff_chat_enabled')->value('value') !== '0', 403);

        return $user;
    }

    private function staffContacts(User $user, int $departmentId): Collection
    {
        return User::query()
            ->whereKeyNot($user->getKey())
            ->where('is_active', true)
            ->when($departmentId > 0, fn ($query) => $query->where('department_id', $departmentId))
            ->orderBy('name')
            ->get();
    }

    private function conversation(User $user, User $contact): Collection
    {
        return GeneralMessage::query()
            ->with('replyTo')
            ->where(function ($query) use ($user, $contact) {
                $query->where('sender_id', $user->getKey())->where('receiver_id', $contact->getKey());
            })
            ->orWhere(function ($query) use ($user, $contact) {
                $query->where('sender_id', $contact->getKey())->where('receiver_id', $user->getKey());
            })
            ->oldest()
            ->get();
    }

    private function isParticipant(GeneralMessage $message, User $user): bool
    {
        return $message->sender_id === $user->getKey() || $message->receiver_id === $user->getKey();
    }
}
