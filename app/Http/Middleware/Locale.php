<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Config;
use Session;
use Auth;

class Locale {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $locale = "en";
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $locale = $currentUser->preferred_language;
        }
        
        if (Auth::guard('web')->check()) {
            $currentUser = Auth::guard('web')->user();
            $locale = $currentUser->preferred_language;
        }

        /* $raw_locale = Session::get('locale');
          if ($raw_locale == 1) {

          } else {

          } */

        App::setLocale($locale);
        return $next($request);
    }

}
