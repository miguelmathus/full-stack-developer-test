<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Classes\ResponseAction;
use App\Classes\ResponseCheckout;

class VehiclesController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['registerVehicle','registerCheckIn','registerCheckOut']]);
    }

    public function registerVehicle(Request $request){

        $validatedData = $request->validate([
            'type' => 'required',
            'numberPlate' => 'required',
            'status' => 'required'
        ]);

        $last_record = DB::table('vehicles')->where('numberPlate', $request->numberPlate)->first();     
        if($last_record == null){

            $result = DB::connection('mongodb')->collection('vehicles')->insert(array(
                "type"=> $request->type,
                "numberPlate"=> $request->numberPlate,
                "status"=> $request->status
            ));
            if($result){
                $response = new ResponseAction('0','Registro exitoso');
            }else{
                $response = new ResponseAction('99','No fue posible registrar el Vehiculo');
            }
        }else{
            $response = new ResponseAction('99','Numero de Placa ya registrado');
        }
        return response()->json($response);
    }

    public function registerCheckIn(Request $request){
        $validatedData = $request->validate([
            'numberPlate' => 'required',
        ]);

        $last_record = DB::table('journals')->where('numberPlate', $request->numberPlate)->where('checkOut',null)->orderBy('id', 'desc')->first();     
        
        if($last_record == null){

            $result = DB::connection('mongodb')->collection('journals')->insert(array(
                "numberPlate"=> $request->numberPlate,
                "status"=> "INGRESADO",
                "checkIn" => date('Y/m/d H:i:s'),
                "updatedBy" => [ "username" => "" ],
                "checkOut" => null,
                "user" => [ "username" => "miguel" ] //hack mientras jwt funciona

            ));
            if($result){
                $response = new ResponseAction('0','Registro exitoso');
            }else{
                $response = new ResponseAction('99','No fue posible registrar el el ingreso');
            }
        }else{
            $response = new ResponseAction('99','Este vehiculo ya ha ingresado');
        }
        return response()->json($response);
    }

    public function registerCheckOut(Request $request){
        setlocale(LC_MONETARY, 'en_US');
        $responseCheckout = new ResponseCheckout();
        $responseCheckout->setCurrency('$');
        $validatedData = $request->validate([
            'numberPlate' => 'required',
        ]);

        $last_record = DB::table('journals')->where('numberPlate', $request->numberPlate)->where('checkOut',null)->orderBy('id', 'desc')->first();     
        
        if($last_record != null){
            $last_record=  (object) $last_record;
            $result = DB::connection('mongodb')->collection('journals')->where('_id',$last_record->_id)->update(array(
                "status" => "EGRESADO",
                "checkOut" => date('Y/m/d H:i:s'),
                "updatedBy" => [ "username" => "miguel" ] //hack mientras jwt funciona
            ));
            
            if($result){
                $response = new ResponseAction('0','Registro exitoso');
            }else{
                $response = new ResponseAction('99','No fue posible registrar el egreso');
            }
            $responseCheckout->setResponse($response);

            $last_record = DB::table('journals')->where('numberPlate', $request->numberPlate)->orderBy('id', 'desc')->first();
            $last_record = (object) $last_record;
            $vehicle_info = DB::table('vehicles')->where('numberPlate',$request->numberPlate)->first();
            
            if($vehicle_info !=null ){
                $vehicle_info = (object) $vehicle_info;
                //registred car
                $cartype=$vehicle_info->type;
            }else{
                //not registred
                $cartype='EXTERNO';
            }
            $payment_config = DB::table('vehicle_types')->where('name',$cartype)->first();
            if($payment_config !=null){
                $payment_config = (object) $payment_config;
                $checkin = strtotime($last_record->checkIn);
                $checkout = strtotime($last_record->checkOut);
                $interval = abs($checkout - $checkin);
                $minutes = round($interval / 60,2);
                $amount = $minutes*($payment_config->changing);
                
            }else{
                //invalid type
            }
            $responseCheckout->setCheckin($last_record->checkIn);
            $responseCheckout->setCheckout($last_record->checkOut);
            $responseCheckout->setAmount($this->asDollars($amount));
            $responseCheckout->setMinutes($minutes);

            $timestamp = strtotime($responseCheckout->getCheckin());
            $month = date('m',$timestamp);
            DB::connection('mongodb')->collection('parking_sessions')->insert(array(
                
                "numberPlate"=>$request->numberPlate,
                "status"=>"ACTIVO",
                "created"=>$responseCheckout->getCheckin(),
                "minutes"=>$responseCheckout->getMinutes(),
                "amount"=>$amount,
                "month"=>$month,
                "type"=>$cartype

            ));

        }else{
            $response = new ResponseAction('89','Este Vehiculo no posee un ingreso activo');
            $responseCheckout->setResponse($response);

        }
        return response()->json($responseCheckout);
    }

    function asDollars($value) {
        if ($value<0) return "-".asDollars(-$value);
        return '$' . number_format($value, 2);
      }

}
