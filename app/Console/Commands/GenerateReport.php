<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class GenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $students=Student::where("created_at", now()->subDay())->get();
        $report= $students->map(function(Student $student){
            return [ 'id' => $student->id, 'name' => $student->name, 'email' => $student->email];
        });

        foreach($report as $student){
            LOG::info($student);
        }
    }
}
