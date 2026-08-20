@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-2xl backdrop-blur md:p-8">
    <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Create IT Support Ticket</h1>
            <p class="text-sm text-slate-600">Share the issue clearly so the IT team can respond quickly.</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Back</a>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Please fix the following issues:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tickets.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data" data-confirm="Are you sure you want to submit this ticket?">
        @csrf
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <label class="mb-2 block text-sm font-medium text-slate-700">Submitted By</label>
            <input type="text" value="{{ auth()->user()?->name ?? 'Unknown employee' }}" class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2.5" readonly>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('title') border-red-500 @enderror" placeholder="Short and descriptive title" required>
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('description') border-red-500 @enderror" rows="5" placeholder="Describe the issue, symptoms, and impact" required>{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Priority</label>
                <select name="priority" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" required>
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                <input type="text" name="category" value="{{ old('category') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('category') border-red-500 @enderror" placeholder="Example: Network" required>
                @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Department</label>
            <select name="department_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('department_id') border-red-500 @enderror" required>
                <option value="">Select a department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
            @error('department_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Attachment (optional)</label>
            <div class="flex items-center gap-3">
                <label for="attachment-input" class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <span>Attach file</span>
                </label>
                <input id="attachment-input" type="file" name="attachment" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.txt,.doc,.docx" onchange="document.getElementById('attachment-name').textContent = this.files[0] ? this.files[0].name : ''">
                <span id="attachment-name" class="text-sm text-slate-500"></span>
            </div>
            <p class="mt-1 text-xs text-slate-400">PDF, image, text, or Word files. Max 2 MB.</p>
            @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition hover:bg-blue-700">Submit Ticket</button>
            <a href="{{ route('tickets.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-center font-medium text-slate-700 transition hover:bg-slate-100">Cancel</a>
        </div>
    </form>
</div>
@endsection
