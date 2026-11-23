<?php

declare(strict_types=1);

namespace Solu1\ProPulse\Core;

use Solu1\ProPulse\Service\CredentialProvisioner;

final class ModuleEvents
{
    public static function onActivate(): void
    {
        CredentialProvisioner::create()->ensure();
    }

    public static function onDeactivate(): void
    {
    }
}
