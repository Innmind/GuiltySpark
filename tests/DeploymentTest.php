<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Deployment,
    Installation\Name,
};
use Innmind\Ark\Installation;
use Innmind\Url\UrlInterface;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class DeploymentTest extends TestCase
{
    public function testInterface()
    {
        $deployment = new Deployment;

        $this->assertNull($deployment->deployed(
            new Name('foo'),
            $installation = new Installation(
                new Installation\Name('vps-foo'),
                $this->createMock(UrlInterface::class)
            )
        ));
        $this->assertSame($installation, $deployment->get(new Name('foo')));
        $this->assertInstanceOf(SetInterface::class, $deployment->installations());
        $this->assertSame(Installation::class, (string) $deployment->installations()->type());
        $this->assertCount(1, $deployment->installations());
        $this->assertSame([$installation], $deployment->installations()->toPrimitive());

        $this->expectException(\InvalidArgumentException::class);

        $deployment->get(new Name('bar'));
    }
}
