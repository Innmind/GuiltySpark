<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark\Deploy\Prerequisite;

use Innmind\GuiltySpark\Deploy\Prerequisite;
use Innmind\Server\Control\{
    Server,
    Server\Script,
};

final class Composer implements Prerequisite
{
    private $script;

    public function __construct()
    {
        $this->script = Script::of(
            'php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"',
            # hash can't be verified automatically
            'php composer-setup.php',
            'php -r "unlink(\'composer-setup.php\');"',
            'mv composer.phar /usr/bin/composer',
            'echo \'export PATH=~/.composer/vendor/bin:$PATH\' >> ~/.profile'
        );
    }

    public function __invoke(Server $server): void
    {
        ($this->script)($server);
    }
}
