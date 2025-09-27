<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProgramController as ProgramController;
use Illuminate\Http\Request;

class EnrollProgramController extends Controller
{
    protected ProgramController $inner;

    public function __construct(ProgramController $inner)
    {
        $this->inner = $inner;
    }

    public function create()
    {
        return $this->inner->create();
    }

    public function store(Request $request)
    {
        return $this->inner->store($request);
    }
}
