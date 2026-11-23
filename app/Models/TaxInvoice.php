<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TaxInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'tax_invoice_number',
        'invoice_header_id',
        'tax_period',
        'dpp_amount',
        'ppn_amount',
        'status',
        'qr_code_url',
    ];

    protected $casts = [
        'dpp_amount' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function invoiceHeader()
    {
        return $this->belongsTo(InvoiceHeader::class);
    }
}
