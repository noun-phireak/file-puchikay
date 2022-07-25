<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Response;
// Encryt
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class ImageController extends Controller
{
  public function index(Request $request){
        $file_url = 'http://file.mpwt.gov.kh';
        $decrypted = Crypt::decryptString($request->token);

        $image = $file_url.'/'.$decrypted;
        // return $image;
        $content  = file_get_contents($image);
        $response = Response::make($content, 200);
        $response->header('Content-Type', "image/jpeg");
        return $response;
  }
  
}
