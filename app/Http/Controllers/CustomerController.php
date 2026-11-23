<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of enterprise clients.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);

        $customers = $this->customerService->getPaginatedCustomers($search, $perPage);

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Store a newly created enterprise customer.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->customerService->createCustomer($request->validated(), $request->user()?->id);

        return redirect()->back()->with('success', 'Enterprise customer created successfully.');
    }

    /**
     * Update the specified enterprise customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->updateCustomer($customer->id, $request->validated(), $request->user()?->id);

        return redirect()->back()->with('success', 'Customer record updated successfully.');
    }

    /**
     * Remove the specified customer (Soft Delete).
     */
    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        $this->customerService->deleteCustomer($customer->id, $request->user()?->id);

        return redirect()->back()->with('success', 'Customer record deleted successfully.');
    }

    /**
     * Export customer directory to Excel.
     */
    public function export()
    {
        return $this->customerService->exportExcel();
    }

    /**
     * Import customer directory from uploaded Excel file.
     */
    public function import(ImportCustomerRequest $request): RedirectResponse
    {
        $this->customerService->importExcel($request->file('file'), $request->user()?->id);

        return redirect()->back()->with('success', 'Customers imported successfully from Excel.');
    }
}
