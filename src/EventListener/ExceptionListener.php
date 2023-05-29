<?php

namespace App\EventListener;

use App\Factory\NormalizerFactory;
use App\Http\ExceptionResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Throwable;

class ExceptionListener
{
    public function __construct(
        protected readonly NormalizerFactory $normalizerFactory
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = $this->createApiResponse($exception);
        $event->setResponse($response);
    }

    /**
     * @param  Throwable  $exception
     * @return ExceptionResponse
     * @throws ExceptionInterface
     */
    private function createApiResponse(Throwable $exception): ExceptionResponse
    {
        $normalizer = $this->normalizerFactory->getNormalizer($exception);
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode(
        ) : Response::HTTP_INTERNAL_SERVER_ERROR;

        try {
            $errors = $normalizer ? $normalizer->normalize($exception) : [];
        } catch (Exception $e) {
            $errors = [];
        }

        return new ExceptionResponse($exception->getMessage(), [$exception->getTraceAsString()], $errors, $statusCode);
    }
}
