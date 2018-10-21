<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Deploy\Prerequisite;

use Innmind\GuiltySpark\{
    Deploy\Prerequisite\PHP,
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

class PHPTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Prerequisite::class, new PHP);
    }

    public function testInvokation()
    {
        $install = new PHP;
        $server = $this->createMock(Server::class);
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));

        $commands = [
            'apt-get install apt-transport-https lsb-release ca-certificates -y',
            'wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg',
            'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list',
            'apt-get update',
            'apt-get install git php7.2 php7.2-fpm php7.2-cli php7.2-json php7.2-xml php7.2-intl php7.2-mbstring php7.2-curl php7.2-zip php7.2-gd php7.2-bcmath -y',
        ];

        foreach ($commands as $i => $command) {
            $processes
                ->expects($this->at($i))
                ->method('execute')
                ->with(Command::foreground($command))
                ->willReturn($process = $this->createMock(Process::class));
            $process
                ->expects($this->once())
                ->method('wait')
                ->will($this->returnSelf());
            $process
                ->expects($this->any())
                ->method('exitCode')
                ->willReturn(new ExitCode(0));
        }

        $this->assertNull($install($server));
    }
}
