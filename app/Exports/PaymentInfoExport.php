<?php

namespace App\Exports;

use App\Models\Payment;
// use Illuminate\Support\Facades\DB;
// use App\Exports\PaymentInfoExport;
// use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\RCHAcontroller\paymentController;

class PaymentInfoExport implements FromCollection
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['data']);
    }
}
