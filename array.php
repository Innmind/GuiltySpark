<?php
declare(strict_types = 1);

use Innmind\GuiltySpark\{
    InstallationArray,
    Installation,
    Installation\Name,
};
use Innmind\Url\Path;
use Innmind\Immutable\Stream;

return function(): InstallationArray {
    return new InstallationArray(
        new Installation(
            new Name('00'),
            Stream::of(
                'string',
                'innmind/infrastructure-neo4j',
                'innmind/library',
                'innmind/infrastructure-nginx',
                'innmind/warden'
            ),
            Stream::of(Name::class),
            new Path('.'),
            ''
        ),
        new Installation(
            new Name('01'),
            Stream::of('string', 'innmind/crawler-app', 'innmind/warden'),
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
            Stream::of('string', 'innmind/infrastructure-amqp', 'innmind/warden'),
            Stream::of(Name::class),
            new Path('.'),
            ''
        )
    );
};
