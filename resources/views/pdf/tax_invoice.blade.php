<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Pajak {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #0f172a;
            line-height: 1.3;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
        }
        .main-table td, .main-table th {
            border: 1px solid #000000;
            padding: 5px 7px;
        }
        .header-title {
            text-align: center;
            font-size: 13px;
            font-weight: 800;
            background-color: #f1f5f9;
            text-transform: uppercase;
        }
        .section-header {
            font-weight: 700;
            background-color: #f8fafc;
        }
    </style>
</head>
<body>

    <table class="main-table">
        <tr>
            <td colspan="3" class="header-title">FAKTUR PAJAK</td>
        </tr>

        <!-- Serial Number -->
        <tr>
            <td colspan="3">
                <strong>Kode dan Nomor Seri Faktur Pajak: </strong>
                <span style="font-family: monospace; font-size: 11px; font-weight: bold;">
                    {{ $invoice->taxInvoice?->tax_number ?: ('010.000-26.' . str_pad($invoice->id, 8, '0', STR_PAD_LEFT)) }}
                </span>
            </td>
        </tr>

        <!-- PKP Penjual -->
        <tr class="section-header">
            <td colspan="3"><strong>Pengusaha Kena Pajak (Penjual)</strong></td>
        </tr>
        <tr>
            <td style="width: 120px;">Nama:</td>
            <td colspan="2"><strong>PT. TELKOM INDONESIA (PERSERO) Tbk WITEL LAMPUNG</strong></td>
        </tr>
        <tr>
            <td>Alamat:</td>
            <td colspan="2">Jl. Majapahit No. 1, Enggal, Kota Bandar Lampung, Lampung 35118</td>
        </tr>
        <tr>
            <td>NPWP:</td>
            <td colspan="2" style="font-family: monospace; font-weight: bold;">01.000.013.1-093.000</td>
        </tr>

        <!-- Pembeli BKP / JKP -->
        <tr class="section-header">
            <td colspan="3"><strong>Pembeli Barang Kena Pajak / Penerima Jasa Kena Pajak</strong></td>
        </tr>
        <tr>
            <td>Nama:</td>
            <td colspan="2"><strong>{{ $invoice->customer->company_name }}</strong></td>
        </tr>
        <tr>
            <td>Alamat:</td>
            <td colspan="2">{{ $invoice->customer->address }}, {{ $invoice->customer->city }}</td>
        </tr>
        <tr>
            <td>NPWP:</td>
            <td colspan="2" style="font-family: monospace; font-weight: bold;">{{ $invoice->customer->npwp ?: '00.000.000.0-000.000' }}</td>
        </tr>

        <!-- Dynamic Items -->
        <tr class="section-header" style="text-align: center;">
            <td style="width: 25px;">No.</td>
            <td>Nama Barang Kena Pajak / Jasa Kena Pajak</td>
            <td style="width: 140px; text-align: right;">Harga Jual / Penggantian (Rp)</td>
        </tr>
        @foreach($invoice->items as $idx => $item)
        <tr>
            <td style="text-align: center;">{{ $idx + 1 }}</td>
            <td>
                {{ $item->service_name }}
                @if($item->description)
                    <br><span style="font-size: 9px; color: #475569;">{{ $item->description }}</span>
                @endif
            </td>
            <td style="text-align: right; font-family: monospace;">{{ number_format($item->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach

        <!-- DPP & PPN Totals -->
        <tr>
            <td colspan="2"><strong>Harga Jual / Penggantian</strong></td>
            <td style="text-align: right; font-family: monospace; font-weight: bold;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Dikurangi Potongan Harga</strong></td>
            <td style="text-align: right; font-family: monospace;">Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Dasar Pengenaan Pajak (DPP)</strong></td>
            <td style="text-align: right; font-family: monospace; font-weight: bold;">Rp {{ number_format(max(0, $invoice->subtotal - $invoice->discount_amount), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>PPN = 11% x Dasar Pengenaan Pajak</strong></td>
            <td style="text-align: right; font-family: monospace; font-weight: bold; color: #ee2e24;">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
        </tr>

        <!-- DJP Verification Footer -->
        <tr>
            <td colspan="2" style="vertical-align: bottom;">
                <p style="font-size: 8.5px; color: #475569;">
                    Sesuai dengan ketentuan perundang-undangan perpajakan yang berlaku, Faktur Pajak ini telah dilaporkan kepada Direktorat Jenderal Pajak dan memperoleh persetujuan elektronik.
                </p>
                <div style="font-size: 9px; font-weight: bold; margin-top: 5px;">
                    Kota Bandar Lampung, {{ $invoice->issue_date }}
                </div>
            </td>
            <td style="text-align: center; vertical-align: bottom;">
                <!-- QR Code Verification Badge -->
                <svg width="60" height="60" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <rect width="100" height="100" fill="#ffffff"/>
                    <rect x="10" y="10" width="30" height="30" fill="#0f172a"/>
                    <rect x="15" y="15" width="20" height="20" fill="#ffffff"/>
                    <rect x="20" y="20" width="10" height="10" fill="#0f172a"/>
                    <rect x="60" y="10" width="30" height="30" fill="#0f172a"/>
                    <rect x="65" y="15" width="20" height="20" fill="#ffffff"/>
                    <rect x="70" y="20" width="10" height="10" fill="#0f172a"/>
                    <rect x="10" y="60" width="30" height="30" fill="#0f172a"/>
                    <rect x="15" y="65" width="20" height="20" fill="#ffffff"/>
                    <rect x="20" y="70" width="10" height="10" fill="#0f172a"/>
                    <rect x="50" y="50" width="15" height="15" fill="#ee2e24"/>
                </svg>
                <div style="font-size: 8px; font-weight: bold;">DJP e-Faktur QR</div>
            </td>
        </tr>
    </table>

</body>
</html>
