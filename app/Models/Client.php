<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $full_name
 * @property string $phone
 * @property string|null $email
 * @property string|null $cpf
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $postal_code
 * @property string|null $street
 * @property string|null $number
 * @property string|null $complement
 * @property string|null $neighborhood
 * @property string|null $city
 * @property string|null $state
 * @property string|null $notes
 * @property string|null $otp_code
 * @property \Illuminate\Support\Carbon|null $otp_expires_at
 */
#[Fillable([
    'full_name',
    'phone',
    'email',
    'cpf',
    'birth_date',
    'postal_code',
    'street',
    'number',
    'complement',
    'neighborhood',
    'city',
    'state',
    'notes',
    'otp_code',
    'otp_expires_at'
])]
#[Hidden(['remember_token', 'otp_code'])]
class Client extends Authenticatable
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the password for the user.
     * Required by Authenticatable interface even for passwordless/OTP flows.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return '';
    }

    /**
     * Get the appointments associated with the client.
     *
     * @return HasMany
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
