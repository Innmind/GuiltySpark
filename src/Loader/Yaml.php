<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Loader;

use Innmind\GuiltySpark\{
    Loader,
    InstallationArray,
    Installation,
    Installation\Name,
};
use Innmind\Url\{
    PathInterface,
    Path,
};
use Innmind\Config\Config;
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};
use Symfony\Component\Yaml\Yaml as Parser;

final class Yaml implements Loader
{
    public function __invoke(PathInterface $path): InstallationArray
    {
        $structure = (new Config)->build(Parser::parseFile(__DIR__.'/../../schema.yml'));
        $config = $structure->process(Parser::parseFile((string) $path));
        $installations = [];

        foreach ($config['installations'] as $name => $value) {
            $installations[] = new Installation(
                new Name($name),
                $value['express'],
                $value['contacts']->reduce(
                    Stream::of(Name::class),
                    static function(StreamInterface $names, string $name): StreamInterface {
                        return $names->add(new Name($name));
                    }
                ),
                new Path($value['workingDirectory'] ?? '.'),
                $value['spark']
            );
        }

        return new InstallationArray(...$installations);
    }
}
