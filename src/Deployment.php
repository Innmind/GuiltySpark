<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark;

use Innmind\GuiltySpark\Installation\Name;
use Innmind\Ark\Installation;
use Innmind\Immutable\Map;

final class Deployment
{
    private $installations;

    public function __construct()
    {
        $this->installations = new Map('string', Installation::class);
    }

    public function deployed(Name $name, Installation $installation): void
    {
        $this->installations = $this->installations->put(
            (string) $name,
            $installation
        );
    }

    public function get(Name $name): Installation
    {
        return $this->installations->get((string) $name);
    }
}
