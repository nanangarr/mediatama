<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AccessRequests extends Model
{
    protected $fillable = [
        'customer_id',
        'video_id',
        'requested_minutes',
        'status',
        'reviewer_id',
        'reviewed_at',
        'reason',
        'requested_at',
    ];

    protected $casts = [
        'requested_minutes' => 'integer',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function videoAccess(): HasOne
    {
        return $this->hasOne(VideoAccess::class, 'request_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
