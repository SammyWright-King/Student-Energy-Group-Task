<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    use HasFactory;

    protected $fillable = ['mpxn', 'meter_type_id', 'installation_date', 'estimated_annual_consumption'];

    /**
     * meter is one type.whether electric or gas
     */
    public function type(): BelongsTo 
    {
        return $this->belongsTo(MeterType::class, 'meter_type_id');
    }

    /**
     * meter has many readings
     */
    public function readings(): HasMany 
    {
        return $this->hasMany(MeterReading::class)->orderBy('created_at', 'desc');
    }
}

