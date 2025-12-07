<?php

namespace App\Notifications;

use App\Models\AccessRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $accessRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccessRequests $accessRequest)
    {
        $this->accessRequest = $accessRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $video = $this->accessRequest->video;
        $requestedMinutes = $this->accessRequest->requested_minutes ?? 'default';

        return (new MailMessage)
            ->subject('Request Akses Video - Menunggu Persetujuan')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Request akses video Anda telah berhasil diajukan.')
            ->line('**Detail Request:**')
            ->line('Video: ' . $video->title)
            ->line('Durasi yang diminta: ' . $requestedMinutes . ' menit')
            ->line('Status: Menunggu persetujuan admin')
            ->line('Kami akan mengirimkan notifikasi ketika request Anda telah ditinjau oleh admin.')
            ->line('Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'access_request_id' => $this->accessRequest->id,
            'video_id' => $this->accessRequest->video_id,
            'status' => $this->accessRequest->status,
        ];
    }
}
