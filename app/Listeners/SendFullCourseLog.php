<?php

namespace App\Listeners;

use App\Events\CourseMaxCapacity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendFullCourseLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CourseMaxCapacity $event): void
    {
        $course=$event->course;
        LOG::info('Course max capacity reached.',[
            'course_id' => $course->id,
            'course_name' => $course->name,
            'enrolled_students' => $course->students()->count(),
        ]);
    }
}
