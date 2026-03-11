@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Applications</h1>

    @if($applications->isEmpty())
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">You haven't submitted any applications yet.</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($applications as $application)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-semibold mb-2">{{ $application->title }}</h2>
                            <p class="text-gray-600 mb-4">{{ $application->description }}</p>
                            
                            <div class="text-sm text-gray-500">
                                <p>Program: {{ $application->program->title }}</p>
                                <p>Submitted: {{ $application->submission_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                @if($application->status === 'approved')
                                    bg-green-100 text-green-800
                                @elseif($application->status === 'rejected')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif
                            ">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
