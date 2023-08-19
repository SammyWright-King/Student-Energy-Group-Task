<?php

namespace App\Interfaces;

use App\Models\Meter;
use Illuminate\Database\Eloquent\Collection;

interface MeterInterface
{
    public function findById(int $id): Meter;

    public function findWhere(string $key, mixed $value): ?Meter;

    public function fetchWhere(string $key, mixed $value): Collection;

    public function fetchAll(): Collection;

    public function save(array $data): Meter;

    public function update(int $id, array $data): Meter;

}