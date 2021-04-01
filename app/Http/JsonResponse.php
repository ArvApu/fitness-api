<?php

namespace App\Http;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;

class JsonResponse extends \Illuminate\Http\JsonResponse
{
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        if ($data instanceof Paginator) {
            $data = $this->convertPaginatorData($data);
        }

        parent::__construct($data, $status, $headers, $options);
    }

    /**
     * Convert paginator data
     *
     * @param Paginator $paginator
     * @return array
     */
    protected function convertPaginatorData(Paginator $paginator): array
    {
        $paginated = $paginator->toArray();

        $links = [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];

        $meta = Arr::except($paginated, [
            'data',
            'links',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);

        return [
            'data' => $paginated['data'],
            'links' => $links,
            'meta' => $meta,
        ];
    }
}
