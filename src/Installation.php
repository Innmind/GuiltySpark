<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Installation\Name,
    Installation\Gene,
    Exception\InstallationMustExpressAtLeastOneGene,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\{
    StreamInterface,
    SetInterface,
    Set,
};

final class Installation
{
    private $name;
    private $genes;
    private $contacts;

    public function __construct(
        Name $name,
        StreamInterface $genes,
        StreamInterface $contacts,
        PathInterface $workingDirectory,
        string $spark
    ) {
        if ((string) $genes->type() !== Gene::class) {
            throw new \TypeError(sprintf(
                'Argument 2 must be of type StreamInterface<%s>',
                Gene::class
            ));
        }

        if ((string) $contacts->type() !== Name::class) {
            throw new \TypeError(sprintf(
                'Argument 3 must be of type StreamInterface<%s>',
                Name::class
            ));
        }

        if ($genes->size() === 0) {
            throw new InstallationMustExpressAtLeastOneGene((string) $name);
        }

        $this->name = $name;
        $this->genes = $genes;
        $this->contacts = $contacts;
        $this->workingDirectory = $workingDirectory;
        $this->spark = $spark;
    }

    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return StreamInterface<Gene>
     */
    public function genes(): StreamInterface
    {
        return $this->genes;
    }

    /**
     * @return StreamInterface<Name>
     */
    public function contacts(): StreamInterface
    {
        return $this->contacts;
    }

    public function workingDirectory(): PathInterface
    {
        return $this->workingDirectory;
    }

    public function spark(): string
    {
        return $this->spark;
    }

    public function dependsOn(self $installation): bool
    {
        return $this
            ->contacts
            ->reduce(
                Set::of('string'),
                static function(SetInterface $names, Name $name): SetInterface {
                    return $names->add((string) $name);
                }
            )
            ->contains((string) $installation->name());
    }
}
