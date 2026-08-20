@extends('layouts.app')

@section('content')
<div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[18rem_1fr]">
    <aside class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl">
        <h1 class="text-xl font-semibold text-slate-900">Staff Chat</h1>
        <p class="mt-1 text-sm text-slate-600">Select a department, then choose a user for a private chat.</p>

        <form method="GET" action="{{ route('it-communications.index') }}" class="mt-5 space-y-4">
            <div>
                <label for="department_id" class="mb-2 block text-sm font-medium text-slate-700">Department</label>
                <select id="department_id" name="department_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ $selectedDepartmentId === $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if ($contacts->isNotEmpty())
            <div class="mt-4 space-y-2">
                @foreach ($contacts as $person)
                    @php($unread = $unreadPerContact->get($person->id, 0))
                    <a href="{{ route('it-communications.index', ['department_id' => $selectedDepartmentId, 'contact' => $person->id]) }}"
                       class="chat-contact flex items-center justify-between rounded-xl border {{ $contact?->is($person) ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50' }} px-4 py-3 transition"
                       data-user-id="{{ $person->id }}">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $person->name }}</p>
                            <p class="text-xs text-slate-500">{{ $person->department?->name ?? 'No department' }} &middot; {{ strtoupper(str_replace('_', ' ', $person->role)) }}</p>
                        </div>
                        <span class="chat-contact-badge inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-semibold text-white @if($unread === 0) hidden @endif" data-user-id="{{ $person->id }}">{{ $unread }}</span>
                    </a>
                @endforeach
            </div>
        @elseif ($selectedDepartmentId > 0)
            <p class="mt-4 text-sm text-slate-500">No users found in this department.</p>
        @endif
    </aside>

    <section class="flex min-h-[34rem] flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
        @if ($contact !== null)
            <div class="border-b border-slate-200 pb-4">
                <h2 class="font-semibold text-slate-900">{{ $contact->name }}</h2>
                <p class="text-sm text-slate-600">{{ $contact->department?->name ?? 'No department' }} &middot; {{ strtoupper(str_replace('_', ' ', $contact->role)) }}</p>
            </div>
            <div id="chat-messages" class="flex-1 space-y-3 overflow-y-auto py-5">
                @forelse ($messages as $message)
                    @php($isOwnMessage = $message->sender_id === auth()->id())
                    <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-3 {{ $isOwnMessage ? 'rounded-br-md bg-indigo-600 text-white' : 'rounded-bl-md bg-slate-100 text-slate-800' }}">
                            <p class="mb-1 text-xs font-semibold {{ $isOwnMessage ? 'text-indigo-100' : 'text-slate-500' }}">{{ $isOwnMessage ? 'You' : $message->sender?->name }}</p>
                            <p class="whitespace-pre-wrap text-sm">{{ $message->message }}</p>
                            <p class="mt-2 text-right text-xs {{ $isOwnMessage ? 'text-indigo-100' : 'text-slate-500' }}">{{ $message->created_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-slate-500">Start a conversation with {{ $contact->name }}.</p>
                @endforelse
            </div>
            <form id="chat-form" action="{{ route('it-communications.store') }}" method="POST" class="border-t border-slate-200 pt-4">
                @csrf
                <input type="hidden" name="recipient_id" value="{{ $contact->id }}">
                <div class="flex gap-3">
                    <textarea id="chat-textarea" name="message" rows="2" required maxlength="4000" class="min-w-0 flex-1 rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="Write a message..."></textarea>
                    <button type="submit" class="self-end rounded-full bg-indigo-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-indigo-700">Send</button>
                </div>
                @error('message')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </form>
        @else
            <div class="flex flex-1 items-center justify-center text-center text-slate-600">
                <p>Select a user from the left to start a chat.</p>
            </div>
        @endif
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatBadge = document.getElementById('chat-unread-badge');
    const currentContactId = {{ $contact?->id ?? 'null' }};

    function pollUnreadCounts() {
        fetch('{{ route("it-communications.unread-count") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (chatBadge) {
                if (data.total_unread > 0) {
                    chatBadge.textContent = data.total_unread;
                    chatBadge.style.display = '';
                } else {
                    chatBadge.style.display = 'none';
                }
            }
            document.querySelectorAll('.chat-contact-badge').forEach(function (badge) {
                var userId = badge.getAttribute('data-user-id');
                var count = data.per_contact[userId] || 0;
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        })
        .catch(function () {});
    }

    setInterval(pollUnreadCounts, 10000);

    function pollNewMessages() {
        if (!currentContactId) return;
        fetch('{{ route("it-communications.updates") }}?contact=' + currentContactId, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var container = document.getElementById('chat-messages');
            if (!container) return;
            var lastId = container.getAttribute('data-last-id');
            if (lastId && data.latest_message_id && parseInt(data.latest_message_id) > parseInt(lastId)) {
                window.location.reload();
            }
            if (!lastId && data.latest_message_id) {
                container.setAttribute('data-last-id', data.latest_message_id);
            }
        })
        .catch(function () {});
    }

    var chatContainer = document.getElementById('chat-messages');
    if (chatContainer) {
        var lastMsg = chatContainer.querySelector('[data-last-id]');
    }
    setInterval(pollNewMessages, 5000);
});
</script>
@endsection
