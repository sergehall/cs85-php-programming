<?php

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use App\Services\Modules\Module2B\CosmicCalendarBuilder;
use Illuminate\Contracts\View\View;

final class Module2BCosmicCalendarController extends Controller
{
    public function __invoke(CosmicCalendarBuilder $calendarBuilder): View
    {
        return view('pages.assignments.module2.cosmic-calendar', $calendarBuilder->build('Serge'));
    }
}
