<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Command;

use Innmind\GuiltySpark\{
    Command\Deploy,
    InstallationArray,
    Deploy as DoDeploy,
};
use Innmind\Ark\Forge;
use Innmind\OperatingSystem\Remote;
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};
use PHPUnit\Framework\TestCase;

class DeployTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Command::class,
            new Deploy(
                new InstallationArray,
                new DoDeploy(
                    $this->createMock(Forge::class),
                    $this->createMock(Remote::class)
                )
            )
        );
    }

    public function testInvokation()
    {
        $deploy = new Deploy(
            new InstallationArray,
            new DoDeploy(
                $this->createMock(Forge::class),
                $this->createMock(Remote::class)
            )
        );

        $this->assertNull($deploy(
            $this->createMock(Environment::class),
            new Arguments,
            new Options
        ));
    }
}
