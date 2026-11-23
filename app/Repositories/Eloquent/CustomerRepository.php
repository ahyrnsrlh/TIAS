<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getPaginated(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Customer::withCount('invoices')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Customer::latest()->get();
    }

    public function findById(int $id): ?Customer
    {
        return Customer::with('invoices')->find($id);
    }

    public function findByCode(string $code): ?Customer
    {
        return Customer::where('customer_code', $code)->first();
    }

    public function create(array $data): Customer
    {
        if (empty($data['uuid'])) {
            $data['uuid'] = (string) Str::uuid();
        }
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    public function getTotalCount(): int
    {
        return Customer::count();
    }
}
