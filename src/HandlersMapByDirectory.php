<?php

declare(strict_types=1);

namespace Jomisacu\DomainEventsFoundations;

use ReflectionClass;

final class HandlersMapByDirectory implements HandlersMapInterface
{
    private array $handlersMap = [];

    public function __construct(private readonly string $directory, private readonly string $cachePath)
    {
    }

    private function loadHandlers(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }

            $className = $this->getClassNameFromFile($fileInfo->getPathname());
            if ($className === null) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);
            if (!$reflectionClass->implementsInterface(DomainEventHandlerInterface::class)) {
                continue;
            }

            $domainEventClassName = $this->getHandledEventClassName($reflectionClass);
            if ($domainEventClassName === null) {
                continue;
            }

            $this->handlersMap[$domainEventClassName][] = $reflectionClass->getName();
        }
    }

    private function getClassNameFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $contents, $matches)) {
            $namespace = $matches[1];
        } else {
            return null;
        }

        if (preg_match('/class\s+(\S+)/', $contents, $matches)) {
            $className = $matches[1];
        } else {
            return null;
        }

        if ($namespace && $className) {
            $FQCN = $namespace . '\\' . $className;
        } else {
            $FQCN = $className;
        }

        return $FQCN;
    }

    private function getHandledEventClassName(ReflectionClass $reflectionClass): ?string
    {
        $method = null;
        if ($reflectionClass->hasMethod('handle')) {
            $method = $reflectionClass->getMethod('handle');
        } elseif ($reflectionClass->hasMethod('__invoke')) {
            $method = $reflectionClass->getMethod('__invoke');
        } else {
            throw new DomainEventHandlerWithoutHandlerMethodException(
                "Neither 'handle' nor '__invoke' method found in " . $reflectionClass->getName()
            );
        }

        $parameters = $method->getParameters();
        if (count($parameters) === 0) {
             throw new DomainEventHandlerWithoutEventParameterException(
                 "Handler method in " . $reflectionClass->getName() . " has no parameters"
             );
        } elseif ($parameters[0]->getType() === null || !is_a($parameters[0]->getType()->getName(), DomainEventInterface::class, true)) {
            throw new DomainEventHandlerWithoutDomainEventInterfaceAsParameterException(
                "Handler method in " . $reflectionClass->getName() . " has a wrong parameter"
            );
        }

        return $parameters[0]->getType()->getName();
    }

    public function getHandlers(string $domainEventClassName): array
    {
        if (file_exists($this->cachePath) && $this->isFresh($this->cachePath, $this->directory)) {
            $this->handlersMap = require $this->cachePath;
        } else {
            $this->loadHandlers();
            file_put_contents($this->cachePath, '<?php return ' . var_export($this->handlersMap, true) . ';');
        }

        return $this->handlersMap[$domainEventClassName] ?? [];
    }

    private function isFresh(string $cachePath, string $directory): bool
    {
        $cacheTime = filemtime($cachePath);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }

            if (filemtime($fileInfo->getPathname()) > $cacheTime) {
                return false;
            }
        }

        return true;
    }
}
