<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $contentType = $request->headers->get('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $data = json_decode($request->getContent(), true);

            // If JSON is invalid, throw a bad request error
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON data');
            }

            // Replace the request data with decoded JSON
            $request->request->replace(is_array($data) ? $data : []);
        }
    }
}
