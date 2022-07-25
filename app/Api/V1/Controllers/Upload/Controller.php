<?php

namespace App\Api\V1\Controllers\Upload;

use Illuminate\Http\Request;
use App\Api\V1\Controllers\ApiController;
use Dingo\Api\Routing\Helpers;
use TelegramBot; 

class Controller extends ApiController
{
    use Helpers;
    function upload64base(Request $request){
        
        if(isset($_POST['file'])){

            
            
            $folder = isset($_POST['folder'])?$_POST['folder']:"uploads/roadcare/pothole/";
            $fileName = isset($_POST['fileName'])?$_POST['fileName']: uniqid().'.jpg';

            $image_parts = explode(";base64,", $_POST['file']);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
           
            $uri = $folder.$fileName;

            // $response = TelegramBot::sendMessage([
            //   'chat_id' => 229388689, 
            //   'text' => '<b>File has been upload. </b>'.env('APP_URL').'/'.$uri,
            //   'parse_mode' => 'HTML'
            // ]);

            file_put_contents($uri, $image_base64);  
            if(isset($_GET['return']) && $_GET['return'] == 1){
                echo env('APP_URL').'/'.$uri; 
            } 
        } 
    }
}
