<?php

namespace App\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use stdClass;

class ApiResponseFormatter
{
    private bool $success;
    private int $status;
    private float $start;

    public function __construct()
    {
        $this->success = true;
        $this->status = 200;
        $this->start = 0;
    }

    public function make($data, int|null $status = null): JsonResponse
    {
        if ($status) $this->status = $status;

        if ($this->status >= 300) {
            $response = $this->error($data);
        } else {
            $response = $this->success($data);
        }

        return response()->json($response, $this->status);
    }

    public function error($exception): array
    {
        $this->success = 0;
        $response = $this->response();
        if ($this->status === 422) {
            $response["errors"] = $exception->errors;
        } else {
            $response["errors"] = [
                "message" => $exception->message ?? ($exception ?? 'Internal Server Error!')
            ];
        }
        return $response;
    }

    private function response(): array
    {
        return [
            "success" => $this->success,
            "status" => $this->status,
            "meta_key" => $this->meta(),
        ];
    }

    private function meta(): array
    {
        return [
            'method' => $this->method(),
            'endpoint' => $this->path(),
            'duration' => $this->duration()
        ];
    }

    private function method(): string
    {
        return strtolower(Request::method());
    }

    private function path(): string
    {
        return Request::path();
    }

    private function duration(): float
    {
        return round((microtime(true) - $this->start) * 1000, 2);
    }

    private function success(stdClass $data): array
    {
        if (isset($data->meta)) {
            // for pagination
            $response = array_merge($this->response(), (array)$data);
        } else {
            $response = $this->response();
            $response['data'] = $data->data ?? $data;
        }
        return $response;
    }

    public function start(float $start): static
    {
        $this->start = $start;
        return $this;
    }
}
