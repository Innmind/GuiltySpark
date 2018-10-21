<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Loader;

use Innmind\GuiltySpark\{
    Loader\PHP,
    Loader,
    InstallationArray,
};
use Innmind\Url\Path;
use PHPUnit\Framework\TestCase;

class PHPTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Loader::class, new PHP);
    }

    public function testInvokation()
    {
        $array = (new PHP)(new Path('array.php'));

        $this->assertInstanceOf(InstallationArray::class, $array);
        $this->assertSame('00', (string) $array->key());
        $array->next();
        $this->assertSame('02', (string) $array->key());
        $array->next();
        $this->assertSame('01', (string) $array->key());
    }
}
