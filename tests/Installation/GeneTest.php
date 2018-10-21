<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Installation;

use Innmind\GuiltySpark\{
    Installation\Gene,
    Installation\Gene\Name,
};
use Innmind\Url\PathInterface;
use PHPUnit\Framework\TestCase;

class GeneTest extends TestCase
{
    public function testInterface()
    {
        $gene = new Gene(
            $name = new Name('foo/bar'),
            $directory = $this->createMock(PathInterface::class)
        );

        $this->assertSame($name, $gene->name());
        $this->assertSame($directory, $gene->directory());
    }
}
