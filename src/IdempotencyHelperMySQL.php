<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

final class IdempotencyHelperMySQL implements IdempotencyHelperInterface
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function alreadyHandled(string $eventId, string $handlerClass): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT event_id 
            FROM domain_events_handled 
            WHERE event_id = :eventId 
              AND handler_class = :handlerClass
        ');

        $stmt->execute(['eventId' => $eventId, 'handlerClass' => $handlerClass]);

        return (bool)$stmt->fetchColumn();
    }

    public function markAsHandled(string $eventId, string $handlerClass): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO domain_events_handled (event_id, handler_class) VALUES (:eventId, :handlerClass)
        ');
        $stmt->execute(['eventId' => $eventId, 'handlerClass' => $handlerClass]);
    }
}
