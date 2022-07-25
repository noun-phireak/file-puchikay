<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

	Route::get('/', function () {
        return response()->json(['message'=>' FILE APIa'], 200);
    });
	
    // $api->post('/upload/64base',                        ['uses' => 'App\Api\V1\Controllers\Upload\Controller@upload64base']);
    // $api->post('/upload/image/64base',                  ['uses' => 'App\Api\V1\Controllers\Upload\ImageController@upload64base']);
   	// $api->post('/upload/image',       		            ['uses' => 'App\Api\V1\Controllers\Upload\ImageController@upload']);    
    // $api->post('/upload/image/64base.v2',               ['uses' => 'App\Api\V1\Controllers\Upload\ImageController@upload64baseV2']);
    // $api->post('/upload/image/64base/resize',           ['uses' => 'App\Api\V1\Controllers\Upload\ImageController@resize64base']);    
    // $api->post('/upload/image/64base/resize',           ['uses' => 'App\Api\V1\Controllers\Upload\ImageController@resize64base']);
    
    // $api->group([ 'middleware' => 'basicAuth'], function ($api) {
        
    // });
    $api->post('/attach/image',                             ['uses' => 'App\Api\V1\Controllers\Attachment\Controller@index']);
    $api->post('/attach/image-resize',                      ['uses' => 'App\Api\V1\Controllers\Attachment\Controller@imageResize']);
    $api->post('/attach/voice',                             ['uses' => 'App\Api\V1\Controllers\Voice\Controller@index']);
    $api->post('/attach/file',                              ['uses' => 'App\Api\V1\Controllers\Attachment\FileController@index']);
    $api->post('/attach/file-epub',                         ['uses' => 'App\Api\V1\Controllers\Attachment\FileController@uploadFileEpub']);
    $api->get('/attach/get-file',                           ['uses' => 'App\Api\V1\Controllers\Attachment\FileController@getFile']);
});