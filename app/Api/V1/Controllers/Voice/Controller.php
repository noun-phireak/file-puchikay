<?php

namespace App\Api\V1\Controllers\Voice;

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
class Controller extends ApiController
{
    use Helpers;
    
    public function index(Request $request){
        //validation
        $this->validate($request, 
            [
                'project'       => 'required',
                'file'          => 'required',
                'folder'          => 'required',
            ],
            [
                'project.required'          => 'Please input project',
                'file.required'             => 'Please input base64 file',
                'folder.required'           => 'Please input folder',
            ]
        );

        $project = $request->project;
        $folder = $request->folder;
        $fileName = $request->fileName;
        $file = $request->file;
        $resizes = $request->resize;
        $is_return_full_url = $request->is_return_full_url;
        /**
         * Check if project folder existing
        */
        $project_folder_path = 'public/uploads/'.$project;
        // if (!file_exists($project_folder_path)) {
        //     return response ()->json ([
        //         'status_code' => 400,
        //         'message' => 'Project folder not found'
        //     ], 400);
        // }

        $extension = 'mp3';
        $image_base64 = base64_decode($file);
       
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
            'url' =>  'uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension
        ], 200);
       
    }

    //helper function resize
    function resize( $project, $folder, $fileName, $extension, $resizes, $is_return_full_url, $file_id){
        $data = [];
        $img = Image::make('public/uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension);
        //Loop json resize
        $resizes = json_decode($resizes, true);
        $i = 1;
        foreach($resizes as $index => $row){
            $mkdir_folder = 'public/uploads/'.$project.'/'.$folder.'/'.$row['width'].'x'.$row['height'];
            if(!file_exists($mkdir_folder)){
                File::makeDirectory($mkdir_folder, 0777,true);
            } 
            $img->resize($row['width'],$row['height'], function($constraint) {
                $constraint->aspectRatio();
            });
            $img->save('public/uploads/'.$project.'/'.$folder.'/'.$row['width'].'x'.$row['height'].'/'.$fileName.'.'.$extension);
            
            /**
             * Save file resizes database
            */
            // $file_resizes_data                      = new Resize();
            // $file_resizes_data->file_id             = $file_id;
            // $file_resizes_data->name                = $fileName;
            // $file_resizes_data->url                 = 'public/uploads/'.$project.'/'.$folder.'/'.$row['width'].'x'.$row['height'].'/'.$fileName.'.'.$extension;
            // $file_resizes_data->is_return_full_url  = $is_return_full_url;
            // $file_resizes_data->save();
            // push data
            if($is_return_full_url == 1){
                $data[] = env('APP_URL').'uploads/'.$project.'/'.$folder.'/'.$row['width'].'x'.$row['height'].'/'.$fileName.'.'.$extension;
            }else{
                $data[] = 'uploads/'.$project.'/'.$folder.'/'.$row['width'].'x'.$row['height'].'/'.$fileName.'.'.$extension;
            }
        }
        return $data;
    }

}
