<?php

namespace App\Listeners;

use App\Events\StudentRegistration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEmailforNewRegistered implements ShouldQueue
{
    /**
     * Create the event listener.
     */

    protected $delay = 20;
    public function __construct(StudentRegistration $event)
    {
        
    }

    /**
     * Handle the event.
     */
    public function handle(StudentRegistration $event): void
    {
        $student= $event->student;
        Log::info('Welcome email sent to new student.', [
            'student_name' => $student->name,
            'student_email' => $student->email,
            'message' => "Welcome, {$student->name}! Thanks for registering."
        ]);
    }
}
