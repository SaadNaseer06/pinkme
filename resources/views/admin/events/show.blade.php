@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        @if (session('success'))
            <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif

        <h1 class="text-2xl font-semibold">{{ $event->title }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <p><span class="font-medium">When:</span> {{ optional($event->date)->format('Y-m-d H:i') }}</p>
                @if ($event->location)
                    <p><span class="font-medium">Location:</span> {{ $event->location }}</p>
                @endif
                @if ($event->description)
                    <div>
                        <p class="font-medium">Description</p>
                        <p class="whitespace-pre-wrap">{{ $event->description }}</p>
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                <div class="p-3 rounded border">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold">Sponsorships</h2>
                        <span class="text-sm">Total:
                            <strong>{{ number_format($event->sponsorship_total, 2) }}</strong></span>
                    </div>
                    <table class="w-full mt-3 text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-1">Sponsor</th>
                                <th class="py-1">Amount</th>
                                <th class="py-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->sponsorships as $row)
                                <tr class="border-b">
                                    <td class="py-1">{{ $row->sponsor->name ?? $row->sponsor->email }} (ID:
                                        {{ $row->sponsor_id }})</td>
                                    <td class="py-1">{{ number_format($row->amount, 2) }}</td>
                                    <td class="py-1 text-right">
                                        <form method="POST"
                                            action="{{ route('events.sponsorships.destroy', [$event, $row]) }}"
                                            onsubmit="return confirm('Remove this sponsorship?')">
                                            @csrf @method('DELETE')
                                            <button class="px-2 py-1 rounded border">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-3 text-gray-500">No sponsorships yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 rounded border">
                    <h3 class="font-semibold mb-2">Add Sponsorship</h3>
                    <form method="POST" action="{{ route('events.sponsorships.store', $event) }}"
                        class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Sponsor</label>
                            <select name="sponsor_id" class="mt-1 w-full rounded border p-2">
                                @foreach ($sponsors as $s)
                                    <option value="{{ $s->id }}">{{ $s->name ?? $s->email }} (ID:
                                        {{ $s->id }})</option>
                                @endforeach
                            </select>
                            @error('sponsor_id')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Amount</label>
                            <input name="amount" type="number" step="0.01" min="0.01"
                                class="mt-1 w-full rounded border p-2">
                            @error('amount')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <button class="px-4 py-2 rounded bg-pink-600 text-white hover:bg-pink-700">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
