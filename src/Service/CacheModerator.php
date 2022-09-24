<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheModerator
{
    public function shouldCachedResponse(Request $request): bool
    {
        return true;
    }

    public function cachedResponse(Request $request): Response
    {
        return new Response('hello');
    }
}
