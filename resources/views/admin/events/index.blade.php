@extends('admin.layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Events</h1>
        <a href="{{ route('events.create') }}" class="bg-pink-600 text-white px-4 py-2 rounded-md hover:bg-pink-700">
            Add New Event
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sponsorships</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($events as $event)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                            @if($event->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($event->description, 100) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $event->date->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $event->location ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>Total: ${{ number_format($event->sponsorship_total, 2) }}</div>
                            <div class="text-xs text-gray-400">{{ $event->sponsors->count() }} sponsor(s)</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('events.show', $event) }}" class="text-pink-600 hover:text-pink-900">View</a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline ml-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this event?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No events found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection