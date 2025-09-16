<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov.
 *
 * For license information, see the LICENSE file.
 */

namespace App;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LocalSubscriber.
 *
 * Subscribes to the kernel.request event to set the locale based on the URL or default.
 */
class LocalSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;

    /**
     * LocalSubscriber constructor.
     *
     * @param string $defaultLocale The default locale to use (e.g., 'pl')
     */
    public function __construct(string $defaultLocale = 'pl')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Handles the kernel.request event.
     *
     * Sets the locale of the request based on the '_locale' attribute from the URL.
     * If no locale is provided, uses the default locale.
     *
     * @param RequestEvent $event The event instance
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // Set locale from URL if available, otherwise use default
        $request->setLocale($request->get('_locale', $this->defaultLocale));
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array<string, array<int, array{string, int}>> Event name => [method, priority]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
