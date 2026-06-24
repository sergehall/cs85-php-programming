<?php

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class Module1Assignment1AController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.assignments.module1.assignment1a');
    }
}
