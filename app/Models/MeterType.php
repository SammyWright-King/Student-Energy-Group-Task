<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeterType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public $timestamps = true;

    /**
     * one meter type has many meters 
     */
    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
}
