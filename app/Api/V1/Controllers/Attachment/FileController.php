<?php

namespace App\Api\V1\Controllers\Attachment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Api\V1\Controllers\ApiController;
use Dingo\Api\Routing\Helpers;
use TelegramBot; 
use Image;
use File;
use Auth;
use App\Model\File as Model;
use App\Model\Resize;
use Illuminate\Support\Facades\Storage;

class FileController extends ApiController
{
    use Helpers;
    public function index(Request $request){
        //validation
        $this->validate($request, 
            [
                'project'           => 'required',
                'file'              => 'required',
                'folder'            => 'required',
            ],
            [
                'project.required'          => 'Please input project',
                'file.required'             => 'Please input base64 file',
                'folder.required'           => 'Please input folder',
            ]
        );

        $project = strtoupper($request->project);
        $folder = $request->folder;
        $fileName = $request->fileName;
        $file = $request->file;
        $extension = '';

        $info = substr($file, 5, strpos($file, ';')-5);
        $extension = explode("/", $info);
        if(isset($extension[1])){
            $ext = strtolower($extension[1]);
            if (strpos($ext, '+') !== false) {
               $result =  explode("+", $ext);
               $extension = $result[0];
            }else{
                $extension = $ext;
            }
        }
       
        /**
         * Check if project folder existing
        */
        $project_folder_path = 'public/uploads/'.$project;
       
        //get file
        $image_parts = explode(";base64,", $file);
        $image_base64 = base64_decode($image_parts[1]);
        // $image_type_aux = explode("image/", $image_parts[0]);
        // $extension = $image_type_aux[1];
        // $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid();
        //Find full url
        $mkdir_folder = 'public/uploads/'.$project.'/'.$folder;
        $uri = 'public/uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension;
        if(!file_exists($mkdir_folder)){
            File::makeDirectory($mkdir_folder, 0777,true);
        } 
        // make original image
        // file_put_contents($uri, $image_base64);
        // or create a new image resource from binary data
        file_put_contents($uri, $image_base64);
        return response ()->json ([
            'status_code' => 200,
            'url' => 'uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension
        ], 200);
    }

    public function uploadFileEpub(Request $request){
        $directory = 'uploads/test';
        if ($request->hasFile('file')) {
    		$file = $request->file('file');
    		if($file->isValid()){
                $fileName = time().'.'.$file->getClientOriginalExtension();
                // $sourceFile = $request->file('file');
                // $stream = Storage::disk('s3')->getDriver()
                //              ->readStream($sourceFile);

                    //Storage::disk('s3')->put('avatars/1', $stream);

                    return Storage::disk('s3')->put('avatars/2', $request->file('file'));
		    	// $path = $file->move(public_path($directory), $fileName);
                // $url = 'public/'.$directory.'/'.$fileName;
                // return response ()->json ([
                //     'status_code' => 200,
                //     'url' => $url
                // ], 200);
    		}
    		
    		
    	}
    }

    public function getFile(Request $request){
        $contents = Storage::disk('s3')->get('avatars/1/M18ab6qglyVajHpSVgABRCsiRa3oJo0pFimAPpDD.png');
        return $contents;
        //return response()->file($contents);
    }

}
