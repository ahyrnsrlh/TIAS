<?php

namespace App\Repositories\Contracts;

use App\Models\InvoiceHeader;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InvoiceRepositoryInterface
{
    public function getPaginated(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?InvoiceHeader;
    public function findByInvoiceNumber(string $invoiceNumber): ?InvoiceHeader;
    public function create(array $headerData, array $itemsData): InvoiceHeader;
    public function update(InvoiceHeader $invoice, array $headerData, ?array $itemsData = null): InvoiceHeader;
    public function delete(InvoiceHeader $invoice): bool;
    public function getOverduePendingInvoices(): Collection;
    public function getDashboardMetrics(): array;
}
