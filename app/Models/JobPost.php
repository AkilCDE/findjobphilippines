<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employer_id',
        'title',
        'category',
        'job_type',
        'salary_range',
        'description',
        'requirements',
        'workers_needed',
        'workers_hired',
        'location_address',
        'latitude',
        'longitude',
        'status',
        'archived_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'workers_needed' => 'integer',
            'workers_hired' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * The employer who published this job post.
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    /**
     * All applications for this post.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_post_id');
    }

    /**
     * Applications that have been hired.
     */
    public function hiredApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_post_id')->where('status', 'hired');
    }

    /**
     * Check if job is locked (capacity reached or manually locked).
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked' || $this->workers_hired >= $this->workers_needed;
    }

    /**
     * Check if job is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived' || ! is_null($this->archived_at);
    }

    /**
     * Check if job is open for new applications.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->workers_hired < $this->workers_needed && is_null($this->archived_at);
    }

    /**
     * Remaining worker slots available.
     */
    public function getRemainingSlotsAttribute(): int
    {
        return max(0, $this->workers_needed - $this->workers_hired);
    }

    /**
     * Capacity percentage hired (0 - 100).
     */
    public function getCapacityPercentageAttribute(): int
    {
        if ($this->workers_needed <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->workers_hired / $this->workers_needed) * 100));
    }

    /**
     * Synchronize and automatically update locked status based on worker count.
     */
    public function syncLockStatus(): void
    {
        if ($this->isArchived()) {
            return;
        }

        if ($this->workers_hired >= $this->workers_needed) {
            $this->status = 'locked';
        } elseif ($this->status === 'locked' && $this->workers_hired < $this->workers_needed) {
            $this->status = 'open';
        }

        $this->saveQuietly();
    }

    /**
     * Scope posts visible on worker job feed and map (not archived).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', 'archived')
            ->whereNull('archived_at');
    }
}
