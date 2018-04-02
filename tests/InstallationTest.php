<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Installation,
    Installation\Name,
    Exception\InstallationMustExpressAtLeastOneGene,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\Stream;
use PHPUnit\Framework\TestCase;

class InstallationTest extends TestCase
{
    public function testInterface()
    {
        $installation = new Installation(
            $name = new Name('foo'),
            $genes = Stream::of('string', 'foo/bar'),
            $contacts = Stream::of(Name::class),
            $path = $this->createMock(PathInterface::class),
            'spark'
        );

        $this->assertSame($name, $installation->name());
        $this->assertSame($genes, $installation->genes());
        $this->assertSame($contacts, $installation->contacts());
        $this->assertSame($path, $installation->workingDirectory());
        $this->assertSame('spark', $installation->spark());
    }

    public function testThrowWhenInvalidGeneStream()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 2 must be of type StreamInterface<string>');

        new Installation(
            new Name('foo'),
            Stream::of('int', 42),
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
            Stream::of('string', 'foo/bar'),
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
            Stream::of('string'),
            Stream::of(Name::class),
            $this->createMock(PathInterface::class),
            'spark'
        );
    }

    public function testDependsOn()
    {
        $foo = new Installation(
            new Name('foo'),
            Stream::of('string', 'foo/bar'),
            Stream::of(Name::class, new Name('bar')),
            $this->createMock(PathInterface::class),
            'spark'
        );
        $bar = new Installation(
            new Name('bar'),
            Stream::of('string', 'foo/bar'),
            Stream::of(Name::class),
            $this->createMock(PathInterface::class),
            'spark'
        );

        $this->assertTrue($foo->dependsOn($bar));
        $this->assertFalse($foo->dependsOn($foo));
        $this->assertFalse($bar->dependsOn($bar));
        $this->assertFalse($bar->dependsOn($foo));
    }
}
