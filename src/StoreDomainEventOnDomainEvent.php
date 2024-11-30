<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

final class StoreDomainEventOnDomainEvent
{
    public function __construct(
        private readonly DomainEventRepositoryInterface $domainEventRepository,
        private readonly IdempotencyHelperInterface $idempotencyHelper,
    ) {
    }

    public function __invoke(DomainEventInterface $domainEvent): void
    {
        if ($this->idempotencyHelper->alreadyHandled($domainEvent->getId(), self::class)) {
            return;
        }

        $this->domainEventRepository->save($domainEvent);

        $this->idempotencyHelper->markAsHandled($domainEvent->getId(), self::class);
    }

    public function handle(DomainEventInterface $domainEvent): void
    {
        $this->__invoke($domainEvent);
    }
}
