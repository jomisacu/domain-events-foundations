<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

interface HandlersMapInterface
{
    /**
     * @return array<DomainEventHandlerInterface>
     */
    public function getHandlers(string $domainEventClassName): array;
}
