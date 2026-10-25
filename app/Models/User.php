<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'password',
    'role',
    'sex',
    'address',
    'contact_number',
    'company_name',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Determine if the user is a worker.
     */
    public function isWorker(): bool
    {
        return $this->role === 'worker';
    }

    /**
     * Determine if the user is an employer.
     */
    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    /**
     * Get user display name (handles company, worker names, or default name).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isEmployer() && ! empty($this->company_name)) {
            return $this->company_name;
        }

        if (! empty($this->first_name) || ! empty($this->last_name)) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        return $this->name ?? 'User';
    }

    /**
     * Job posts created by the user (as employer).
     */
    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class, 'employer_id');
    }

    /**
     * Applications submitted by the user (as worker).
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'worker_id');
    }

    /**
     * Determine if the worker currently holds an active hired job.
     */
    public function hasActiveJob(): bool
    {
        return $this->applications()
            ->whereIn('status', ['hired', 'leave_requested'])
            ->exists();
    }

    /**
     * Retrieve the active hired job application for this worker.
     */
    public function activeJobApplication(): ?JobApplication
    {
        return $this->applications()
            ->whereIn('status', ['hired', 'leave_requested'])
            ->with(['jobPost.employer'])
            ->first();
    }

    /**
     * Messages sent by this user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Messages received by this user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
