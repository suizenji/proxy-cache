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
    private string $path = '/_debug/cache/rule/';

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
            'cache_rule[type]' => 'Testing',
            'cache_rule[cond]' => 'Testing',
            'cache_rule[tranId]' => 'Testing',
        ]);

        self::assertResponseRedirects('/cache/rule/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new CacheRule();
        $fixture->setType('My Title');
        $fixture->setCond('My Title');
        $fixture->setTranId('My Title');

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
        $fixture->setType('My Title');
        $fixture->setCond('My Title');
        $fixture->setTranId('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'cache_rule[type]' => 'Something New',
            'cache_rule[cond]' => 'Something New',
            'cache_rule[tranId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/cache/rule/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getCond());
        self::assertSame('Something New', $fixture[0]->getTranId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new CacheRule();
        $fixture->setType('My Title');
        $fixture->setCond('My Title');
        $fixture->setTranId('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/cache/rule/');
    }
}
