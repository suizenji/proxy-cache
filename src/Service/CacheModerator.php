<?php

namespace App\Service;

use App\Repository\CacheRuleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheModerator
{
    public array $rules;

    public function __construct(CacheRuleRepository $repo)
    {
        $this->rules = $repo->findAll();
    }

    public function shouldCachedResponse(Request $request): bool
    {
        return false;
    }

    public function cachedResponse(Request $request): Response
    {
        return new Response('hello');
    }
}
