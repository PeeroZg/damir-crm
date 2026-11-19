<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $offer->number }}</title>
    <style>
        @page { margin: 32px 40px 60px; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 10px; color: #1e293b; }
        .muted { color: #64748b; }
        .brand { color: #4f46e5; }
        h1 { font-size: 22px; margin: 0; letter-spacing: -0.5px; }
        table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brandbar td { padding: 0; vertical-align: middle; }
        .logo { width: 26px; height: 26px; text-align: center; background: #4f46e5; color: #fff; font-weight: bold; border-radius: 6px; font-size: 14px; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
        .label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 3px; }
        .items th { background: #4f46e5; color: #fff; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.4px; padding: 7px 8px; text-align: left; }
        .items td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; }
        .items tr:nth-child(even) td { background: #f8fafc; }
        .right, .items th.right { text-align: right; }
        .totals td { padding: 4px 8px; }
        .grand td { font-size: 13px; font-weight: bold; border-top: 2px solid #4f46e5; padding-top: 8px; }
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 8px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 6px; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #eef2ff; color: #4338ca; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="footer">
        {{ $seller['name'] }} · {{ $seller['address'] }} · OIB {{ $seller['oib'] }} · IBAN {{ $seller['iban'] }} · {{ $seller['email'] }}
    </div>

    <table class="header">
        <tr>
            <td style="width: 55%">
                <table class="brandbar" style="width: auto;">
                    <tr>
                        <td class="logo">D</td>
                        <td style="padding-left: 8px; font-size: 15px; font-weight: bold;">Damir <span class="brand">CRM</span></td>
                    </tr>
                </table>
                <p class="muted" style="margin-top: 10px; line-height: 1.5">
                    <strong style="color:#1e293b">{{ $seller['name'] }}</strong><br>
                    {{ $seller['address'] }}<br>
                    OIB: {{ $seller['oib'] }}<br>
                    {{ $seller['email'] }}
                </p>
            </td>
            <td class="right">
                <h1>{{ mb_strtoupper(__('Offer')) }}</h1>
                <p style="font-size: 13px; margin: 4px 0 8px;" class="brand"><strong>{{ $offer->number }}</strong></p>
                <span class="status">{{ $offer->status->label() }}</span>
                <p class="muted" style="margin-top: 10px; line-height: 1.6">
                    {{ __('Issue date') }}: <strong style="color:#1e293b">{{ format_date($offer->issued_at) }}</strong><br>
                    {{ __('Valid until') }}: <strong style="color:#1e293b">{{ format_date($offer->valid_until) }}</strong>
                </p>
            </td>
        </tr>
    </table>

    <table style="margin-top: 22px;">
        <tr>
            <td style="width: 50%; padding-right: 8px;">
                <div class="box">
                    <div class="label">{{ __('Customer') }}</div>
                    <strong style="font-size: 11px">{{ $offer->company?->name }}</strong><br>
                    @if ($offer->company?->address){{ $offer->company->address }}<br>@endif
                    @if ($offer->company?->city){{ $offer->company->city }}<br>@endif
                    @if ($offer->company?->oib)OIB: {{ $offer->company->oib }}@endif
                </div>
            </td>
            <td style="width: 50%; padding-left: 8px;">
                <div class="box">
                    <div class="label">{{ __('Subject') }}</div>
                    <strong style="font-size: 11px">{{ $offer->deal?->title }}</strong><br>
                    @if ($offer->deal?->contact){{ __('Attn.') }} {{ $offer->deal->contact->full_name }}@if ($offer->deal->contact->position), {{ $offer->deal->contact->position }}@endif<br>@endif
                    {{ __('Prepared by') }}: {{ $offer->owner?->name }}
                </div>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 22px;">
        <thead>
            <tr>
                <th style="width: 4%">#</th>
                <th>{{ __('Description') }}</th>
                <th class="right" style="width: 12%">{{ __('Qty') }}</th>
                <th class="right" style="width: 15%">{{ __('Unit price') }}</th>
                <th class="right" style="width: 9%">{{ __('Disc. %') }}</th>
                <th class="right" style="width: 16%">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offer->items as $i => $item)
                <tr>
                    <td class="muted">{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ App\Support\Money::number($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2) }} {{ $item->unit }}</td>
                    <td class="right">{{ money($item->unit_price, $offer->currency) }}</td>
                    <td class="right">{{ (float) $item->discount_percent ? App\Support\Money::number($item->discount_percent, 0) : '—' }}</td>
                    <td class="right">{{ money($item->line_total, $offer->currency) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 14px;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 20px;">
                @if ($offer->notes)
                    <div class="label">{{ __('Notes & terms') }}</div>
                    <p style="line-height: 1.5; margin-top: 0">{!! nl2br(e($offer->notes)) !!}</p>
                @endif
            </td>
            <td style="vertical-align: top;">
                <table class="totals">
                    <tr><td class="muted">{{ __('Subtotal') }}</td><td class="right">{{ money($offer->subtotal, $offer->currency) }}</td></tr>
                    <tr><td class="muted">{{ __('VAT') }} {{ App\Support\Money::number($offer->vat_rate, 0) }} %</td><td class="right">{{ money($offer->vat_amount, $offer->currency) }}</td></tr>
                    <tr class="grand"><td>{{ __('Total') }}</td><td class="right brand">{{ money($offer->total, $offer->currency) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="muted" style="margin-top: 40px;">{{ __('Thank you for your trust. We look forward to working together.') }}</p>
</body>
</html>
