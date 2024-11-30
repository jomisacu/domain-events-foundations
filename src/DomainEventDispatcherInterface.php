<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

interface DomainEventDispatcherInterface
{
    public function dispatch(DomainEventInterface $domainEvent): void;
}
