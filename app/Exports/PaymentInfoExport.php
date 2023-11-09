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

// class PaymentInfoExport implements FromCollection
// {
    // /**
    // * @return \Illuminate\Support\Collection
    // */
//     protected $paymentInfo;
//     public function __construct($paymentInfo)
//     {
//         $this->paymentInfo = $paymentInfo;
//     }
//     public function collection()
//     {
//     return collect($this->paymentInfo);
//     }
// public function headings(): array
// {
//     return [
//         'User Email',
//         'User Phone Number',
//         'First Name',
//         'Last Name',
//         'Place Name',
//         'Place Location',
//         'Amount',
//         'Created At',
//         'Paid Token'
//     ];
// }
    // private function getPaymentInfo()
    // {
    //     // $user = JWTAuth::parseToken()->authenticate();

    //     $payInfoQuery = DB::table('users')
    //         ->join('payments', 'users.id', '=', 'payments.user_id')
    //         ->join('places', 'payments.place_id', '=', 'places.id')
    //         ->join('tokens', 'payments.token_id', '=', 'tokens.id')
    //         ->where('users.id', $user=1)
    //         ->select('users.email', 'users.phone_number', 'users.first_name', 'users.last_name', 'places.place_name', 'places.place_location', 'payments.amount', 'payments.created_at', 'tokens.paid_token');

    //     // Add sorting logic

    //     $results = $payInfoQuery->get();

    //     return $results;
    
    // }
// }
