<?php

namespace App\Api\V1\Controllers\Upload;

use Illuminate\Http\Request;
use App\Api\V1\Controllers\ApiController;
use Dingo\Api\Routing\Helpers;
use TelegramBot; 
use Image;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ImageController extends ApiController
{
    use Helpers;

    /**
     * Overview
     * Combine cropping and resizing to format image in a smart way. 
     * The method will find the best fitting aspect ratio of your given width and height on the current image automatically, cut it out and resize it to the given dimension.
     * 
     */
    function fit($folder, $fileName, $extension){
        $sizes = $_POST['sizes']; 
        $sizeArr = explode("-", $sizes); 
        foreach($sizeArr as $row){
            $dim = explode("x", $row); 
            if(count($dim) == 2){
                if( is_int(intval($dim[0])) && is_int(intval($dim[1])) ){
                    $img = Image::make($folder.$fileName.'.'.$extension);
                    $img->fit($dim[0], $dim[1]);
                    $img->save($folder.$fileName.'-'.$row.'.'.$extension);
                }   
            }           
        }
    }

    /**
     * Overview
     *  - Upload data with base64 request
     *  - Using method post geting data vai $request
     *  - $request data : file, folder, filename, sizes
     * Flow 
     *  - Check if have $request have file or not
     *      + if do not have file do N/A
     *      + if have file
     *          - check if have $request have folder or not
     *              + if do not have folder, it will automatic go to "public/uploads/unknown/" path.
     *              + if have path folder but it do not have folder created it will automatically create folder by mkdir($folder , 0777, true)
     *          - get base64 file
     *              + explode base64 string to devide base64 and image string
     *              + explode image type aux to get image path and extentions
     *          - check if have fileName $request or not
     *              + if have file name, it automatic set file name by $request fileName
     *              + if do not have file name, it will set file name vai uniqid() function
     *         - create uri link =  $folder.'/'.$fileName.'.'.$extension => "public/upload/roadcare/pothole/21322.jpeg"
     *         - convert base64 image string to image file vai php function base64_decode($image_parts)
     *         - write image file to uri folder vai php function  file_put_contents($uri, $image_base64)
     *         - check if have $request sizes
     *              + if do not have size $request go next 
     *              + if have size $request call function fit() by passing param $folder, $fileName, $extension
     */
    function upload64base(Request $request){
        if(isset($_POST['file'])){
            $folder = isset($_POST['folder'])?$_POST['folder']:"uploads/unknown/";
            if(!file_exists($folder)){
                mkdir($folder , 0777, true);
            }
            $image_parts = explode(";base64,", $_POST['file']);
            $image_type_aux = explode("image/", $image_parts[0]);
            $extension = $image_type_aux[1];
            $fileName = isset($_POST['fileName'])?$_POST['fileName']: uniqid();
            $uri = $folder.'/'.$fileName.'.'.$extension; 
            $image_base64 = base64_decode($image_parts[1]);
            file_put_contents($uri, $image_base64);   
            if( isset($_POST['sizes']) ){
                $this->fit($folder, $fileName, $extension); 
            }   
            if(isset($_GET['return']) && $_GET['return'] == 1){
                echo env('APP_URL').'/'.$uri; 
            } 
        } 
    }

    /**
     * Overview
     * - upload data with file request like (png, jpeg, jpg) extension
     */
    function upload(Request $request){
        if($request->hasFile('file')) {    
            $image = $request->file('file');
            $extension = $image->getClientOriginalExtension(); 
            $folder = isset($_POST['folder'])?$_POST['folder']:"uploads/unknown/";
            $fileName = isset($_POST['fileName'])?$_POST['fileName']: uniqid();
            if(!file_exists($folder)){
                mkdir($folder , 7777, true);
            }
            $img = Image::make($image->getRealPath());
            $img = $img->save($folder.$fileName.'.'.$extension);
            if( isset($_POST['sizes']) ){
                $this->fit($folder, $fileName, $extension); 
            }
            if(isset($_GET['return']) && $_GET['return'] == 1){
                echo env('APP_URL').'/'.$folder.$fileName.'.'.$extension; 
            } 
        }
    }

	/**
	*	new update of upload file to storage/app
	*	10/09/2019
	*	@return json
	*/
    function upload64baseV2(Request $request){
      // $folder = $request->folder;
       $base64Image = $request->file;
       $project = $request->project;
       $fileName = $request->fileName;
        if($base64Image && $project && $fileName){           
			$type = explode('/', substr($base64Image, 0, strpos($base64Image, ';')))[1];
			$img = '';
			$dir = $project.'/'.Carbon::now()->year .'/'.Carbon::now()->format('Ym').'/'.Carbon::now()->format('Ymd');	
						
            $image_parts = explode(";base64,", $base64Image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $extension = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $img = $dir.'/'.$fileName.'.'.$type;
            Storage::disk('local')->put($img, $image_base64);
		
			$txt = $dir.'/'.$fileName.'.txt';
			Storage::disk('local')->put($txt, $base64Image);
			$img = 'storage/app/'.$img;
			$uri =  env('APP_URL')."/$img";
			return response ()->json ([
				'status_code' => 200,
				'uri' => $uri
			], 200 );
        } 
        return response ()->json ([
                'status_code' => 400,
                'message' => 'Bad request'
            ], 400);
    }

    /** 
     * Overview
     *  - function to resize image and do not effect aspect ration
     *  - resize extension 500x500, 375x375, 250x250
     */
    function resize($folder, $fileName, $extension){
        $sizes = $_POST['sizes']; 
        $sizeArr = explode("-", $sizes); 
        foreach($sizeArr as $row){
            $dim = explode("x", $row); 
            if(count($dim) == 2){
                if( is_int(intval($dim[0])) && is_int(intval($dim[1])) ){
                    $img = Image::make($folder.$fileName.'.'.$extension);
                    $img->resize('500', '500', function($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($folder.$fileName.'-x1'.'.'.$extension);
                    $img->resize('375', '375', function($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($folder.$fileName.'-x2'.'.'.$extension);
                    $img->resize('250', '250', function($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($folder.$fileName.'-x3'.'.'.$extension);
                }
            }           
        }
    }
    
    /** Function to resize image */
    function resize64base(Request $request){
        if(isset($_POST['file'])){
            $folder = isset($_POST['folder'])?$_POST['folder']:"uploads/unknown/";
            if(!file_exists($folder)){
                mkdir($folder , 0777, true);
            }
            $image_parts = explode(";base64,", $_POST['file']);
            $image_type_aux = explode("image/", $image_parts[0]);
            $extension = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = isset($_POST['fileName'])?$_POST['fileName']: uniqid();
            $uri = $folder.'/'.$fileName.'.'.$extension; 
            file_put_contents($uri, $image_base64); 
            if( isset($_POST['sizes']) ){
                $this->resize($folder, $fileName, $extension); 
            }
            if(isset($_GET['return']) && $_GET['return'] == 1){
                echo env('APP_URL').'/'.$uri; 
            } 
        } 
    }


}
