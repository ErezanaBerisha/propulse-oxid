<?php

declare(strict_types=1);

namespace Solu1\ProPulse\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use Solu1\ProPulse\Service\CredentialProvisioner;

class CredentialsController extends AdminController
{
    public function render()
    {
        parent::render();

        $credentials = CredentialProvisioner::create()->credentials();
        $assets = dirname(__DIR__, 3) . '/assets/src';

        $this->addTplParam('propulseShopUrl', $credentials['shopUrl']);
        $this->addTplParam('propulseEmail', $credentials['email']);
        $this->addTplParam('propulsePassword', $credentials['password']);
        $this->addTplParam('propulsePlatformType', $credentials['platformType']);
        $this->addTplParam('propulseCss', (string) file_get_contents($assets . '/css/admin.css'));
        $this->addTplParam('propulseQrJs', (string) file_get_contents($assets . '/js/qrcode-generator.js'));
        $this->addTplParam('propulseAdminJs', (string) file_get_contents($assets . '/js/admin.js'));

        return 'propulse_credentials.tpl';
    }
}
