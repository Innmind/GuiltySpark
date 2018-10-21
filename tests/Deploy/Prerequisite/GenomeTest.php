<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Deploy\Prerequisite;

use Innmind\GuiltySpark\{
    Deploy\Prerequisite\Genome,
    Deploy\Prerequisite,
};
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Command,
    Server\Process,
    Server\Process\ExitCode,
};
use PHPUnit\Framework\TestCase;

class GenomeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Prerequisite::class, new Genome);
    }

    public function testInvokation()
    {
        $install = new Genome;
        $server = $this->createMock(Server::class);
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with(Command::foreground('composer global require innmind/genome'))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->any())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));

        $this->assertNull($install($server));
    }
}
