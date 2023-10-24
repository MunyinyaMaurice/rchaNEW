@extends('layout-session')
@section('tile','paymentInfoExport')
@section('content')

<div>
    <h1>Download Test</h1>

    {{-- <form action="http://127.0.0.1:8000/api/auth/exportPaymentInfoExcel" method="GET"> --}}
        <form action="http://127.0.0.1:8000/api/auth/export-payment-info" method="GET">
        <button type="submit">Download Excel File</button>
    </form>
</div>


@endsection