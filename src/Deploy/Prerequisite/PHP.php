<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Deploy\Prerequisite;

use Innmind\GuiltySpark\Deploy\Prerequisite;
use Innmind\Server\Control\{
    Server,
    Server\Script,
};

final class PHP implements Prerequisite
{
    private $script;

    public function __construct()
    {
        $this->script = Script::of(
            'apt-get install apt-transport-https lsb-release ca-certificates -y',
            'wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg',
            'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list',
            'apt-get update',
            'apt-get install git php7.2 php7.2-fpm php7.2-cli php7.2-json php7.2-xml php7.2-intl php7.2-mbstring php7.2-curl php7.2-zip php7.2-gd php7.2-bcmath -y'
        );
    }

    public function __invoke(Server $server): void
    {
        ($this->script)($server);
    }
}
