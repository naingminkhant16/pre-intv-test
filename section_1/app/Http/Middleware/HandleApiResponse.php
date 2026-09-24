<?php

namespace App\Http\Middleware;

use App\Api\ApiResponseFormatter;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleApiResponse
{
    public function __construct(private readonly ApiResponseFormatter $formatter)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $api = $this->formatter->start($start);

        $response = $next($request);

        if ($this->isApi($request, $response)) {
            $statusCode = $response->getStatusCode();
            $data = $response->getData();
            return $api->make($data, $statusCode);
        }
        
        return $response;
    }

    private function isApi(Request $request, $response): bool
    {
        return $response instanceof JsonResponse || $request->expectsJson();
    }
}
