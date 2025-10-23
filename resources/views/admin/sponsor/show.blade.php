@extends('admin.layouts.admin')

@section('title', 'Sponsor Profile')

@section('content')
    <div class="max-w-5xl mx-auto">
        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-[#F3E8EF] rounded-xl p-6 mb-6">
            <h1 class="text-2xl font-semibold text-[#213430] mb-4">Sponsor Profile</h1>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-[#213430] mb-2">Account</h3>
                    <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Email:</span> {{ $sponsor->email }}</p>
                    <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Role:</span> {{ $sponsor->role->name ?? '—' }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-[#213430] mb-2">Profile</h3>
                    <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Name:</span> {{ $sponsor->profile->full_name ?? trim(($sponsor->profile->first_name ?? '').' '.($sponsor->profile->last_name ?? '')) }}</p>
                    <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Phone:</span> {{ $sponsor->profile->phone ?? '—' }}</p>
                    <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Status:</span> {{ (($sponsor->profile->status ?? 0) == 1) ? 'Active' : 'Inactive' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[#F3E8EF] rounded-xl p-6">
            <h2 class="text-xl font-semibold text-[#213430] mb-4">Company Details</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Company Name:</span> {{ $sponsor->sponsorDetail->company_name ?? '—' }}</p>
                <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Company Email:</span> {{ $sponsor->sponsorDetail->company_email ?? '—' }}</p>
                <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Company Phone:</span> {{ $sponsor->sponsorDetail->company_phone ?? '—' }}</p>
                <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Registration #:</span> {{ $sponsor->sponsorDetail->registration_number ?? '—' }}</p>
                <p class="text-sm text-[#6C5B68]"><span class="font-semibold">Company Type:</span> {{ $sponsor->sponsorDetail->company_type ?? '—' }}</p>
            </div>
            <div class="mt-6 flex gap-3">
                <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="px-5 py-2 bg-white text-[#213430] rounded-md border border-[#DCCFD8] hover:bg-[#F6EDF5]">Edit</a>
                <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" onsubmit="return confirm('Remove this sponsor? If deletion fails due to dependencies, the sponsor will be deactivated.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2 bg-red-50 text-red-700 rounded-md border border-red-200 hover:bg-red-100">Remove</button>
                </form>
                <a href="{{ route('admin.sponsors') }}" class="px-5 py-2 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md">Back</a>
            </div>
        </div>
    </div>
@endsection
