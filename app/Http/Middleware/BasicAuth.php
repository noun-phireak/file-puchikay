<?php
namespace App\Http\Middleware;
use Closure;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $AUTH_USER = 'fileuser';
        $AUTH_PASS = 'F!LEWQ12';
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        

        $is_not_authenticated = (
            !$has_supplied_credentials 
        );
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        // check if unauthorized with model
        $credentials = [
            'username'=> $username,
            'password'=>$password, 
            'deleted_at'=>null
        ];
        $token = Auth::attempt($credentials);
        if(!$token){
            return response ()->json ([
                'status_code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        if ($is_not_authenticated) {
            return response ()->json ([
                'status_code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }
        return $next($request);
    }
}