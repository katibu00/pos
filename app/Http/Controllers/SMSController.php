<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function balance()
    {
      

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://app.smartsmssolutions.com/io/api/client/v1/balance/?token=ZwF5tQ6UEexf0QZnriE8ziFcz04B57EVBJJBpKpnWShvlac2zA',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
     
        return view('sms.balance',['balance'=>$response]);
    }

    public function compose()
    {
        return view('sms.compose');
    }


    public function send(Request $request)
    {
       
        $users = User::select('phone')->where('usertype', 'customer')->pluck('phone')->toArray();
       
        $count = count($users);

        $to = implode(',',$users);
       
        $message = $request->message;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.smartsmssolutions.com/io/api/client/v1/sms/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('token' => 'ZwF5tQ6UEexf0QZnriE8ziFcz04B57EVBJJBpKpnWShvlac2zA','sender' => 'KAT','to' => $to,'message' => $message,'type' => '0','routing' => '3','ref_id' => 'unique-ref-id','simserver_token' => 'simserver-token','dlr_timeout' => 'dlr-timeout'),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
     
       
        $data = json_decode($response);
       
        if ($data->code != 1000) {

            return response()->json([
                'status' => 404,
                'message' => 'Error Occured',
            ]);

        } else 
        {
            return response()->json([
                'status' => 200,
                'message' => 'Message Sent to '.$count.' Users',
            ]);
        }

    }


}
