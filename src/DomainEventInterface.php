<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

interface DomainEventInterface
{
    public static function getType(): string;

    public function getAggregateId(): string;
}
