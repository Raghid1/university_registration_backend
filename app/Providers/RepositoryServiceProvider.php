<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\CourseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Repositories\StudentRepository;
use App\Services\CourseService; // Import the service classes
use App\Services\Interfaces\CourseServiceInterface;
use App\Services\Interfaces\StudentServiceInterface;
use App\Services\StudentService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);

        // Service Bindings
        $this->app->bind(StudentServiceInterface::class, StudentService::class);
        $this->app->bind(CourseServiceInterface::class, CourseService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}