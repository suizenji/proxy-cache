<?php

namespace App\Service;

use App\Entity\Http;
use App\Entity\HttpContext;
use App\Entity\HttpHeader;
use App\Entity\HttpBody;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Recorder
{
    public function __construct(
        private EntityManagerInterface $manager,
    ) {
    }

    public function recordRequest($tranId, Request $request)
    {
        $createdAt = new \DateTimeImmutable();

        self::recordContext($this->manager, $tranId, Http::TYPE_SEND, $request->getMethod(), $request->getPathInfo(), 'TODO', $createdAt);
        self::recordHeaders($this->manager, $tranId, Http::TYPE_SEND, $request->headers->all());
        self::recordBody($this->manager, $tranId, Http::TYPE_SEND, $request->getContent());
    }

    public static function recordContext($manager, $tranId, $type, $f1, $f2, $f3, $createdAt)
    {
        $entity = (new HttpContext)
            ->setTranId($tranId)
            ->setType($type)
            ->setF1($f1)
            ->setF2($f2)
            ->setF3($f3)
            ->setCreatedAt($createdAt)
            ;

        $manager->persist($entity);
        $manager->flush();
    }

    public static function recordHeaders($manager, $tranId, $type, $headers = [])
    {
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $entity = (new HttpHeader)
                        ->setTranId($tranId)
                        ->setType($type)
                        ->setName($name)
                        ->setValue($value)
                        ;
            }

            $manager->persist($entity);
            $manager->flush();
        }
    }

    public static function recordBody($manager, $tranId, $type, $content = '')
    {
        $entity = (new HttpBody)
                ->setTranId($tranId)
                ->setType($type)
                ->setContent($content);
        ;

        $manager->persist($entity);
        $manager->flush();
    }

}
