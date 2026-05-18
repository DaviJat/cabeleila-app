<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Appointment extends Model
{
    use HasFactory;

    protected $appends = ['status_badge'];

    protected $fillable = ['client_id', 'availability_id', 'status', 'notes'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

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
