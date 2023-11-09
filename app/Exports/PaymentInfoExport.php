<?php

namespace App\Exports;

use App\Http\Controllers\RCHAcontroller\paymentController;
use App\Models\Payment;
// use App\Exports\PaymentInfoExport;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Concerns\FromCollection;

class PaymentInfoExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $showPaymentInfo;

    public function __construct($showPaymentInfo)
    {
        $this->showPaymentInfo = $showPaymentInfo;
    }
    public function collection()
    {
    return collect($this->showPaymentInfo);
    }
    public function mapArraybleRow($row): array
    {
         // Check if each value in the row is an array
    foreach ($row as $key => $value) {
        if (!is_array($value)) {
            // Convert the value to an array
            $row[$key] = [$value];
        }
    }

        dd($row);
        return [
            $row->email,
            $row->phone_number,
            $row->first_name,
            $row->last_name,
            $row->place_name,
            $row->place_location,
            $row->amount,
            $row->created_at->toArray(),
            $row->paid_token->toArray()
        ];
    }
    

public function headings(): array
{
    return [
        'User Email',
        'User Phone Number',
        'First Name',
        'Last Name',
        'Place Name',
        'Place Location',
        'Amount',
        'Created At',
        'Paid Token'
    ];
}
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
    
    // }*/
    // public function query()
    // {
    //     return PaymentInfoController::getPaymentInfo($sortBy, $sortDirection, $perPage)->get();
    // }
}
