<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    /** @use HasFactory<\Database\Factories\UserAddressFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'house_street',
        'barangay',
        'city',
        'province',
        'region',
        'zip_code',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Returns a formatted single-line representation of the address.
     */
    public function getFormattedAttribute(): string
    {
        $parts = array_filter([
            $this->house_street,
            $this->barangay ? 'Brgy. '.$this->barangay : null,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ]);

        return implode(', ', $parts);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
