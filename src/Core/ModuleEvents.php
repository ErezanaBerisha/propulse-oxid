<?php

declare(strict_types=1);

namespace Solu1\StatHubs\Core;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Solu1\StatHubs\Service\CredentialProvisioner;

final class ModuleEvents
{
    public static function onActivate(): void
    {
        ContainerFactory::getInstance()
            ->getContainer()
            ->get(CredentialProvisioner::class)
            ->ensure();
    }

    public static function onDeactivate(): void
    {
    }
}
