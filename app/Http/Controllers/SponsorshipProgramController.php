<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SponsorshipProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SponsorshipProgramController extends Controller
{
    public function create()
    {
        return view('admin.sponsorship_programs.create');
    }

    public function store(Request $r)
    {
        dd($r->all());
        $hasSlug = Schema::hasColumn('sponsorship_programs', 'slug');

        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'goal_amount' => ['required', 'integer', 'min:1'],
            'status'      => ['required', 'in:upcoming,active,completed,archived'],
            'starts_at'   => ['nullable', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_featured' => ['sometimes', 'boolean'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'body'        => ['nullable', 'string'],
            'cover'       => ['nullable', 'image', 'max:2048'],
        ];
        if ($hasSlug) $rules['slug'] = ['nullable', 'string', 'max:255', 'unique:sponsorship_programs,slug'];

        $data = $r->validate($rules);

        if ($hasSlug) $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        if ($r->hasFile('cover')) $data['cover_path'] = $r->file('cover')->store('programs', 'public');
        $data['is_featured'] = (bool) $r->boolean('is_featured');

        SponsorshipProgram::create($data);

        return back()->with('success', 'Sponsorship program created.');
    }
}
