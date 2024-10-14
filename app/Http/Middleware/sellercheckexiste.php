<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Auth;
use Session;
class sellercheckexiste
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
        if(Auth::id()){
            $user=Auth::user();
           
            if($user->user_type=='3'){
                return $next($request);
            }
            else{
                if(Session::get("is_web")=='1'){
                    return redirect('/');
                }else{
                    return redirect('sellerlogin');
                }
                
            }
            
        }else{
            if(Session::get("is_web")=='1'){
                    return redirect('/');
            }else{
                    return redirect('sellerlogin');
            }
        }
    }
}
