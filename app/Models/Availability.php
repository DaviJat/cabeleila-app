<?php

namespace App\Models;

use Database\Factories\AvailabilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date',            // Campo para o nome do serviço
    'hour',            // Campo para o horário do serviço
    'is_available'     // Campo para indicar se a disponibilidade está disponível
])]
class Availability extends Model
{
    /** @use HasFactory<AvailabilityFactory> */
    use HasFactory;

    protected $appends = ['status_badge'];

    /**
     *  Get the attributes that should be cast.
     *  @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    /**
     *  // Um horário de disponibilidade pode ter muitos agendamentos 
     * (possibilidade de mais de um profissional atender no mesmo horário e manter 
     * registro de agendamentos cancelados ou não comparecidos)
     */

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // Busca apenas as disponibilidades que estão marcadas como disponíveis
    public function scopeAvailable(Builder $query): void
    {
        $query->where('is_available', true);
    }

    // Busca apenas as disponibilidades que estão marcadas como indisponíveis
    public function scopeUnavailable(Builder $query): void
    {
        $query->where('is_available', false);
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Pega o primeiro agendamento que não seja cancelado (na collection já carregada em memória)
                $activeAppointment = $this->appointments->where('status', '!=', 'canceled')->first();

                if ($this->is_available) {
                    return ['label' => 'Disponível', 'severity' => 'success'];
                }

                if (! $activeAppointment) {
                    return ['label' => 'Indisponível', 'severity' => 'secondary'];
                }

                return match ($activeAppointment->status) {
                    'pending'   => ['label' => 'Pendente', 'severity' => 'warn'],
                    'confirmed' => ['label' => 'Confirmado', 'severity' => 'info'],
                    'completed' => ['label' => 'Finalizado', 'severity' => 'contrast'],
                    default     => ['label' => 'Indisponível', 'severity' => 'secondary'],
                };
            }
        );
    }
}
