@extends('admin.layouts.admin')

@section('title', 'Create Event')

@section('content')
    <div class="flex-1 flex flex-col min-h-screen bg-[#FBF7FA]">
        <main class="flex-1 pb-8">
            <div class="max-w-7xl mx-auto px-4 py-8">
                @if (session('success'))
                    <div class="p-4 rounded-lg bg-[#E8F5E9] text-[#1B5E20] mb-6">{{ session('success') }}</div>
                @endif

                <h1 class="text-2xl font-semibold text-[#213430] mb-8">Add New Event</h1>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form method="POST" action="{{ route('events.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-1">Event Title <span
                                    class="text-[#DB69A2]">*</span></label>
                            <input name="title" type="text" value="{{ old('title') }}"
                                class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50"
                                required>
                            @error('title')
                                <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-1">Event Description</label>
                            <textarea name="description" rows="4"
                                class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Date & Time <span
                                        class="text-[#DB69A2]">*</span></label>
                                {{-- events.date is a single DATETIME --}}
                                <input name="date" type="datetime-local" value="{{ old('date') }}"
                                    class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50"
                                    required>
                                @error('date')
                                    <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Event Location</label>
                                <input name="location" type="text" value="{{ old('location') }}"
                                    class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50">
                                @error('location')
                                    <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <fieldset class="border border-[#DCCFD8] rounded-lg p-6 bg-[#F9EEF6]">
                            <legend class="text-sm font-medium text-[#213430] px-2 bg-[#F9EEF6]">Initial Sponsorship
                                (optional)</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-1">Select Sponsor</label>
                                    <select name="sponsor_id"
                                        class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50 bg-white">
                                        <option value="">— Select Sponsor —</option>
                                        @foreach ($sponsors as $s)
                                            <option value="{{ $s->id }}" @selected(old('sponsor_id') == $s->id)>
                                                {{ $s->name ?? $s->email }}</option>
                                        @endforeach
                                    </select>
                                    @error('sponsor_id')
                                        <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-1">Sponsorship Amount
                                        ($)</label>
                                    <input name="amount" type="number" step="0.01" min="0.01"
                                        value="{{ old('amount') }}"
                                        class="mt-1 w-full rounded-lg border border-[#DCCFD8] p-3 focus:border-[#DB69A2] focus:ring focus:ring-[#DB69A2] focus:ring-opacity-50">
                                    @error('amount')
                                        <p class="mt-1 text-sm text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        <div class="flex justify-end pt-6">
                            <button type="submit"
                                class="px-6 py-3 bg-[#DB69A2] text-white text-sm font-medium rounded-lg hover:bg-[#c25891] transition-colors duration-200">
                                Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
