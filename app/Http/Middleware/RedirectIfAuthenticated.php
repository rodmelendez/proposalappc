<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\SesionController;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check() || Auth::user()) {
            //by me. Trying to prevent error when logging in
            if (Request::wantsJson()) {
                $user = Auth::user();
                if ($user && !empty($user->rememeber_token)) {
                    echo json_encode([
                        'ok' => 1,
                        'token' => $user->rememeber_token,
                        'url' => Session::get('url.intended', 'main')
                    ]);
                    die();
                }
                else {
                    SesionController::cerrarSesion(false);
                    echo json_encode([
                        'ok' => 0
                    ]);
                    die();
                }
            }
            //end by me

            return redirect('/');
        }

        return $next($request);
    }
}
