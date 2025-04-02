<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

final class DomainEventDispatcherLaravel implements DomainEventDispatcherInterface
{
    public function __construct(private readonly DomainEventRepositoryInterface $domainEventRepository)
    {
    }

    public function dispatch(DomainEventInterface $domainEvent): void
    {
        $this->domainEventRepository->save($domainEvent);

        // Assuming you are using Laravel's event system
        \Event::dispatch($domainEvent);
    }
}
