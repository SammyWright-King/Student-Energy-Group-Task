<?php

namespace App\Repositories;

use App\Interfaces\MeterInterface;
use App\Models\Meter;
use Illuminate\Database\Eloquent\Collection;

class MeterRepository implements MeterInterface
{
    /**
     * find meter by id
     */
    public function findById(int $id): Meter 
    {
        return Meter::find($id)
                    ->with('type')
                    ->with('readings');
    }

    /**
     * find meter where value is in key in table
     */
    public function findWhere(string $key, mixed $value): ?Meter
    {
        return Meter::with('type')
                        ->with('readings')
                        ->where($key, $value)->first();
    }

    /**
     * fetch meters where value is in key in table
     */
    public function fetchWhere(string $key, mixed $value): Collection
    {
        return Meter::with('type')
                        ->with('readings')
                        ->where($key, $value)->get();
    }

    /**
     * fetch all the meters in table
     */
    public function fetchAll(): Collection
    {
        return Meter::with('type')->with('readings')
                        ->orderBy('created_at', 'desc')->get();
    }

    /**
     * save new entry into meters table
     */
    public function save(array $data): Meter
    {
        return Meter::create($data);
    }

    /**
     * edit meter record
     */
    public function update(int $id, array $data): Meter
    {
        $meter =  Meter::find($id);
        $meter->update($data);
        return $meter;
    }
}