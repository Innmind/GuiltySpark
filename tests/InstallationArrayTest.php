<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    InstallationArray,
    Installation,
    Installation\Name,
    Installation\Gene,
    Exception\UnknownInstallations,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\Stream;
use PHPUnit\Framework\TestCase;

class InstallationArrayTest extends TestCase
{
    public function testInterface()
    {
        $array = new InstallationArray(
            $foo = new Installation(
                new Name('foo'),
                Stream::of(
                    Gene::class,
                    new Gene(
                        new Gene\Name('foo/bar'),
                        $this->createMock(PathInterface::class)
                    )
                ),
                Stream::of(Name::class, new Name('baz')),
                $this->createMock(PathInterface::class),
                'spark'
            ),
            $bar = new Installation(
                new Name('bar'),
                Stream::of(
                    Gene::class,
                    new Gene(
                        new Gene\Name('foo/bar'),
                        $this->createMock(PathInterface::class)
                    )
                ),
                Stream::of(Name::class, new Name('baz'), new Name('foo')),
                $this->createMock(PathInterface::class),
                'spark'
            ),
            $baz = new Installation(
                new Name('baz'),
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
            )
        );

        $this->assertSame($baz, $array->current());
        $this->assertSame($baz->name(), $array->key());
        $this->assertTrue($array->valid());
        $this->assertNull($array->next());
        $this->assertSame($foo, $array->current());
        $this->assertSame($foo->name(), $array->key());
        $this->assertTrue($array->valid());
        $this->assertNull($array->next());
        $this->assertSame($bar, $array->current());
        $this->assertSame($bar->name(), $array->key());
        $this->assertTrue($array->valid());
        $this->assertNull($array->next());
        $this->assertFalse($array->valid());
        $this->assertNull($array->rewind());
        $this->assertSame($baz->name(), $array->key());
    }

    public function testThrowWhenUnknownContactInstallations()
    {
        $this->expectException(UnknownInstallations::class);
        $this->expectExceptionMessage('foo, bar');

        new InstallationArray(
            new Installation(
                new Name('baz'),
                Stream::of(
                    Gene::class,
                    new Gene(
                        new Gene\Name('foo/bar'),
                        $this->createMock(PathInterface::class)
                    )
                ),
                Stream::of(Name::class, new Name('foo'), new Name('bar')),
                $this->createMock(PathInterface::class),
                'spark'
            ),
            new Installation(
                new Name('foobar'),
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
            )
        );
    }
}
