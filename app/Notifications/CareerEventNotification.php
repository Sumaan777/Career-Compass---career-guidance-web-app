<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CareerEventNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $url;
    protected $icon;

    public function __construct($title, $message = null, $url = null, $icon = 'bi-bell')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
            'icon'    => $this->icon,
        ];
    }
}
