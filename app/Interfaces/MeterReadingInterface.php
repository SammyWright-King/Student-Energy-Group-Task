<?php

namespace App\Interfaces;

use App\Models\MeterReading;

interface MeterReadingInterface
{
    public function findById(int $id): MeterReading;

    public function save(array $data): MeterReading;

    public function bulkSave(array $data): void;
}