<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Video extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'storage_path',
        'external_url',
        'duration_sec',
        'is_external_secured',
        'external_signed_until',
        'external_security_checked_at',
        'is_active',
    ];

    protected $casts = [
        'duration_sec' => 'integer',
        'is_external_secured' => 'boolean',
        'is_active' => 'boolean',
        'external_signed_until' => 'datetime',
        'external_security_checked_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = Str::slug($video->title);
            }
        });
    }

    public function accessRequests(): HasMany
    {
        return $this->hasMany(AccessRequests::class, 'video_id');
    }

    public function videoAccesses(): HasMany
    {
        return $this->hasMany(VideoAccess::class, 'video_id');
    }

    public function isExternal(): bool
    {
        return !empty($this->external_url);
    }

    public function getVideoSourceAttribute(): string
    {
        return $this->isExternal() ? $this->external_url : asset('storage/' . $this->storage_path);
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_sec) return '0:00';
        $minutes = floor($this->duration_sec / 60);
        $seconds = $this->duration_sec % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
