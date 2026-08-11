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
            <input type="file" name="attachment" class="w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2.5 text-sm" accept=".pdf,.jpg,.jpeg,.png,.txt,.doc,.docx">
            @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition hover:bg-blue-700">Submit Ticket</button>
            <a href="{{ route('tickets.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-center font-medium text-slate-700 transition hover:bg-slate-100">Cancel</a>
        </div>
    </form>
</div>
@endsection
