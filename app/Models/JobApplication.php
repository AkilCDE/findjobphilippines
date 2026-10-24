<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_post_id',
        'worker_id',
        'status',
        'cover_letter',
        'submitted_requirements',
        'attachment_path',
        'hired_at',
        'leave_reason',
        'leave_requested_at',
        'termination_reason',
        'ended_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hired_at' => 'datetime',
            'leave_requested_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    /**
     * The job post applied for.
     */
    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    /**
     * The worker who applied.
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    /**
     * Check if application is hired.
     */
    public function isHired(): bool
    {
        return $this->status === 'hired';
    }

    /**
     * Check if worker requested to leave/quit.
     */
    public function isLeaveRequested(): bool
    {
        return $this->status === 'leave_requested';
    }

    /**
     * Check if worker has an active engagement (either hired or pending leave confirmation).
     */
    public function isActiveEngagement(): bool
    {
        return in_array($this->status, ['hired', 'leave_requested']);
    }

    /**
     * Check if application has been terminated / fired by employer.
     */
    public function isFired(): bool
    {
        return $this->status === 'fired';
    }

    /**
     * Check if worker has left / resigned after employer confirmation.
     */
    public function isResigned(): bool
    {
        return $this->status === 'resigned';
    }

    /**
     * Check if application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
