<?php

namespace App\Test\Controller;

use App\Entity\CacheRule;
use App\Repository\CacheRuleRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CacheRuleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CacheRuleRepository $repository;
    private string $path = '/cache/rule/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(CacheRule::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('CacheRule index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'cache_rule[judgeType]' => 'Testing',
            'cache_rule[judgeCond]' => 'Testing',
            'cache_rule[resType]' => 'Testing',
            'cache_rule[resCond]' => 'Testing',
        ]);

        self::assertResponseRedirects('/cache/rule/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new CacheRule();
        $fixture->setJudgeType('My Title');
        $fixture->setJudgeCond('My Title');
        $fixture->setResType('My Title');
        $fixture->setResCond('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('CacheRule');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new CacheRule();
        $fixture->setJudgeType('My Title');
        $fixture->setJudgeCond('My Title');
        $fixture->setResType('My Title');
        $fixture->setResCond('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'cache_rule[judgeType]' => 'Something New',
            'cache_rule[judgeCond]' => 'Something New',
            'cache_rule[resType]' => 'Something New',
            'cache_rule[resCond]' => 'Something New',
        ]);

        self::assertResponseRedirects('/cache/rule/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getJudgeType());
        self::assertSame('Something New', $fixture[0]->getJudgeCond());
        self::assertSame('Something New', $fixture[0]->getResType());
        self::assertSame('Something New', $fixture[0]->getResCond());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new CacheRule();
        $fixture->setJudgeType('My Title');
        $fixture->setJudgeCond('My Title');
        $fixture->setResType('My Title');
        $fixture->setResCond('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/cache/rule/');
    }
}
