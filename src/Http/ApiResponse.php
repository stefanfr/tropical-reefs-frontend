<?php

namespace App\Http;

use App\Normalizer\EntityNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiResponse extends JsonResponse
{
    /**
     * @var array
     */
    protected $context;

    /**
     * ApiResponse constructor.
     *
     * @param mixed $data
     * @param int $status
     * @param array $context
     * @param array $headers
     * @param bool $json
     */
    public function __construct(
        $data = null,
        int $status = 200,
        array $context = [],
        array $headers = [],
        bool $json = false
    )
    {
        $this->context = $context;
        parent::__construct($this->format($data), $status, $headers, (gettype($data) === 'object' || (isset($data[0]) && gettype($data[0]) === 'object') ? true : $json));
    }

    /**
     * Format the API response.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    private function format($data = null)
    {
        if (gettype($data) === 'object' || (isset($data[0]) && gettype($data[0]) === 'object')) {
            $encoder = new JsonEncoder();
            $defaultContext = array_merge(
                [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                        return $object->getId();
                    },
                ],
                $this->context
            );
            $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

            $serializer = new Serializer([$normalizer], [$encoder]);
            return $serializer->serialize($data, 'json');
        }

        return $data;
    }
}
