<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Deploy,
    Deploy\Prerequisite,
    Loader\PHP,
    Exception\DeploymentFailed,
};
use Innmind\Ark\{
    Forge,
    Installation,
};
use Innmind\OperatingSystem\Remote;
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Process,
    Server\Process\ExitCode,
};
use Innmind\Url\{
    Url,
    Path,
};
use PHPUnit\Framework\TestCase;

class DeployTest extends TestCase
{
    public function testInvokation()
    {
        $deploy = new Deploy(
            $forge = $this->createMock(Forge::class),
            $remote = $this->createMock(Remote::class),
            $prerequisite = $this->createMock(Prerequisite::class)
        );
        $forge
            ->expects($this->at(0))
            ->method('new')
            ->willReturn($installation00 = new Installation(
                new Installation\Name('vps-00'),
                Url::fromString('http://vps-00/')
            ));
        $forge
            ->expects($this->at(1))
            ->method('new')
            ->willReturn($installation02 = new Installation(
                new Installation\Name('vps-02'),
                Url::fromString('http://vps-02/')
            ));
        $forge
            ->expects($this->at(2))
            ->method('new')
            ->willReturn($installation01 = new Installation(
                new Installation\Name('vps-01'),
                Url::fromString('http://vps-01/')
            ));
        $remote
            ->expects($this->at(0))
            ->method('ssh')
            ->with($installation00->location())
            ->willReturn($server = $this->createMock(Server::class));
        $remote
            ->expects($this->at(1))
            ->method('ssh')
            ->with($installation02->location())
            ->willReturn($server);
        $remote
            ->expects($this->at(2))
            ->method('ssh')
            ->with($installation01->location())
            ->willReturn($server);
        $server
            ->expects($this->any())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->atLeastOnce())
            ->method('execute')
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->any())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->any())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));

        $this->assertNull($deploy(
            (new PHP)(new Path('array.php'))
        ));
    }

    public function testDestroyEverythingWhenOneDeploymentFails()
    {
        $deploy = new Deploy(
            $forge = $this->createMock(Forge::class),
            $remote = $this->createMock(Remote::class),
            $prerequisite = $this->createMock(Prerequisite::class)
        );
        $forge
            ->expects($this->at(0))
            ->method('new')
            ->willReturn($installation00 = new Installation(
                new Installation\Name('vps-00'),
                Url::fromString('http://vps-00/')
            ));
        $forge
            ->expects($this->at(1))
            ->method('new')
            ->willReturn($installation02 = new Installation(
                new Installation\Name('vps-02'),
                Url::fromString('http://vps-02/')
            ));
        $forge
            ->expects($this->at(2))
            ->method('dispose')
            ->with($installation02);
        $forge
            ->expects($this->at(3))
            ->method('dispose')
            ->with($installation00);
        $remote
            ->expects($this->at(0))
            ->method('ssh')
            ->with($installation00->location())
            ->willReturn($server00 = $this->createMock(Server::class));
        $remote
            ->expects($this->at(1))
            ->method('ssh')
            ->with($installation02->location())
            ->willReturn($server02 = $this->createMock(Server::class));
        $server00
            ->expects($this->any())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->atLeastOnce())
            ->method('execute')
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->any())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->any())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $server02
            ->expects($this->any())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->atLeastOnce())
            ->method('execute')
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->any())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->any())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $this->expectException(DeploymentFailed::class);

        $deploy(
            (new PHP)(new Path('array.php'))
        );
    }
}
