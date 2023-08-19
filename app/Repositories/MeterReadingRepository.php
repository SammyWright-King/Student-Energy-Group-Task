<?php

namespace App\Repositories;

use App\Interfaces\MeterReadingInterface;
use App\Models\MeterReading;

class MeterReadingRepository implements MeterReadingInterface
{
    /**
     * get one reading at a time
     */
    public function findById(int $id): MeterReading
    {
        return MeterReading::find($id)->with('meter');
    }

    /**
     * save reading to table
     */
    public function save(array $data): MeterReading
    {
        return MeterReading::firstOrCreate($data);
    }

    /**
     * bulk insert data into table
     */
    public function bulkSave(array $data): void
    {
        MeterReading::insert($data);
    }
}