<?php

namespace App\Service;

use App\Entity\CacheRule;
use App\Entity\Http;
use App\Entity\HttpContext;
use App\Entity\HttpHeader;
use App\Entity\HttpBodyt;
use App\Repository\CacheRuleRepository;
use App\Repository\HttpContextRepository;
use App\Repository\HttpHeaderRepository;
use App\Repository\HttpBodyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheModerator
{
    public function __construct(
        private CacheRuleRepository $repoRule,
        private HttpContextRepository $repoContext,
        private HttpHeaderRepository $repoHeader,
        private HttpBodyRepository $repoBody,
    ) {
    }

    public function suggestResponse(Request $request): string|bool
    {
        foreach ($this->repoRule->findAll() as $rule) {
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
        $cond = ['tranId' => $cacheKey, 'type' => Http::TYPE_RECV];

        $context = $this->repoContext->findOneBy($cond);
        $headers = $this->repoHeader->findBy($cond);
        $body = $this->repoBody->findOneBy($cond);

        // TODO generate response

        return new Response('hello');
    }
}
