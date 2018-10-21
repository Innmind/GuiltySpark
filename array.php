<?php
declare(strict_types = 1);

use Innmind\GuiltySpark\{
    InstallationArray,
    Installation,
    Installation\Name,
    Installation\Gene,
};
use Innmind\Url\Path;
use Innmind\Immutable\Stream;

return function(): InstallationArray {
    return new InstallationArray(
        new Installation(
            new Name('00'),
            Stream::of(
                Gene::class,
                new Gene(
                    new Gene\Name('innmind/infrastructure-neo4j'),
                    new Path('/root')
                ),
                new Gene(
                    new Gene\Name('innmind/library'),
                    new Path('/root')
                ),
                new Gene(
                    new Gene\Name('innmind/infrastructure-nginx'),
                    new Path('/root')
                ),
                new Gene(
                    new Gene\Name('innmind/warden'),
                    new Path('/root')
                )
            ),
            Stream::of(Name::class),
            new Path('.'),
            ''
        ),
        new Installation(
            new Name('01'),
            Stream::of(
                Gene::class,
                new Gene(
                    new Gene\Name('innmind/crawler-app'),
                    new Path('/root')
                ),
                new Gene(
                    new Gene\Name('innmind/warden'),
                    new Path('/root')
                )
            ),
            Stream::of(
                Name::class,
                new Name('00'),
                new Name('02')
            ),
            new Path('.'),
            ''
        ),
        new Installation(
            new Name('02'),
            Stream::of(
                Gene::class,
                new Gene(
                    new Gene\Name('innmind/infrastructure-amqp'),
                    new Path('/root')
                ),
                new Gene(
                    new Gene\Name('innmind/warden'),
                    new Path('/root')
                )
            ),
            Stream::of(Name::class),
            new Path('.'),
            ''
        )
    );
};
