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

    public function suggestCache(Request $request): CacheRule|bool
    {
        foreach ($this->repoRule->findAll() as $rule) {
            $type = $rule->getJudgeType();

            if ($type === CacheRule::JUDGE_TYPE_SCHEME_HOST) {
                if ($request->getSchemeAndHttpHost() === $rule->getJudgeCond()) {
                    return $rule;
                }
            }
        }

        return false;
    }

    public function createResponse(CacheRule $rule, Request $request): Response
    {
        if ($rule->getResType() === CacheRule::RES_TYPE_URL_MATCH) {
            $requestContext = $this->repoContext->findOneBy([
                'f2' => $request->getSchemeAndHttpHost() . $rule->getResCond(),
                'type' => Http::TYPE_SEND,
            ]);

            $tranId = $requestContext->getTranId();
        } else {
            throw new \LogicException('unsupported type');
        }

        $cond = ['tranId' => $tranId, 'type' => Http::TYPE_RECV];

        $contextEntity = $this->repoContext->findOneBy($cond);
        $headersEntities = $this->repoHeader->findBy($cond);
        $bodyEntity = $this->repoBody->findOneBy($cond);

        $status = $contextEntity->getF2();

        $headers = [];
        foreach ($headersEntities as $entity) {
            $headers[$entity->getName()] = $entity->getValue();
        }

        $body = $bodyEntity->getContent();

        return new Response($body, $status, $headers);
    }
}
