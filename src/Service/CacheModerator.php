<?php

namespace App\Service;

use App\Entity\CacheRule;
use App\Repository\CacheRuleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheModerator
{
    public function __construct(private CacheRuleRepository $repo)
    {
    }

    public function suggestResponse(Request $request): string|bool
    {
        foreach ($this->repo->findAll() as $rule) {
            $type = $rule->getType();

            if ($type === Entity::TYPE_SCHEME_HOST) {
                if ($request->getSchemeAndHttpHost() === $rule->getCond()) {
                    return $rule->getTranId();
                }
            }
        }

        return false;
    }

    public function createResponse(string $cacheKey): Response
    {
        return new Response('hello');
    }
}
