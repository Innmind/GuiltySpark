<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Deploy;

use Innmind\Server\Control\Server;

interface Prerequisite
{
    public function __invoke(Server $server): void;
}
