<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     *
     * @SWG\SecurityScheme(
     *     securityDefinition="bearerAuth",
     *     type="apiKey",
     *     in="header",
     *     name="Authorization",
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
     */
    public function handle($request, Closure $next)
    {
        $key = $request->header('Authorization');
        $key = str_replace('Bearer ', '', $key);
        if ($key !== env('API_KEY')) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
