<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Availability extends Model
{
    use HasFactory;

    protected $appends = ['status'];

    protected $fillable = [
        'date',
        'hour',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Slots still available for scheduling
                if ($this->is_available) {
                    return [
                        'label'      => 'Disponível',
                        'severity'   => 'success',
                        'is_blocked' => false
                    ];
                }

                // Fetches the real/active appointment (ignores canceled ones)
                $activeAppointment = $this->appointments->where('status', '!=', 'canceled')->first();

                // If there is no active appointment, it means the time has passed and is blocked
                if (! $activeAppointment) {
                    return [
                        'label'      => 'Expirado',
                        'severity'   => 'secondary',
                        'is_blocked' => true
                    ];
                }

                return match ($activeAppointment->status) {
                    // The "completed" status is the result of a confirmed appointment that passed its time (realized)
                    'completed' => [
                        'label'      => 'Realizado',
                        'severity'   => 'contrast',
                        'is_blocked' => true
                    ],
                    // The "confirmed" status is the result of a confirmed appointment that hasn't happened yet
                    'confirmed' => [
                        'label'      => 'Confirmado',
                        'severity'   => 'info',
                        'is_blocked' => false
                    ],
                    // The "pending" status is the result of an appointment waiting for confirmation
                    'pending' => [
                        'label'      => 'Pendente',
                        'severity'   => 'warn',
                        'is_blocked' => false
                    ],
                    // Slot unavailable even without an active appointment
                    default => [
                        'label'      => 'Indisponível',
                        'severity'   => 'secondary',
                        'is_blocked' => false
                    ],
                };
            }
        );
    }
}
