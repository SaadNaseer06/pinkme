@extends('admin.layouts.admin')


@section('title', 'Create Program')

@section('content')
    <div class="flex-1 flex flex-col min-h-screen bg-[#FBF7FA]">
        <main class="flex-1 pb-8">
            <div class="max-w-7xl mx-auto px-4 py-8">
                @if (session('success'))
                    <div class="p-4 rounded-lg bg-[#E8F5E9] text-[#1B5E20] mb-6">{{ session('success') }}</div>
                @endif

                <h1 class="text-2xl font-semibold text-[#213430] mb-8">Add New Support Program</h1>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form method="POST" action="{{ route('programs.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-1">Program Title <span
                                    class="text-[#DB69A2]">*</span></label>
                            <input name="title" type="text" value="{{ old('title') }}"
                                class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50"
                                required>
                            @error('title')
                                <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-1">Program Description <span
                                    class="text-[#DB69A2]">*</span></label>
                            <textarea name="description" rows="5"
                                class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50"
                                required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Program Date <span
                                        class="text-[#DB69A2]">*</span></label>
                                <input name="event_date" type="date" value="{{ old('event_date') }}"
                                    class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50"
                                    required>
                                @error('event_date')
                                    <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Program Time <span
                                        class="text-[#DB69A2]">*</span></label>
                                <input name="event_time" type="time" value="{{ old('event_time') }}"
                                    class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50"
                                    required>
                                @error('event_time')
                                    <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Program Status <span
                                        class="text-[#DB69A2]">*</span></label>
                                <select name="status"
                                    class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50 bg-white"
                                    required>
                                    <option value="upcoming" @selected(old('status') === 'upcoming')>Upcoming</option>
                                    <option value="ongoing" @selected(old('status') === 'ongoing')>Ongoing</option>
                                    <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-1">Program Banner</label>
                            <div class="mt-1 flex items-center">
                                <div class="relative">
                                    <input type="file" name="banner" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                    <button type="button"
                                        class="px-4 py-2 bg-[#F3E8EF] text-[#91848C] rounded-lg border border-[#DCCFD8] hover:bg-[#F6EDF5] transition-colors">
                                        Choose File
                                    </button>
                                </div>
                                <span id="file-name" class="ml-3 text-sm text-[#91848C]">No file chosen</span>
                            </div>
                            @error('banner')
                                <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-6">
                            <button type="submit"
                                class="px-6 py-3 bg-[#DB69A2] text-white text-sm font-medium rounded-lg hover:bg-[#c25891] transition-colors duration-200">
                                Create Program
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
