<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $duration_minutes
 */
#[Fillable(['name', 'description', 'price', 'duration_minutes'])]
class Service extends Model
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
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * Get the appointments associated with the service.
     *
     * @return BelongsToMany
     */
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class)->withTimestamps();
    }
}
