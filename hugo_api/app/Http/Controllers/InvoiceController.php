<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Classes\ResponseAction;
use App\Classes\ResponseCheckout;

class InvoiceController extends Controller
{

public function __construct()
{
    $this->middleware('jwt', ['except' => ['closingPeriod','getInvoice','createReport','executePayment']]);
}

public function closingPeriod(Request $request){
    $month = $request->route('month');
    $opencheck = DB::connection('mongodb')->collection('invoices')->where('month', str_pad($month, 2, '0', STR_PAD_LEFT))->get();
    $opencheck = (Object) $opencheck;
    if($opencheck == null || sizeof($opencheck)==0){
        $monthrecords = DB::connection('mongodb')->collection('parking_sessions')->where('month', str_pad($month, 2, '0', STR_PAD_LEFT))->where('type','RESIDENTE')->groupBy('numberPlate')->get();
        $monthrecords = (Object) $monthrecords;
        

        foreach($monthrecords as $item) {
            $monthPlateRecords = DB::connection('mongodb')->collection('parking_sessions')->where('month', str_pad($month, 2, '0', STR_PAD_LEFT))->where('numberPlate',$item['numberPlate'])->get();
            $monthPlateRecords = (Object) $monthPlateRecords;
            $minutesMonth=0;
            $amountMonth=0;
            foreach($monthPlateRecords as $session) {
                $minutesMonth=$minutesMonth+$session['minutes'];
                $amountMonth=$amountMonth+$session['amount'];
            }

            $validity=date('Y/m/d');
            $result = DB::connection('mongodb')->collection('invoices')->insert(array(
                "vehicle" => $item['numberPlate'],
                "status" => 'GENERADO',
                "created" => date('Y/m/d H:i:s'),
                "validity" => date("Y/m/t", strtotime($validity)),
                "minutes" => $minutesMonth,
                "amount"=> $amountMonth,
                "month"=> str_pad($month, 2, '0', STR_PAD_LEFT),
                "payDate" => null,
                "recivedBy" => [ "username" => "" ] //hack mientras jwt funciona
            ));    
        }
        $response = new ResponseAction('0','Cierre generado exitosamente');  
    }else{
        $response = new ResponseAction('97','los invoices para este mes ya fueron generados');  
    }  
    return response()->json($response);
}

 public function getInvoice(Request $request){
    $month = $request->route('month');
    $plate = $request->route('plate');
    $getInvoice = DB::connection('mongodb')->collection('invoices')->where('month', str_pad($month, 2, '0', STR_PAD_LEFT))->where('vehicle',$plate)->get();
    $getInvoice = (Object) $getInvoice;
    if($getInvoice != null && sizeof($getInvoice)>0){
        return response()->json($getInvoice);
    }else{
        $response = new ResponseAction('96','Invoice no encontrado');  
        return response()->json($response);
    }
    
 }

 public function createReport(Request $request){
    $month = $request->route('month');
    
    $getInvoices = DB::connection('mongodb')->collection('invoices')->where('month', str_pad($month, 2, '0', STR_PAD_LEFT))->get();
    $getInvoices = (Object) $getInvoices;
    if($getInvoices != null && sizeof($getInvoices)>0){
        $getInvoices = (Object) $getInvoices;
        foreach($getInvoices as $item) {
            $data_array[] = 
                     array(
                        'plate' => $item['vehicle'],
                        'minutes' => $item['minutes'],
                        'amount' => $item['amount']
                );
            
        }

        return response()->json($data_array);
    }else{
        $response = new ResponseAction('96','No se encontraron datos para este mes');  
        return response()->json($response);
    }
 }

 public function executePayment(Request $request){
    $month = $request->route('month');
    $plate = $request->route('plate');

    $getInvoice = DB::connection('mongodb')->collection('invoices')->where('month', str_pad($month, 2, '0', STR_PAD_LEFT))->where('vehicle',$plate)->where('status','GENERADO')->first();
    $getInvoice = (Object) $getInvoice;
    if($getInvoice != null && get_object_vars($getInvoice)){
        $result = DB::connection('mongodb')->collection('invoices')->where('_id',$getInvoice->_id)->update(array(
            "status" => "PAGADO",
            "payDate" => date('Y/m/d H:i:s'),
            "recivedBy" => [ "username" => "miguel" ] //hack mientras jwt funciona
        ));
        $response = new ResponseAction('0','Pago Realizado');  
    }else{
        $response = new ResponseAction('100','Este Vehiculo no posee facturas pendientes');  
    }
    return response()->json($response);
}

}
