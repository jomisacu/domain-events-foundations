<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use DateTimeInterface;
use PDO;

final class DomainEventRepositoryMySql implements DomainEventRepositoryInterface
{
    private readonly ?DomainEventSerializerInterface $serializer;

    public function __construct(private readonly PDO $pdo, ?DomainEventSerializerInterface $serializer = null)
    {
        $this->serializer = $serializer ?? new DomainEventSerializer();
    }

    public function save(DomainEventInterface $domainEvent): void
    {
        $statement = $this->pdo->prepare('INSERT INTO domain_events (id, aggregate_id, type, php_class, payload, occurred_on) VALUES (:id, :aggregate_id, :type, :php_class, :payload, :occurred_on)');
        $params = [
            'id' => $domainEvent->id,
            'aggregate_id' => $domainEvent->getAggregateId(),
            'type' => $domainEvent->type,
            'php_class' => $domainEvent::class,
            'payload' => $this->serializer->serialize($domainEvent),
            'occurred_on' => $domainEvent->occurred_on,
        ];
        $statement->execute($params);
    }

    public function findById(string $eventId): ?DomainEventInterface
    {
        $statement = $this->pdo->prepare('SELECT * FROM domain_events WHERE id = :id');
        $statement->execute(['id' => $eventId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->parseRow($row);
    }

    private function parseRow(mixed $row): DomainEventInterface
    {
        return $this->serializer->deserialize($row['payload'], $row['php_class']);
    }

    public function findByAggregateId(
        string $aggregateId,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): array {
        $statement = $this->pdo->prepare('
            SELECT * 
            FROM domain_events 
            WHERE aggregate_id = :aggregate_id 
                AND occurred_on BETWEEN :date_from AND :date_to
        ');
        $params = [
            'aggregate_id' => $aggregateId,
            'date_from' => $dateFrom->format('Y-m-d H:i:s'),
            'date_to' => $dateTo->format('Y-m-d H:i:s'),
        ];
        $statement->execute($params);

        $events = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $events[] = $this->parseRow($row);
        }

        return $events;
    }

    public function findByType(string $eventType, DateTimeInterface $dateFrom, DateTimeInterface $dateTo): array
    {
        $statement = $this->pdo->prepare('
            SELECT * 
            FROM domain_events 
            WHERE type = :type 
                AND occurred_on BETWEEN :date_from AND :date_to
        ');
        $params = [
            'type' => $eventType,
            'date_from' => $dateFrom->format('Y-m-d H:i:s'),
            'date_to' => $dateTo->format('Y-m-d H:i:s'),
        ];
        $statement->execute($params);

        $events = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $events[] = $this->parseRow($row);
        }

        return $events;
    }
}
