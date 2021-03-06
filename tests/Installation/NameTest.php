<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Installation;

use Innmind\GuiltySpark\{
    Installation\Name,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;
use Eris\{
    TestTrait,
    Generator,
};

class NameTest extends TestCase
{
    use TestTrait;

    public function testInterface()
    {
        $this
            ->forAll(Generator\string())
            ->when(static function(string $string): bool {
                return $string !== '';
            })
            ->then(function(string $string): void {
                $this->assertSame($string, (string) new Name($string));
            });
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(DomainException::class);

        new Name('');
    }
}
