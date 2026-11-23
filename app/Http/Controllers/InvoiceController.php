<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 10);

        $invoices = $this->invoiceService->getPaginatedInvoices($search, $status, $perPage);

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'per_page' => $perPage,
            ],
            'statuses' => ['draft', 'generated', 'sent', 'paid', 'partially_paid', 'overdue', 'cancelled'],
            'flash_stats' => session('import_stats'),
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(): Response
    {
        $customers = Customer::orderBy('company_name')->get(['id', 'customer_code', 'company_name', 'name', 'virtual_account', 'npwp']);

        return Inertia::render('Invoices/Create', [
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->createInvoice($request->validated(), $request->user()?->id);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice generated successfully.');
    }

    /**
     * Display the specified invoice detail & printable view.
     */
    public function show(InvoiceHeader $invoice): Response
    {
        $invoiceDetails = $this->invoiceService->getInvoiceDetails($invoice->id);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoiceDetails,
        ]);
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(InvoiceHeader $invoice): Response
    {
        $invoiceDetails = $this->invoiceService->getInvoiceDetails($invoice->id);
        $customers = Customer::orderBy('company_name')->get(['id', 'customer_code', 'company_name', 'name', 'virtual_account', 'npwp']);

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoiceDetails,
            'customers' => $customers,
        ]);
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(UpdateInvoiceRequest $request, InvoiceHeader $invoice): RedirectResponse
    {
        $this->invoiceService->updateInvoice($invoice->id, $request->validated(), $request->user()?->id);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Request $request, InvoiceHeader $invoice): RedirectResponse
    {
        $this->invoiceService->deleteInvoice($invoice->id, $request->user()?->id);

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Bulk import customer billing invoices from Excel spreadsheet.
     */
    public function import(ImportInvoiceRequest $request): RedirectResponse
    {
        $stats = $this->invoiceService->importBulkInvoices($request->file('file'), $request->user()?->id);

        return redirect()->route('invoices.index')
            ->with('import_stats', $stats)
            ->with('success', "Bulk billing import completed. {$stats['imported']} invoices created.");
    }

    /**
     * Download sample Excel import template.
     */
    public function downloadTemplate()
    {
        return $this->invoiceService->downloadImportTemplate();
    }
}
