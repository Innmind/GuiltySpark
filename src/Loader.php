<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark;

use Innmind\Url\PathInterface;

interface Loader
{
    public function __invoke(PathInterface $path): InstallationArray;
}
