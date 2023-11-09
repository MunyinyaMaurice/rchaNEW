<?php

namespace App\Http\Controllers\RCHAcontroller;
use App\Http\Controllers\RCHAcontroller\paymentController;
use Illuminate\Http\Request;
// use App\Exports\paymentInfoExport;
use App\Exports\PaymentInfoExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


class paymentInfoExportController extends Controller
{
// public function exportPaymentInfo()
// {
//     $paymentController = new paymentController();
//         $paymentInfo = $paymentController->showPaymentInfo(request());
//         return Excel::download(new PaymentInfoExport($paymentInfo), 'payment-info.xlsx');
   
// }
public function exportPaymentInfo($sortBy, $sortDirection, $perPage)
{
    return Excel::download(new PaymentInfoExport($sortBy, $sortDirection, $perPage), 'payment_info.xlsx');
}

}
