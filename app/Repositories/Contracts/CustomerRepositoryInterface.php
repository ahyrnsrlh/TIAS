<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function getPaginated(?string $search = null, int $perPage = 10): LengthAwarePaginator;
    public function getAll(): Collection;
    public function findById(int $id): ?Customer;
    public function findByCode(string $code): ?Customer;
    public function create(array $data): Customer;
    public function update(Customer $customer, array $data): Customer;
    public function delete(Customer $customer): bool;
    public function getTotalCount(): int;
}
