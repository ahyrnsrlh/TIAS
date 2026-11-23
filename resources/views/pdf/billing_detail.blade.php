<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Billing Detail {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
        }
        .company-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .doc-title {
            font-size: 18px;
            font-weight: 900;
            color: #0284c7;
            text-align: right;
            margin: 0;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .totals-box {
            width: 40%;
            float: right;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <p class="company-title">PT. TELKOM INDONESIA (PERSERO) Tbk</p>
                <span style="font-size: 10px; color: #ee2e24; font-weight: bold;">WITEL LAMPUNG - BILLING STATEMENT UNIT</span><br>
                <span style="font-size: 9px; color: #64748b;">Lampiran Rincian Penggunaan Layanan B2B / Enterprise Link</span>
            </td>
            <td style="text-align: right;">
                <p class="doc-title">RINCIAN TAGIHAN</p>
                <span style="font-size: 11px; font-family: monospace; font-weight: bold; color: #0f172a;">{{ $invoice->invoice_number }}</span>
            </td>
        </tr>
    </table>

    <!-- Meta Info -->
    <div class="meta-box">
        <table style="width: 100%; font-size: 10.5px;">
            <tr>
                <td style="width: 50%;">
                    <strong style="color: #0f172a; font-size: 12px;">{{ $invoice->customer->company_name }}</strong><br>
                    <span>ID Pelanggan: <strong>{{ $invoice->customer->customer_code }}</strong></span><br>
                    <span>NPWP: {{ $invoice->customer->npwp ?: 'N/A' }}</span>
                </td>
                <td style="width: 50%; text-align: right;">
                    <span>Periode Layanan: <strong>{{ $invoice->period_start ?: $invoice->issue_date }} s/d {{ $invoice->period_end ?: $invoice->due_date }}</strong></span><br>
                    <span>Tanggal Cetak: {{ date('Y-m-d H:i') }} WIB</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Detail Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th style="width: 100px;">Kode Tarif</th>
                <th>Nama Produk / Layanan Telkom</th>
                <th style="width: 50px; text-align: center;">Volume</th>
                <th style="width: 110px; text-align: right;">Tarif Dasar (Rp)</th>
                <th style="width: 120px; text-align: right;">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
            <tr>
                <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold; color: #0284c7;">{{ $item->service_code ?: 'TLK-SRV-0' . ($idx+1) }}</td>
                <td>
                    <strong style="color: #0f172a;">{{ $item->service_name }}</strong>
                    @if($item->description)
                        <br><span style="font-size: 9.5px; color: #64748b;">{{ $item->description }}</span>
                    @endif
                </td>
                <td style="text-align: center; font-family: monospace;">{{ $item->quantity }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="totals-box">
            <table style="width: 100%; font-size: 10.5px;">
                <tr>
                    <td style="color: #64748b;">Total Netto Layanan:</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b;">PPN ({{ $invoice->tax_percentage }}%):</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-size: 12px; font-weight: 800; color: #0284c7; border-top: 1px solid #cbd5e1;">
                    <td style="padding-top: 4px;">Total Tagihan Gross:</td>
                    <td style="text-align: right; font-family: monospace; padding-top: 4px;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px;">
        Dokumen Rincian Tagihan ini merupakan lampiran resmi dari Invoice {{ $invoice->invoice_number }} PT. Telkom Indonesia Witel Lampung.
    </div>

</body>
</html>
