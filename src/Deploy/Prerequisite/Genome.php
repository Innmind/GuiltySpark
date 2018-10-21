<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Deploy\Prerequisite;

use Innmind\GuiltySpark\Deploy\Prerequisite;
use Innmind\Server\Control\{
    Server,
    Server\Script,
};

final class Genome implements Prerequisite
{
    private $script;

    public function __construct()
    {
        $this->script = Script::of('composer global require innmind/genome');
    }

    public function __invoke(Server $server): void
    {
        ($this->script)($server);
    }
}
