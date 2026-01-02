@extends('admin.layouts.admin')

@section('title', $webinar->exists ? 'Edit Webinar' : 'Create Webinar')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#213430]">
                {{ $webinar->exists ? 'Edit Webinar' : 'Create Webinar' }}
            </h1>
            <p class="text-[#6C5B68]">
                Set up webinar details patients and sponsors will see.
            </p>
        </div>
        <a href="{{ route('admin.webinars.index') }}" class="text-pink hover:text-pink-dark font-medium">Back to list</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#F1E4EC] p-6">
        <form method="POST"
              action="{{ $webinar->exists ? route('admin.webinars.update', $webinar) : route('admin.webinars.store') }}">
            @csrf
            @if($webinar->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $webinar->title) }}"
                           class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink"
                           required>
                    @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Presenter</label>
                    <input type="text" name="presenter" value="{{ old('presenter', $webinar->presenter) }}"
                           class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink">
                    @error('presenter')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Scheduled At</label>
                    <input type="datetime-local" name="scheduled_at"
                           value="{{ old('scheduled_at', $webinar->scheduled_at ? $webinar->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink"
                           required>
                    @error('scheduled_at')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', $webinar->duration_minutes) }}"
                           min="1" max="1440"
                           class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink">
                    @error('duration_minutes')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Join URL</label>
                    <input type="url" name="join_url" value="{{ old('join_url', $webinar->join_url) }}"
                           class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink"
                           placeholder="https://example.com/webinar-link">
                    @error('join_url')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Max Attendees</label>
                    <input type="number" name="max_attendees" min="1"
                           value="{{ old('max_attendees', $webinar->max_attendees) }}"
                           class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink"
                           placeholder="Leave blank for unlimited">
                    @error('max_attendees')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink">
                        @foreach(['upcoming' => 'Upcoming', 'live' => 'Live', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $webinar->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#6C5B68] mb-1">Audience</label>
                    <select name="audience"
                            class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink">
                        @foreach(['both' => 'Patients & Sponsors', 'patient' => 'Patients only', 'sponsor' => 'Sponsors only'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('audience', $webinar->audience ?? 'both') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('audience')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-[#6C5B68] mb-1">Description</label>
                <textarea name="description" rows="5"
                          class="w-full border border-[#E6D7E7] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink">{{ old('description', $webinar->description) }}</textarea>
                @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.webinars.index') }}"
                   class="px-4 py-2 rounded-lg border border-[#E6D7E7] text-[#213430]">Cancel</a>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-pink text-white font-medium shadow hover:bg-pink-dark transition">
                    {{ $webinar->exists ? 'Update Webinar' : 'Create Webinar' }}
                </button>
            </div>
        </form>
    </div>
@endsection
