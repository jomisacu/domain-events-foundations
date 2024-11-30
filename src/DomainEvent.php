<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use DateTime;
use DateTimeZone;
use Symfony\Component\Uid\Factory\UuidFactory;

abstract class DomainEvent implements DomainEventInterface
{
    public readonly string $id;
    public readonly string $occurred_on;
    public readonly string $type;

    public function __construct(string $id = null, string $occurred_on = null)
    {
        $this->id = $id ?? (new UuidFactory())->create()->toRfc4122();
        $this->occurred_on = (new DateTime($occurred_on ?? 'now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        $this->type = self::getType();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public static function getType(): string
    {
        return sprintf(
            '%s.%s.%s.%s.%s',
            static::getOrganization(),
            static::getService(),
            static::getVersion(),
            static::getEntity(),
            static::getEvent(),
        );
    }

    protected static function getOrganization(): string
    {
        throw new \RuntimeException('You must implement the getOrganization method in ' . static::class . ' class');
    }

    protected static function getService(): string
    {
        return 'monolith';
    }

    protected static function getVersion(): string
    {
        return 'v1';
    }

    protected static function getEntity(): string
    {
        throw new \RuntimeException('You must implement the getEntity method in ' . static::class . ' class');
    }

    protected static function getEvent(): string
    {
        throw new \RuntimeException('You must implement the getEvent method in ' . static::class . ' class');
    }
}
