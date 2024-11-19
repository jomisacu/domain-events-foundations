<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class DomainEventSerializer implements DomainEventSerializerInterface
{
    private readonly SerializerInterface $serializer;

    public function __construct(?SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer ?? new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function serialize(DomainEventInterface $domainEvent): string
    {
        return $this->serializer->serialize($domainEvent, 'json');
    }

    public function deserialize(string $payload, string $targetDomainEventClass): DomainEventInterface
    {
        return $this->serializer->deserialize($payload, $targetDomainEventClass, 'json');
    }
}
