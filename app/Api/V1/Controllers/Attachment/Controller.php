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
class Controller extends ApiController
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

        //get file
        $image_parts = explode(";base64,", $file);
        $image_type_aux = explode("image/", $image_parts[0]);
        $extension = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
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
        /**
         * Save To data database
        */
        // $file_data = new Model();
        // $file_data->project = $project;
        // $file_data->file_name = $fileName;
        // $file_data->url = $uri;
        // $file_data->is_return_full_url = $is_return_full_url;
        // $file_data->resize = $resizes;
        // $file_data->save();
        /**
         * Check if resize existing
         */
        $resizes_data = '';
        if($resizes){
          $resizes_data =  $this->resize($project, $folder, $fileName, $extension, $resizes, $is_return_full_url, '');
        }
        if($is_return_full_url == 1){
            //return  env('APP_URL').$uri;
            return response ()->json ([
                'status_code' => 200,
                'url' =>  env('APP_URL').'uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension,
                'resizes' => $resizes_data
            ], 200);
        }
        return response ()->json ([
            'status_code' => 200,
            'url' => $uri,
            'resizes' => $resizes_data
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


    public function imageResize(Request $request){
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
        $resizes = $request->resize;
        $is_return_full_url = $request->is_return_full_url;
        /**
         * Check if project folder existing
        */
        $project_folder_path = 'public/uploads/'.$project;

        //get file
        $image_parts = explode(";base64,", $file);
        $image_type_aux = explode("image/", $image_parts[0]);
        $extension = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid();
        //Find full url
        $mkdir_folder = 'public/uploads/'.$project.'/'.$folder;
        $uri = 'public/uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension;
        if(!file_exists($mkdir_folder)){
            File::makeDirectory($mkdir_folder, 0777,true);
        } 
        // make original image
        file_put_contents($uri, $image_base64);
        // or create a new image resource from binary data
        // file_put_contents($uri, $image_base64);
        $img = Image::make('public/uploads/'.$project.'/'.$folder.'/'.$fileName.'.'.$extension);
        $img->resize('300','500', function($constraint) {
            $constraint->aspectRatio();
        });
        $img->save('public/uploads/'.$project.'/'.$folder.'/'.$fileName.'11'.'.'.$extension);
        $uriResize = 'public/uploads/'.$project.'/'.$folder.'/'.$fileName.'11'.'.'.$extension;
        /**
         * Check if resize existing
         */
        $resizes_data = '';
        return response ()->json ([
            'status_code' => 200,
            'url' => 'uploads/'.$project.'/'.$folder.'/'.$fileName.'11'.'.'.$extension,
            'resizes' => $resizes_data
        ], 200);
    }

}
