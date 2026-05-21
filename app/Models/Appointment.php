<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $client_id
 * @property int $availability_id
 * @property string $status
 * @property string|null $notes
 * @property array $status_badge
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Availability $availability
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 */
#[Fillable(['client_id', 'availability_id', 'status', 'notes'])]
class Appointment extends Model
{
    use HasFactory;

    protected $appends = ['status_badge'];

    /**
     * Get the client that owns the appointment.
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the availability slot associated with the appointment.
     *
     * @return BelongsTo
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    /**
     * Get the services associated with the appointment.
     *
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    /**
     * Interact with the appointment's status badge attribute.
     * Formats the status for UI rendering.
     *
     * @return Attribute
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                'pending'   => ['label' => 'Pendente',   'severity' => 'warn'],
                'confirmed' => ['label' => 'Confirmado', 'severity' => 'info'],
                'completed' => ['label' => 'Realizado',  'severity' => 'success'],
                'canceled'  => ['label' => 'Cancelado',  'severity' => 'danger'],
                default     => ['label' => $this->status, 'severity' => 'secondary'],
            }
        );
    }
}
