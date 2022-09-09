<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;

/** @see Symfony\Component\HttpKernel\EventListener */
class RouterListener implements EventSubscriberInterface
{
    private UrlMatcherInterface $matcher;
    private RequestContext $context;
    private ?LoggerInterface $logger;
    private RequestStack $requestStack;

    public function __construct(
        UrlMatcherInterface $matcher,
        RequestStack $requestStack,
        RequestContext $context = null,
        LoggerInterface $logger = null,
    ) {
        if (null === $context && !$matcher instanceof RequestContextAwareInterface) {
            throw new \InvalidArgumentException('You must either pass a RequestContext or the matcher must implement RequestContextAwareInterface.');
        }

        $this->matcher = $matcher;
        $this->context = $context ?? $matcher->getContext();
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    private function setCurrentRequest(Request $request = null)
    {
        if (null !== $request) {
            try {
                $this->context->fromRequest($request);
            } catch (\UnexpectedValueException $e) {
                throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
            }
        }
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }

        try {
            $this->matcher->match($request->getPathInfo());
        } catch (\RuntimeException $e) {
            $parameters['_route'] = 'app_proxy';
            $parameters['_controller'] = 'App\Controller\ProxyController::index';
            $request->attributes->add($parameters);

            $this->logger->info('setted request attributes');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 33]],
        ];
    }
}
