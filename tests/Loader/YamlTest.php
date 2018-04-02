<?php
declare(strict_types = 1);

namespace Tests\Innmind\GuiltySpark\Loader;

use Innmind\GuiltySpark\{
    Loader\Yaml,
    Loader,
    InstallationArray,
};
use Innmind\Url\Path;
use PHPUnit\Framework\TestCase;

class YamlTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Loader::class, new Yaml);
    }

    public function testInvokation()
    {
        $array = (new Yaml)(new Path('array.yml'));

        $this->assertInstanceOf(InstallationArray::class, $array);
        $this->assertSame('00', (string) $array->key());
        $array->next();
        $this->assertSame('02', (string) $array->key());
        $array->next();
        $this->assertSame('01', (string) $array->key());
    }
}
