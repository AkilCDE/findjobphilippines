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

    /**
     * Get deterministic, URL-safe QR verification token for this user.
     */
    public function getQrTokenAttribute(): string
    {
        $sig = substr(hash_hmac('sha256', "findjobph_worker_{$this->id}", (string) config('app.key', 'findjobph_secret')), 0, 16);

        return rtrim(strtr(base64_encode("{$this->id}:{$sig}"), '+/', '-_'), '=');
    }

    /**
     * Locate a worker by their QR verification token.
     */
    public static function findByQrToken(?string $token): ?User
    {
        if (empty($token)) {
            return null;
        }

        $decoded = base64_decode(strtr($token, '-_', '+/'));
        if (! $decoded || ! str_contains($decoded, ':')) {
            return null;
        }

        [$id, $sig] = explode(':', $decoded, 2);
        $user = static::find($id);
        if (! $user || ! $user->isWorker()) {
            return null;
        }

        $expectedSig = substr(hash_hmac('sha256', "findjobph_worker_{$user->id}", (string) config('app.key', 'findjobph_secret')), 0, 16);
        if (! hash_equals($expectedSig, $sig)) {
            return null;
        }

        return $user;
    }

    /**
     * Generate live QR profile data for worker status verification.
     *
     * @return array<string, mixed>
     */
    public function getQrProfileData(): array
    {
        $activeJob = $this->activeJobApplication();
        $status = 'pending';
        $workTitle = null;
        $employerName = null;
        $hiredAt = null;
        $endedAt = null;
        $terminationReason = null;

        if ($activeJob && $activeJob->jobPost) {
            $status = 'hired';
            $workTitle = $activeJob->jobPost->title;
            $employerName = $activeJob->jobPost->employer?->display_name;
            $hiredAt = $activeJob->hired_at?->format('M d, Y') ?? $activeJob->created_at?->format('M d, Y');
        } else {
            // Find most recent fired record
            $latestFired = $this->applications()
                ->where('status', 'fired')
                ->latest('ended_at')
                ->latest('updated_at')
                ->with(['jobPost.employer'])
                ->first();

            $latestResigned = $this->applications()
                ->where('status', 'resigned')
                ->latest('ended_at')
                ->latest('updated_at')
                ->first();

            if ($latestFired && (! $latestResigned || $latestFired->updated_at >= $latestResigned->updated_at)) {
                $status = 'fired';
                $workTitle = $latestFired->jobPost?->title;
                $employerName = $latestFired->jobPost?->employer?->display_name;
                $endedAt = $latestFired->ended_at?->format('M d, Y') ?? $latestFired->updated_at?->format('M d, Y');
                $terminationReason = $latestFired->termination_reason;
            } else {
                $status = 'pending';
                $workTitle = null;
                $employerName = null;
            }
        }

        return [
            'worker_id' => $this->id,
            'name' => $this->display_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'sex' => $this->sex,
            'address' => $this->address,
            'status' => $status,
            'status_label' => match ($status) {
                'hired' => 'Hired',
                'fired' => 'Fired',
                default => 'Pending',
            },
            'work_title' => $status === 'pending' ? null : $workTitle,
            'employer_name' => $status === 'pending' ? null : $employerName,
            'hired_at' => $hiredAt,
            'ended_at' => $endedAt,
            'termination_reason' => $terminationReason,
            'qr_token' => $this->qr_token,
            'verify_url' => route('worker.qr.verify', ['token' => $this->qr_token]),
        ];
    }

    /**
     * Get JSON string encoded directly into the worker's QR code.
     */
    public function getQrPayloadString(): string
    {
        $data = $this->getQrProfileData();

        $payload = [
            'system' => 'Findjobph Worker Pass',
            'worker_id' => $data['worker_id'],
            'name' => $data['name'],
            'status' => $data['status'],
            'contact' => $data['contact_number'],
            'verify_url' => $data['verify_url'],
            'token' => $data['qr_token'],
        ];

        if ($data['status'] !== 'pending') {
            $payload['work'] = $data['work_title'];
            $payload['employer'] = $data['employer_name'];
        }

        return json_encode($payload, JSON_UNESCAPED_SLASHES);
    }
}
