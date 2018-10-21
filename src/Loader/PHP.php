<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Loader;

use Innmind\GuiltySpark\{
    Loader,
    InstallationArray,
};
use Innmind\Url\PathInterface;

final class PHP implements Loader
{
    public function __invoke(PathInterface $path): InstallationArray
    {
        $load = require (string) $path;

        return $load();
    }
}
