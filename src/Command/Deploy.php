<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Command;

use Innmind\GuiltySpark\{
    InstallationArray,
    Deploy as DoDepoy,
};
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};

final class Deploy implements Command
{
    private $array;
    private $deploy;

    public function __construct(
        InstallationArray $array,
        DoDepoy $deploy
    ) {
        $this->array = $array;
        $this->deploy = $deploy;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        ($this->deploy)($this->array);
    }

    public function __toString(): string
    {
        return <<<USAGE
deploy

Deploy the installations array
USAGE;
    }
}
