<?php

namespace App\Http\Middleware;

use App\Domain;
use Closure;

class CheckDomain
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
        // get domains from the database
        $domains = Domain::pluck('domain')->toArray();

        // see if the referer is in the database, if so, send them forward, otherwise, error out
        if (in_array(parse_url($request->server('HTTP_REFERER'), PHP_URL_HOST), $domains)) {
            return $next($request);
        } else {
            return response()->json(['error' => 'invalid url']);
        }

    }
}
