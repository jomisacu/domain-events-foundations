<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

/**
 * DomainEventDispatcherLaravel is a concrete implementation of the DomainEventDispatcherInterface
 * that uses Laravel's event system to dispatch domain events.
 *
 * This class allows you to dispatch domain events using Laravel's event system
 * and store them in a repository.
 *
 * If you are running in CLI context it will dispatch the event when the command is finished.
 * If you are running in a web context it will dispatch the event when the request is finished.
 *
 * @package Jomisacu\DomainEventsFoundations
 */
final class DomainEventDispatcherLaravel implements DomainEventDispatcherInterface
{
    public function __construct(private readonly DomainEventRepositoryInterface $domainEventRepository)
    {
    }

    public function dispatch(DomainEventInterface $domainEvent): void
    {
        $this->domainEventRepository->save($domainEvent);

        $this->dispatchAfter($domainEvent);
    }

    private function dispatchAfter(DomainEventInterface $domainEvent): void
    {
        if (\App::runningInConsole()) {
            $this->dispatchWhenCommandIsFinished($domainEvent);

            return;
        }

        $this->dispatchWhenRequestIsFinished($domainEvent);
    }

    private function dispatchWhenCommandIsFinished(DomainEventInterface $domainEvent): void
    {
        \Event::listen(\Illuminate\Console\Events\CommandFinished::class, function () use ($domainEvent) {
            \Event::dispatch($domainEvent);
        });
    }

    private function dispatchWhenRequestIsFinished(DomainEventInterface $domainEvent): void
    {
        \App::terminating(function () use ($domainEvent) {
            \Event::dispatch($domainEvent);
        });
    }
}
