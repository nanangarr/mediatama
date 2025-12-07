<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VideoAccess extends Model
{
    protected $fillable = [
        'request_id',
        'customer_id',
        'video_id',
        'approved_by',
        'approved_minutes',
        'grace_minutes',
        'start_at',
        'end_at',
        'status',
        'video_duration_sec_snapshot',
    ];

    protected $casts = [
        'approved_minutes' => 'integer',
        'grace_minutes' => 'integer',
        'video_duration_sec_snapshot' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AccessRequests::class, 'request_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();
        $endWithGrace = $this->end_at->copy()->addMinutes($this->grace_minutes);

        return $now->between($this->start_at, $endWithGrace);
    }

    public function hasExpired(): bool
    {
        $now = Carbon::now();
        $endWithGrace = $this->end_at->copy()->addMinutes($this->grace_minutes);

        return $now->greaterThan($endWithGrace);
    }

    public function getRemainingMinutesAttribute(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        $now = Carbon::now();
        $endWithGrace = $this->end_at->copy()->addMinutes($this->grace_minutes);

        return (int) $now->diffInMinutes($endWithGrace, false);
    }

    public function checkAndUpdateExpiration(): void
    {
        if ($this->status === 'active' && $this->hasExpired()) {
            $this->update(['status' => 'expired']);
        }
    }
}
