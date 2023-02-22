<?php

namespace App\DataFixtures;

use App\Entity\CacheRule;
use App\Entity\Http;
use App\Service\Recorder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // normal
        $tranId = 'A001';
        $createdAt = new \DateTimeImmutable();

        Recorder::recordContext($manager, $tranId, Http::TYPE_SEND, 'GET', 'https://localhost/', 'HTTP/1.1', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => ['localhost'],
            'User-Agent' => ['Nginx', 'iPhone'],
        ]);
        Recorder::recordBody($manager, $tranId, Http::TYPE_SEND);

        Recorder::recordContext($manager, $tranId, Http::TYPE_RECV, 'HTTP/1.1', '200', 'OK', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_RECV, [
            'Content-Length' => ['6'],
        ]);
        Recorder::recordBody($manager, $tranId, Http::TYPE_RECV, 'foo');

        // POST
        $tranId = 'A002';
        sleep(1);
        $createdAt = new \DateTimeImmutable();

        Recorder::recordContext($manager, $tranId, Http::TYPE_SEND, 'POST', 'https://www.localhost.org/data/post', 'HTTP/1.1', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => ['localhost'],
        ]);
        Recorder::recordBody($manager, $tranId, Http::TYPE_SEND, file_get_contents(__FILE__));

        Recorder::recordContext($manager, $tranId, Http::TYPE_RECV, 'HTTP/1.1', '302', 'Found', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_RECV, []);
        Recorder::recordBody($manager, $tranId, Http::TYPE_RECV);

        // cache
        $cacheRule = new CacheRule();
        $cacheRule->setJudgeType(CacheRule::JUDGE_TYPE_SCHEME_HOST);
        $cacheRule->setJudgeCond('https://localhost');
        $cacheRule->setResType(CacheRule::RES_TYPE_URL_MATCH);
        $cacheRule->setResCond('/');
        $manager->persist($cacheRule);
        $manager->flush();

        $cacheRule = new CacheRule();
        $cacheRule->setJudgeType(CacheRule::JUDGE_TYPE_SCHEME_HOST);
        $cacheRule->setJudgeCond('https://www.localhost.org');
        $cacheRule->setResType(CacheRule::RES_TYPE_SCHEME_HOST_MATCH);
        $cacheRule->setResCond('*');
        $manager->persist($cacheRule);
        $manager->flush();
    }
}
