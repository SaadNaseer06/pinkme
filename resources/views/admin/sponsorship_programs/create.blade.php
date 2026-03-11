@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        @if (session('success'))
            <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif

        <h1 class="text-2xl font-semibold">Add Sponsorship Program</h1>

        <form method="POST" action="{{ route('sp.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium">Title <span class="text-pink-600">*</span></label>
                <input name="title" type="text" value="{{ old('title') }}" class="mt-1 w-full rounded border p-2"
                    required>
                @error('title')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @php($hasSlug = Schema::hasColumn('sponsorship_programs', 'slug'))
            @if ($hasSlug)
                <div>
                    <label class="block text-sm font-medium">Slug (optional)</label>
                    <input name="slug" type="text" value="{{ old('slug') }}" class="mt-1 w-full rounded border p-2">
                    @error('slug')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium">Funds Needed (Goal) <span class="text-pink-600">*</span></label>
                <input name="goal_amount" type="number" min="1" value="{{ old('goal_amount') }}"
                    class="mt-1 w-full rounded border p-2" required>
                @error('goal_amount')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Status</label>
                    <select name="status" class="mt-1 w-full rounded border p-2">
                        <option value="upcoming" @selected(old('status') === 'upcoming')>Upcoming</option>
                        <option value="active" @selected(old('status') === 'active')>Active</option>
                        <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                        <option value="archived" @selected(old('status') === 'archived')>Archived</option>
                    </select>
                    @error('status')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Starts At</label>
                    <input name="starts_at" type="datetime-local" value="{{ old('starts_at') }}"
                        class="mt-1 w-full rounded border p-2">
                    @error('starts_at')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Ends At</label>
                    <input name="ends_at" type="datetime-local" value="{{ old('ends_at') }}"
                        class="mt-1 w-full rounded border p-2">
                    @error('ends_at')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <input id="is_featured" name="is_featured" type="checkbox" value="1" @checked(old('is_featured'))>
                <label for="is_featured" class="text-sm font-medium">Featured</label>
            </div>

            <div>
                <label class="block text-sm font-medium">Short Description</label>
                <textarea name="excerpt" rows="2" class="mt-1 w-full rounded border p-2" placeholder="Shown on cards…">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Full Description</label>
                <textarea name="body" rows="6" class="mt-1 w-full rounded border p-2">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Cover Image</label>
                <input name="cover" type="file" accept="image/*" class="mt-1">
                @error('cover')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button class="px-5 py-2 rounded bg-pink-600 text-white hover:bg-pink-700">Save Program</button>
        </form>
    </div>
@endsection
