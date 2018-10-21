<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Deploy\Prerequisite;

use Innmind\GuiltySpark\{
    Deploy\Prerequisite\Composer,
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

class ComposerTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Prerequisite::class, new Composer);
    }

    public function testInvokation()
    {
        $install = new Composer;
        $server = $this->createMock(Server::class);
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));

        $commands = [
            'php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"',
            'php composer-setup.php',
            'php -r "unlink(\'composer-setup.php\');"',
            'mv composer.phar /usr/bin/composer',
            'echo \'export PATH=~/.composer/vendor/bin:$PATH\' >> ~/.profile',
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
