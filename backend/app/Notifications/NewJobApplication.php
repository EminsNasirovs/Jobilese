<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewJobApplication extends Notification
{
    use Queueable;

    protected $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    // store in database
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'application_id' => $this->application->id,
            'vacancy_id'     => $this->application->vacancy_id,
            'applicant_name' => $this->application->user->firstname . ' ' . $this->application->user->lastname,
            'message'        => 'New application received.',
        ];
    }
}
