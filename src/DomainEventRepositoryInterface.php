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

    /**
     * @return array<DomainEventInterface>
     */
    public function findByType(string $eventType, DateTimeInterface $from, DateTimeInterface $to): array;

    /**
     * @return array<DomainEventInterface>
     */
    public function getDomainEventsSince(\DateTimeInterface $from): array;

    public function findUnpublished(int $limit): array;

    /**
     * @param string[] $eventIds
     */
    public function markAsPublished(array $eventIds): void;
}
