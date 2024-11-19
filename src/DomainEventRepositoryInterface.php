<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use DateTimeInterface;

interface DomainEventRepositoryInterface
{
    public function save(DomainEventInterface $domainEvent): void;

    public function findById(string $eventId): ?DomainEventInterface;

    public function findByAggregateId(
        string $aggregateId,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): array;

    public function findByType(string $eventType, DateTimeInterface $dateFrom, DateTimeInterface $dateTo): array;
}
