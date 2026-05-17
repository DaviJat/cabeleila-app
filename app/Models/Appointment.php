<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

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
}
