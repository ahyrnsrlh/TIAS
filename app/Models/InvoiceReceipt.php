<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InvoiceReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'receipt_number',
        'invoice_header_id',
        'user_id',
        'payment_date',
        'amount_paid',
        'payment_method',
        'reference_number',
        'proof_file_path',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount_paid' => 'decimal:2',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
