<?php

namespace App\Notifications;

use App\Models\AccessRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $accessRequest;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccessRequests $accessRequest, string $reason)
    {
        $this->accessRequest = $accessRequest;
        $this->reason = $reason;
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

        return (new MailMessage)
            ->subject('Request Akses Video Ditolak')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Mohon maaf, request akses video Anda telah **ditolak** oleh admin.')
            ->line('**Detail Request:**')
            ->line('Video: ' . $video->title)
            ->line('Alasan penolakan: ' . $this->reason)
            ->line('Jika Anda memiliki pertanyaan, silakan hubungi admin.')
            ->action('Lihat Video Lainnya', url('/customer/videos'))
            ->line('Terima kasih atas pengertian Anda.');
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
            'status' => 'rejected',
            'reason' => $this->reason,
        ];
    }
}
