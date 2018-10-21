<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Installation;

use Innmind\Url\PathInterface;

final class Gene
{
    private $name;
    private $directory;

    public function __construct(Gene\Name $name, PathInterface $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
    }

    public function name(): Gene\Name
    {
        return $this->name;
    }

    public function directory(): PathInterface
    {
        return $this->directory;
    }
}
