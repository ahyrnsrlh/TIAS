<?php

namespace App\Services;

use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CustomerService
{
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getPaginatedCustomers(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->customerRepository->getPaginated($search, $perPage);
    }

    public function createCustomer(array $data, ?int $userId = null): Customer
    {
        if (empty($data['customer_code'])) {
            $latestId = Customer::max('id') + 1;
            $data['customer_code'] = 'CUST-TLK-' . str_pad($latestId, 3, '0', STR_PAD_LEFT);
        }

        if (empty($data['virtual_account'])) {
            $latestId = Customer::max('id') + 1;
            $data['virtual_account'] = '988114' . str_pad($latestId, 10, '0', STR_PAD_LEFT);
        }

        $data['created_by'] = $userId;
        $customer = $this->customerRepository->create($data);
        DashboardService::clearDashboardCache();

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'CUSTOMER_CREATED',
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'description' => "Created new enterprise client {$customer->company_name} ({$customer->customer_code})",
            'properties' => $customer->toArray(),
        ]);

        return $customer;
    }

    public function updateCustomer(int $id, array $data, ?int $userId = null): bool
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            return false;
        }

        $updatedCustomer = $this->customerRepository->update($customer, $data);
        DashboardService::clearDashboardCache();

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'CUSTOMER_UPDATED',
            'subject_type' => Customer::class,
            'subject_id' => $id,
            'description' => "Updated client details for {$customer->company_name} ({$customer->customer_code})",
            'properties' => $data,
        ]);

        return (bool) $updatedCustomer;
    }

    public function deleteCustomer(int $id, ?int $userId = null): bool
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            return false;
        }

        $companyName = $customer->company_name;
        $code = $customer->customer_code;

        $deleted = $this->customerRepository->delete($customer);
        DashboardService::clearDashboardCache();

        if ($deleted) {
            ActivityLog::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $userId,
                'action' => 'CUSTOMER_DELETED',
                'subject_type' => Customer::class,
                'subject_id' => $id,
                'description' => "Soft-deleted client record {$companyName} ({$code})",
            ]);
        }

        return $deleted;
    }

    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'Telkom_Lampung_Customers_' . date('Ymd_His') . '.xlsx');
    }

    public function exportExcel()
    {
        return $this->exportCustomers();
    }

    public function importCustomers($file, ?int $userId = null): void
    {
        Excel::import(new CustomersImport, $file);
        DashboardService::clearDashboardCache();

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'CUSTOMERS_IMPORTED',
            'subject_type' => Customer::class,
            'description' => "Bulk imported customer records from uploaded Excel file",
        ]);
    }
}
