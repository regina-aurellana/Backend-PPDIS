<?php

namespace App\Providers\Communication;

use App\Http\Services\Communication\Appointment;
use App\Http\Services\Communication\Meeting;
use App\Http\Services\Communication\Project;
use App\Http\Services\Communication\Report;
use Illuminate\Support\ServiceProvider;

class CommunicationServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        
        $this->app->bind('1', Appointment::class);
        $this->app->bind('2', Meeting::class);
        $this->app->bind('3', Project::class);
        $this->app->bind('4', Report::class);

    }
}
