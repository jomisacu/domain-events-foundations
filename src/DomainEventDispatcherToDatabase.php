<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use Jomisacu\DomainEventsFoundations\DomainEventDispatcherInterface;

final class DomainEventDispatcherToDatabase implements DomainEventDispatcherInterface
{
    public function __construct(private readonly DomainEventRepositoryInterface $domainEventRepository)
    {
    }

    public function dispatch(DomainEventInterface $domainEvent): void
    {
        $this->domainEventRepository->save($domainEvent);
    }
}
