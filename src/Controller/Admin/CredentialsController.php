<?php

declare(strict_types=1);

namespace Solu1\StatHubs\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Solu1\StatHubs\Service\CredentialProvisioner;

class CredentialsController extends AdminController
{
    public function render()
    {
        parent::render();

        $credentials = ContainerFactory::getInstance()
            ->getContainer()
            ->get(CredentialProvisioner::class)
            ->credentials();
        $assets = dirname(__DIR__, 3) . '/assets/src';

        $this->addTplParam('stathubsShopUrl', $credentials['shopUrl']);
        $this->addTplParam('stathubsEmail', $credentials['email']);
        $this->addTplParam('stathubsPassword', $credentials['password']);
        $this->addTplParam('stathubsPlatformType', $credentials['platformType']);
        $this->addTplParam('stathubsCss', (string) file_get_contents($assets . '/css/admin.css'));
        $this->addTplParam('stathubsQrJs', (string) file_get_contents($assets . '/js/qrcode-generator.js'));
        $this->addTplParam('stathubsAdminJs', (string) file_get_contents($assets . '/js/admin.js'));

        return '@solu1stathubs/credentials.html.twig';
    }
}
