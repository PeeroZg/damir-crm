@php
    $tones = [
        'draft' => ['#EDEDE9', '#4A4A45'], 'sent' => ['#E6EEFF', '#2547C9'], 'won' => ['#E2F6E9', '#146B3A'],
        'lost' => ['#FDE8EB', '#A8213F'], 'expired' => ['#F5ECE4', '#7A4520'],
    ];
    [$statusBg, $statusFg] = $tones[$offer->status->color()] ?? $tones['draft'];
    $font = fn ($file) => resource_path('fonts/'.$file);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ __('Offer') }} {{ $offer->number }}</title>
{{-- dompdf: tables for layout, no flexbox/grid, no CSS variables. Geist (OFL) with DejaVu Sans as fallback. --}}
<style>
    @font-face { font-family: 'Geist'; font-weight: 400; font-style: normal; src: url('{{ $font('Geist-Regular.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Geist'; font-weight: 700; font-style: normal; src: url('{{ $font('Geist-SemiBold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Geist Mono'; font-weight: 400; font-style: normal; src: url('{{ $font('GeistMono-Medium.ttf') }}') format('truetype'); }

    @page { margin: 18mm 16mm 22mm 16mm; }
    * { box-sizing: border-box; }
    body { font-family: 'Geist', 'DejaVu Sans', sans-serif; font-size: 9.5pt; line-height: 1.45; color: #1A1A18; margin: 0; }
    table { border-collapse: collapse; width: 100%; }
    td, th { vertical-align: top; padding: 0; }
    .mono { font-family: 'Geist Mono', 'DejaVu Sans Mono', monospace; }
    .muted { color: #6B6B65; }
    .right { text-align: right; }
    .cap { font-size: 7pt; letter-spacing: 0.8pt; text-transform: uppercase; color: #6B6B65; }
    .b { font-weight: 700; }
    .brand-name { font-size: 13pt; font-weight: 700; letter-spacing: -0.2pt; }
    .brand-name span { color: #85857E; }
    .doc-title { font-size: 26pt; font-weight: 700; letter-spacing: -0.8pt; line-height: 1; }
    .doc-no { font-size: 11pt; margin-top: 4pt; }
    .status { display: inline-block; margin-top: 6pt; padding: 2pt 7pt; border-radius: 8pt; background: {{ $statusBg }}; color: {{ $statusFg }}; font-size: 7.5pt; font-weight: 700; }
    .rule { height: 3pt; background: #1A1A18; margin: 14pt 0 0; }
    .rule-accent { height: 3pt; width: 38mm; background: #D4FA4E; }
    .box { border: 0.75pt solid #E5E5E0; border-radius: 6pt; padding: 9pt 11pt; }
    .box .name { font-size: 11pt; font-weight: 700; margin: 3pt 0 2pt; }
    .items { margin-top: 18pt; }
    .items th { background: #1A1A18; color: #FFFFFF; font-size: 7pt; letter-spacing: 0.8pt; text-transform: uppercase; font-weight: 700; padding: 7pt 8pt; text-align: left; }
    .items th.right { text-align: right; }
    .items td { padding: 8pt; border-bottom: 0.75pt solid #EDEDE9; }
    .items tr.alt td { background: #FBFBFA; }
    .items .num { width: 18pt; color: #85857E; }
    .totals td { padding: 4pt 8pt; }
    .grand { background: #D4FA4E; border-radius: 5pt; padding: 7pt 8pt; font-size: 13pt; font-weight: 700; }
    .thanks { margin-top: 26pt; font-size: 10pt; }
    .footer { position: fixed; bottom: -12mm; left: 0; right: 0; border-top: 0.75pt solid #E5E5E0; padding-top: 5pt; font-size: 6.5pt; color: #85857E; text-align: center; }
</style>
</head>
<body>

<div class="footer">
    {{ $seller['name'] }} · {{ $seller['address'] }} · OIB {{ $seller['oib'] }} · IBAN {{ $seller['iban'] }} · {{ $seller['email'] }}
</div>

<table>
    <tr>
        <td style="width: 58%;">
            <table style="width: auto;"><tr>
                <td style="width: 24pt; padding-right: 8pt; vertical-align: middle;"><img src="{{ public_path('images/damir-crm-mark-256.png') }}" style="width: 24pt; height: 24pt;" alt="Damir CRM"></td>
                <td style="vertical-align: middle;"><div class="brand-name">Damir <span>CRM</span></div></td>
            </tr></table>
            <div style="margin-top: 12pt;">
                <div class="b">{{ $seller['name'] }}</div>
                <div class="muted">{{ $seller['address'] }}</div>
                <div class="muted">OIB: {{ $seller['oib'] }}</div>
                <div class="muted">{{ $seller['email'] }}</div>
            </div>
        </td>
        <td class="right">
            <div class="doc-title">{{ __('Offer') }}</div>
            <div class="doc-no mono">{{ $offer->number }}</div>
            <div class="status">{{ $offer->status->label() }}</div>
            <table style="margin-top: 10pt;">
                <tr><td class="right muted" style="padding: 1pt 8pt 1pt 0;">{{ __('Issue date') }}</td><td class="right b" style="width: 78pt; white-space: nowrap;">{{ format_date($offer->issued_at) }}</td></tr>
                <tr><td class="right muted" style="padding: 1pt 8pt 1pt 0;">{{ __('Valid until') }}</td><td class="right b" style="white-space: nowrap;">{{ format_date($offer->valid_until) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<div class="rule"></div>
<div class="rule-accent"></div>

<table style="margin-top: 16pt;">
    <tr>
        <td style="width: 49%;">
            <div class="box">
                <div class="cap">{{ __('Customer') }}</div>
                <div class="name">{{ $offer->company?->name }}</div>
                @if ($offer->company?->address)<div>{{ $offer->company->address }}</div>@endif
                @if ($offer->company?->city)<div>{{ $offer->company->city }}</div>@endif
                @if ($offer->company?->oib)<div class="muted">OIB: {{ $offer->company->oib }}</div>@endif
            </div>
        </td>
        <td style="width: 2%;"></td>
        <td style="width: 49%;">
            <div class="box">
                <div class="cap">{{ __('Subject') }}</div>
                <div class="name">{{ $offer->deal?->title }}</div>
                @if ($offer->deal?->contact)<div>{{ __('Attn.') }} {{ $offer->deal->contact->full_name }}@if ($offer->deal->contact->position), {{ $offer->deal->contact->position }}@endif</div>@endif
                <div class="muted">{{ __('Prepared by') }}: {{ $offer->owner?->name }}</div>
            </div>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th class="num">#</th>
            <th>{{ __('Description') }}</th>
            <th class="right" style="width: 50pt;">{{ __('Qty') }}</th>
            <th class="right" style="width: 72pt;">{{ __('Unit price') }}</th>
            <th class="right" style="width: 54pt; white-space: nowrap;">{{ __('Disc. %') }}</th>
            <th class="right" style="width: 78pt;">{{ __('Amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($offer->items as $i => $item)
            <tr @class(['alt' => $i % 2 === 1])>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ App\Support\Money::number($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2) }} {{ $item->unit }}</td>
                <td class="right">{{ money($item->unit_price, $offer->currency) }}</td>
                <td class="right @if (! (float) $item->discount_percent) muted @endif">{{ (float) $item->discount_percent ? App\Support\Money::number($item->discount_percent, 0) : '—' }}</td>
                <td class="right b">{{ money($item->line_total, $offer->currency) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table style="margin-top: 16pt;">
    <tr>
        <td style="width: 52%; padding-right: 18pt;">
            @if ($offer->notes)
                <div class="cap">{{ __('Notes & terms') }}</div>
                <div style="margin-top: 4pt;">{!! nl2br(e($offer->notes)) !!}</div>
            @endif
        </td>
        <td>
            <table class="totals">
                <tr><td class="muted">{{ __('Subtotal') }}</td><td class="right">{{ money($offer->subtotal, $offer->currency) }}</td></tr>
                <tr><td class="muted" style="padding-bottom: 8pt;">{{ __('VAT') }} {{ App\Support\Money::number($offer->vat_rate, 0) }} %</td><td class="right" style="padding-bottom: 8pt;">{{ money($offer->vat_amount, $offer->currency) }}</td></tr>
            </table>
            <div class="grand"><table><tr><td>{{ __('Total') }}</td><td class="right">{{ money($offer->total, $offer->currency) }}</td></tr></table></div>
        </td>
    </tr>
</table>

<div class="thanks">{{ __('Thank you for your trust. We look forward to working together.') }}</div>

</body>
</html>
