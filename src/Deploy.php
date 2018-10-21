<?php
declare(strict_types = 1);

namespace Innmind\GuiltySpark;

use Innmind\GuiltySpark\{
    Deploy\Prerequisite,
    Exception\DeploymentFailed,
};
use Innmind\Ark\{
    Forge,
    Installation as Deployed,
};
use Innmind\OperatingSystem\Remote;
use Innmind\Immutable\Stream;

final class Deploy
{
    private $forge;
    private $server;

    public function __construct(
        Forge $forge,
        Remote $remote,
        Prerequisite ...$prerequisites
    ) {
        $this->forge = $forge;
        $this->remote = $remote;
        $this->prerequisites = Stream::of(Prerequisite::class, ...$prerequisites);
    }

    public function __invoke(InstallationArray $array): void
    {
        $deployment = new Deployment;

        try {
            foreach ($array as $installation) {
                $deployment->deployed(
                    $installation->name(),
                    $this->deploy($installation, $deployment)
                );
            }
        } catch (\Throwable $e) {
            $deployment
                ->installations()
                ->foreach(function(Deployed $installation): void {
                    $this->forge->dispose($installation);
                });

            throw new DeploymentFailed('', 0, $e);
        }
    }

    private function deploy(
        Installation $specification,
        Deployment $deployment
    ): Deployed {
        $installation = $this->forge->new();
        $server = $this->remote->ssh($installation->location());

        try {
            $this->prerequisites->foreach(static function(Prerequisite $installOn) use ($server): void {
                $installOn($server);
            });

            $specification->expressOn($server);
            $specification->deployTowerOn($server, $deployment);
        } catch (\Throwable $e) {
            $this->forge->dispose($installation);

            throw $e;
        }

        return $installation;
    }
}
