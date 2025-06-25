<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('app:generate-report')->everyTwoMinutes();

Artisan::command('app:generate-report',function(){
    $students=Student::where("created_at", ">=", now()->subDay())->get();
    $report=$students->map(function(Student $student){
        return [
            "id" => $student->id,
            "name" => $student->name,
            "email" => $student->email
        ];
    });
    Log::info('Report: Students registered in the last day:', $report->toArray());
})->purpose('Display students registered in the last day')->everyMinute();;
