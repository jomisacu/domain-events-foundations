<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

interface IdempotencyHelperInterface
{
    public function alreadyHandled(string $eventId, string $handlerClass): bool;

    public function markAsHandled(string $eventId, string $handlerClass): void;
}
