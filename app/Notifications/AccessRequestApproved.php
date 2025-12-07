<?php

namespace App\Notifications;

use App\Models\AccessRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $accessRequest;
    protected $approvedMinutes;
    protected $graceMinutes;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccessRequests $accessRequest, int $approvedMinutes, int $graceMinutes = 0)
    {
        $this->accessRequest = $accessRequest;
        $this->approvedMinutes = $approvedMinutes;
        $this->graceMinutes = $graceMinutes;
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
        $videoAccess = $this->accessRequest->videoAccess;

        $message = (new MailMessage)
            ->subject('Request Akses Video Disetujui')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kabar baik! Request akses video Anda telah **disetujui** oleh admin.')
            ->line('**Detail Akses:**')
            ->line('Video: ' . $video->title)
            ->line('Durasi akses: ' . $this->approvedMinutes . ' menit');

        if ($this->graceMinutes > 0) {
            $message->line('Masa tenggang: ' . $this->graceMinutes . ' menit');
        }

        if ($videoAccess) {
            $message->line('Akses dimulai: ' . $videoAccess->start_at->format('d M Y H:i'))
                ->line('Akses berakhir: ' . $videoAccess->end_at->format('d M Y H:i'));
        }

        if ($this->accessRequest->reason) {
            $message->line('Catatan admin: ' . $this->accessRequest->reason);
        }

        $message->action('Tonton Video Sekarang', url('/customer/videos/' . $video->id))
            ->line('Selamat menikmati video!');

        return $message;
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
            'status' => 'approved',
            'approved_minutes' => $this->approvedMinutes,
            'grace_minutes' => $this->graceMinutes,
        ];
    }
}
