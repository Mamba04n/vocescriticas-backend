<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCommented extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $commenter, public Post $post) {}

    public function via(object $notifiable): array
    {
        // Notificamos directo a la base de datos (campanita del sistema)
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'message' => "{$this->commenter->name} ha comentado en tu investigación.",
            'post_id' => $this->post->id,
            'user_id' => $this->commenter->id,
            'avatar_url' => $this->commenter->avatar_url,
        ];
    }
}