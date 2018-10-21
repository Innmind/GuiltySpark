<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Installation,
    Installation\Name,
    Installation\Gene,
    Loader\PHP,
    Exception\InstallationMustExpressAtLeastOneGene,
};
use Innmind\Url\{
    PathInterface,
    Path,
};
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Command,
    Server\Process,
    Server\Process\ExitCode,
};
use Innmind\Immutable\Stream;
use PHPUnit\Framework\TestCase;

class InstallationTest extends TestCase
{
    public function testInterface()
    {
        $installation = new Installation(
            $name = new Name('foo'),
            Stream::of(
                Gene::class,
                new Gene(
                    new Gene\Name('foo/bar'),
                    $this->createMock(PathInterface::class)
                )
            ),
            $contacts = Stream::of(Name::class),
            $path = $this->createMock(PathInterface::class),
            'spark'
        );

        $this->assertSame($name, $installation->name());
        $this->assertSame($contacts, $installation->contacts());
        $this->assertSame($path, $installation->workingDirectory());
        $this->assertSame('spark', $installation->spark());
    }

    public function testThrowWhenInvalidGeneStream()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 2 must be of type StreamInterface<Innmind\GuiltySpark\Installation\Gene>');

        new Installation(
            new Name('foo'),
            Stream::of('int'),
            Stream::of(Name::class),
            $this->createMock(PathInterface::class),
            'spark'
        );
    }

    public function testThrowWhenInvalidContactStream()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 3 must be of type StreamInterface<Innmind\GuiltySpark\Installation\Name>');

        new Installation(
            new Name('foo'),
            Stream::of(Gene::class),
            Stream::of('string'),
            $this->createMock(PathInterface::class),
            'spark'
        );
    }

    public function testThrowWhenEmptyGeneStream()
    {
        $this->expectException(InstallationMustExpressAtLeastOneGene::class);
        $this->expectExceptionMessage('foo');

        new Installation(
            new Name('foo'),
            Stream::of(Gene::class),
            Stream::of(Name::class),
            $this->createMock(PathInterface::class),
            'spark'
        );
    }

    public function testDependsOn()
    {
        $foo = new Installation(
            new Name('foo'),
            Stream::of(
                Gene::class,
                new Gene(
                    new Gene\Name('foo/bar'),
                    $this->createMock(PathInterface::class)
                )
            ),
            Stream::of(Name::class, new Name('bar')),
            $this->createMock(PathInterface::class),
            'spark'
        );
        $bar = new Installation(
            new Name('bar'),
            Stream::of(
                Gene::class,
                new Gene(
                    new Gene\Name('foo/bar'),
                    $this->createMock(PathInterface::class)
                )
            ),
            Stream::of(Name::class),
            $this->createMock(PathInterface::class),
            'spark'
        );

        $this->assertTrue($foo->dependsOn($bar));
        $this->assertFalse($foo->dependsOn($foo));
        $this->assertFalse($bar->dependsOn($bar));
        $this->assertFalse($bar->dependsOn($foo));
    }

    public function testExpressOn()
    {
        $installation = (new PHP)(new Path('array.php'))->current();
        $server = $this->createMock(Server::class);
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));

        $genes = [
            'innmind/infrastructure-neo4j',
            'innmind/library',
            'innmind/infrastructure-nginx',
            'innmind/warden',
        ];

        foreach ($genes as $i => $gene) {
            $processes
                ->expects($this->at($i))
                ->method('execute')
                ->with(
                    Command::foreground('genome')
                        ->withArgument('express')
                        ->withArgument($gene)
                        ->withArgument('/root')
                )
                ->willReturn($process = $this->createMock(Process::class));
            $process
                ->expects($this->once())
                ->method('wait')
                ->will($this->returnSelf());
            $process
                ->expects($this->once())
                ->method('exitCode')
                ->willReturn(new ExitCode(0));
        }

        $this->assertNull($installation->expressOn($server));
    }
}
