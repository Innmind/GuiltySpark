<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Installation\Name,
    Installation\Gene,
    Exception\InstallationMustExpressAtLeastOneGene,
};
use Innmind\Server\Control\{
    Server,
    Server\Command,
    Server\Script,
};
use Innmind\Url\{
    PathInterface,
    Scheme,
    Authority\Port,
    NullPath,
    NullQuery,
    NullFragment,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
    SetInterface,
    Set,
    MapInterface,
};
use Symfony\Component\Yaml\Yaml;

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

    public function expressOn(Server $server): void
    {
        $commands = $this->genes->reduce(
            Stream::of(Command::class),
            static function(StreamInterface $commands, Gene $gene): StreamInterface {
                return $commands->add(
                    Command::foreground('genome')
                        ->withArgument('express')
                        ->withArgument((string) $gene->name())
                        ->withArgument((string) $gene->directory())
                );
            }
        );
        $expressOn = new Script(...$commands);

        $expressOn($server);
    }

    public function deployTowerOn(
        Server $server,
        Deployment $deployment
    ): void {
        $neighbours = $this->contacts->reduce(
            [],
            static function(array $neighbours, Name $contact) use ($deployment): array {
                $installation = $deployment->get($contact);
                $neighbours[(string) $contact] = [
                    'url' => (string) $installation
                        ->location()
                        ->withScheme(new Scheme('tcp'))
                        ->withAuthority(
                            $installation->location()->authority()->withPort(new Port(1337))
                        )
                        ->withPath(new NullPath)
                        ->withQuery(new NullQuery)
                        ->withFragment(new NullFragment),
                ];

                return $neighbours;
            }
        );
        $towerConfig = Yaml::dump(
            [
                'neighbours' => $neighbours,
                'actions' => [
                    'composer global update innmind/genome',
                    'composer global update innmind/tower',
                    'genome mutate',
                ],
            ],
            0
        );
        $deploy = new Script(
            Command::foreground('echo')
                ->withArgument($towerConfig)
                ->overwrite('/root/.innmind/tower.yml'),
            Command::foreground('genome')
                ->withArgument('express')
                ->withArgument('innmind/tower')
                ->withArgument('/root/.innmind')
        );

        $deploy($server);
    }
}
