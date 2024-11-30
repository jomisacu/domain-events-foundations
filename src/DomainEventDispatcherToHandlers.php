<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

final class DomainEventDispatcherToHandlers implements DomainEventDispatcherInterface
{
    public function __construct(private readonly HandlersMapInterface $handlersMap)
    {
    }

    public function dispatch(DomainEventInterface $domainEvent): void
    {
        $handlers = $this->handlersMap->getHandlers($domainEvent::class);
        foreach ($handlers as $handler) {
            if (method_exists($handler, '__invoke')) {
                $handler->__invoke($domainEvent);
            } elseif (method_exists($handler, 'handle')) {
                $handler->handle($domainEvent);
            } else {
                throw new DomainEventHandlerWithoutEventParameterException();
            }
        }
    }
}
