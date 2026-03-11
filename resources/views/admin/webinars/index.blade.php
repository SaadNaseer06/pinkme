@extends('admin.layouts.admin')

@section('title', 'Webinars')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#213430]">Webinars</h1>
            <p class="text-[#6C5B68]">Create and manage webinars patients and sponsors can join.</p>
        </div>
        <a href="{{ route('admin.webinars.create') }}"
           class="px-4 py-2 rounded-lg bg-pink text-white font-medium shadow hover:bg-pink-dark transition">
            + New Webinar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#F1E4EC]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#F1E4EC]">
                <thead class="bg-[#F9F4F8] text-[#6C5B68]">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Title</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Schedule</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Audience</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Attendees</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1E4EC] text-sm text-[#213430]">
                    @forelse ($webinars as $webinar)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-[#213430]">{{ $webinar->title }}</div>
                                <div class="text-xs text-[#6C5B68]">
                                    Presenter: {{ $webinar->presenter ?? 'TBD' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $webinar->date_label ?? 'Date TBD' }}</div>
                                <div class="text-xs text-[#6C5B68]">{{ $webinar->time_label ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-[#6C5B68]">{{ $webinar->audience_label }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($webinar->status === 'cancelled') bg-red-100 text-red-600
                                    @elseif($webinar->status === 'completed' || ($webinar->scheduled_at && $webinar->scheduled_at->isPast()))
                                        bg-green-100 text-green-700
                                    @else
                                        bg-pink-light text-pink
                                    @endif">
                                    {{ $webinar->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">{{ $webinar->attendee_count ?? 0 }}</div>
                                <div class="text-xs text-[#6C5B68] space-x-2">
                                    @if(in_array($webinar->audience, ['both', 'sponsor']))
                                        <span>Sponsors: {{ $webinar->sponsor_count ?? 0 }}</span>
                                    @endif
                                    @if(in_array($webinar->audience, ['both', 'patient']))
                                        <span>Patients: {{ $webinar->patient_count ?? 0 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 space-x-3">
                                <a href="{{ route('admin.webinars.show', $webinar) }}"
                                   class="text-pink hover:text-pink-dark font-medium">View</a>
                                <a href="{{ route('admin.webinars.edit', $webinar) }}"
                                   class="text-[#213430] hover:text-pink font-medium">Edit</a>
                                <form action="{{ route('admin.webinars.destroy', $webinar) }}" method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete this webinar?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-[#6C5B68]">
                                No webinars have been created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $webinars->links() }}
        </div>
    </div>
@endsection
