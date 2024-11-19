<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

interface DomainEventSerializerInterface
{
    public function serialize(DomainEventInterface $domainEvent): string;

    public function deserialize(string $payload, string $targetDomainEventClass): DomainEventInterface;
}
