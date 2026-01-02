<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyUserNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Traits\Auditable;
use DateTimeInterface;
use Carbon\Carbon;
use Hash;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, Auditable, HasFactory;

    public $table = 'users';

    public static $searchable = [
        'name',
        'email',
    ];

    protected $hidden = [
        'remember_token', 'two_factor_code',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'verified',
        'verified_at',
        'verification_token',
        'two_factor',
        'two_factor_code',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'two_factor_expires_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function generateTwoFactorCode()
    {
        $this->timestamps            = false;
        $this->two_factor_code       = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps            = false;
        $this->two_factor_code       = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $user) {
            if (auth()->check()) {
                $user->verified    = 1;
                $user->verified_at = Carbon::now()->format(config('panel.date_format') . ' ' . config('panel.time_format'));
                $user->save();
            } elseif (! $user->verification_token) {
                $token     = Str::random(64);
                $usedToken = self::where('verification_token', $token)->first();

                while ($usedToken) {
                    $token     = Str::random(64);
                    $usedToken = self::where('verification_token', $token)->first();
                }

                $user->verification_token = $token;
                $user->save();

                $registrationRole = config('panel.registration_default_role');
                if (! $user->roles()->get()->contains($registrationRole)) {
                    $user->roles()->attach($registrationRole);
                }

                $user->notify(new VerifyUserNotification($user));
            }
        });
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        } catch (\Exception $e) {
            // Fallback to Carbon::parse() if format doesn't match exactly
            return Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        }
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        if (!$value) {
            $this->attributes['email_verified_at'] = null;
            return;
        }

        // If it's already a Carbon instance, format it directly
        if ($value instanceof Carbon) {
            $this->attributes['email_verified_at'] = $value->format('Y-m-d H:i:s');
            return;
        }

        // If it's a string in the custom format, parse it
        if (is_string($value)) {
            try {
                $this->attributes['email_verified_at'] = Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, try to parse as standard format or use Carbon::parse()
                try {
                    $this->attributes['email_verified_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
                } catch (\Exception $e2) {
                    $this->attributes['email_verified_at'] = null;
                }
            }
            return;
        }

        $this->attributes['email_verified_at'] = null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getVerifiedAtAttribute($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        } catch (\Exception $e) {
            // Fallback to Carbon::parse() if format doesn't match exactly
            return Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        }
    }

    public function setVerifiedAtAttribute($value)
    {
        if (!$value) {
            $this->attributes['verified_at'] = null;
            return;
        }

        // If it's already a Carbon instance, format it directly
        if ($value instanceof Carbon) {
            $this->attributes['verified_at'] = $value->format('Y-m-d H:i:s');
            return;
        }

        // If it's a string in the custom format, parse it
        if (is_string($value)) {
            try {
                $this->attributes['verified_at'] = Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, try to parse as standard format or use Carbon::parse()
                try {
                    $this->attributes['verified_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
                } catch (\Exception $e2) {
                    $this->attributes['verified_at'] = null;
                }
            }
            return;
        }

        $this->attributes['verified_at'] = null;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getTwoFactorExpiresAtAttribute($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        } catch (\Exception $e) {
            // Fallback to Carbon::parse() if format doesn't match exactly
            return Carbon::parse($value)->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        }
    }

    public function setTwoFactorExpiresAtAttribute($value)
    {
        if (!$value) {
            $this->attributes['two_factor_expires_at'] = null;
            return;
        }

        // If it's already a Carbon instance, format it directly
        if ($value instanceof Carbon) {
            $this->attributes['two_factor_expires_at'] = $value->format('Y-m-d H:i:s');
            return;
        }

        // If it's a string in the custom format, parse it
        if (is_string($value)) {
            try {
                $this->attributes['two_factor_expires_at'] = Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, try to parse as standard format or use Carbon::parse()
                try {
                    $this->attributes['two_factor_expires_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
                } catch (\Exception $e2) {
                    $this->attributes['two_factor_expires_at'] = null;
                }
            }
            return;
        }

        $this->attributes['two_factor_expires_at'] = null;
    }
}
