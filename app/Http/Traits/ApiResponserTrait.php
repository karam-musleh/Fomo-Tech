<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponserTrait
{
    /**
     * Return a successful JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        // لو البيانات ResourceCollection
        if ($data instanceof ResourceCollection) {
            $resourceData = $data->resource;

            // لو ResourceCollection مربوطة بـ Pagination
            if ($resourceData instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $response['meta'] = [
                    'current_page' => $resourceData->currentPage(),
                    'last_page' => $resourceData->lastPage(),
                    'per_page' => $resourceData->perPage(),
                    'total' => $resourceData->total(),
                    'from' => $resourceData->firstItem(),
                    'to' => $resourceData->lastItem(),
                    'next_page_url' => $resourceData->nextPageUrl(),
                    'prev_page_url' => $resourceData->previousPageUrl(),
                ];
            }
        }

        return response()->json($response, $status);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
}
