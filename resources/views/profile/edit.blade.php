@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur md:p-8">
        <h1 class="mb-6 text-2xl font-bold text-slate-900">My Profile</h1>

        <div class="flex flex-col items-center gap-6 sm:flex-row">
            <div class="relative">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full border-4 border-slate-200 object-cover shadow-md">
                <label for="photo-input" class="absolute -bottom-1 -right-1 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-slate-200 bg-white shadow-sm transition hover:bg-slate-50" title="Change photo">
                    <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input id="photo-input" type="file" accept="image/jpeg,image/png" class="hidden" onchange="document.getElementById('photo-form').submit()">
            </div>
            <div class="text-center sm:text-left">
                <h2 class="text-xl font-semibold text-slate-900">{{ $user->name }}</h2>
                <p class="text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $user->role)) }} &middot; {{ $user->department?->name ?? 'No department' }}</p>
                <form id="photo-form" action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="profile_photo" accept="image/jpeg,image/png" class="hidden">
                </form>
                @if ($user->profile_photo)
                    <form action="{{ route('profile.photo.remove') }}" method="POST" class="mt-2 inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Remove profile photo?')">Remove photo</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur md:p-8">
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Personal Information</h2>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4" data-confirm="Save changes to your profile?">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('name') border-red-500 @enderror" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('email') border-red-500 @enderror" required>
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('phone') border-red-500 @enderror" placeholder="Optional">
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
                <input type="text" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-600" readonly>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Department</label>
                <input type="text" value="{{ $user->department?->name ?? 'Unassigned' }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-600" readonly>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Email Verified</label>
                <input type="text" value="{{ $user->email_verified_at ? 'Verified on '.$user->email_verified_at->format('M d, Y') : 'Not verified' }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-600" readonly>
            </div>
            <button type="submit" class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition hover:bg-blue-700">Save Changes</button>
        </form>
    </div>
</div>

<script>
document.getElementById('photo-input')?.addEventListener('change', function () {
    if (this.files[0]) {
        var form = document.getElementById('photo-form');
        var fileInput = form.querySelector('input[type="file"]');
        var dt = new DataTransfer();
        dt.items.add(this.files[0]);
        fileInput.files = dt.files;
        if (confirm('Upload this photo?')) {
            form.submit();
        }
    }
});
</script>
@endsection
