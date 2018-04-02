<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Installation\Name,
    Exception\UnknownInstallations,
};
use Innmind\Immutable\{
    Sequence,
    MapInterface,
    Map,
    StreamInterface,
    Stream,
    SetInterface,
    Set,
};

final class InstallationArray implements \Iterator
{
    private $installations;

    public function __construct(Installation ...$installations)
    {
        $installations = Sequence::of(...$installations)->reduce(
            new Map('string', Installation::class),
            static function(MapInterface $installations, Installation $installation): MapInterface {
                return $installations->put(
                    (string) $installation->name(),
                    $installation
                );
            }
        );

        $dependencies = $installations
            ->values()
            ->reduce(
                Stream::of(Name::class),
                static function(StreamInterface $dependencies, Installation $installation): StreamInterface {
                    return $dependencies->append($installation->contacts());
                }
            )
            ->reduce(
                Set::of('string'),
                static function(SetInterface $dependencies, Name $name): SetInterface {
                    return $dependencies->add((string) $name);
                }
            );
        $unknownInstallations = $dependencies->diff($installations->keys());

        if ($unknownInstallations->size() > 0) {
            throw new UnknownInstallations((string) $unknownInstallations->join(', '));
        }

        $this->installations = $installations
            ->values()
            ->sort(static function(Installation $a, Installation $b): bool {
                return $a->dependsOn($b);
            });
    }

    public function current(): Installation
    {
        return $this->installations->current();
    }

    public function key(): Name
    {
        return $this->current()->name();
    }

    public function next(): void
    {
        $this->installations->next();
    }

    public function rewind(): void
    {
        $this->installations->rewind();
    }

    public function valid(): bool
    {
        return $this->installations->valid();
    }
}
