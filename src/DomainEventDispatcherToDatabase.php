<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use Jomisacu\DomainEventsFoundations\DomainEventDispatcherInterface;

final class DomainEventDispatcherToDatabase implements DomainEventDispatcherInterface
{
    public function __construct(private readonly StoreDomainEventOnDomainEvent $storeDomainEventOnDomainEvent)
    {
    }

    public function dispatch(DomainEventInterface $domainEvent): void
    {
        $this->storeDomainEventOnDomainEvent->__invoke($domainEvent);
    }
}
