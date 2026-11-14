<?php

declare(strict_types=1);

namespace Solu1\StatHubs\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use Solu1\StatHubs\Core\Module;

final class CredentialProvisioner
{
    public function __construct(
        private readonly ModuleSettingServiceInterface $moduleSettingService
    ) {
    }

    public function ensure(): void
    {
        $email = $this->get(Module::SETTING_EMAIL);
        $password = $this->get(Module::SETTING_PASSWORD);

        if ($email === '' || $password === '') {
            $email = $this->generateEmail();
            $password = bin2hex(random_bytes(16));
            $this->moduleSettingService->saveString(Module::SETTING_EMAIL, $email, Module::ID);
            $this->moduleSettingService->saveString(Module::SETTING_PASSWORD, $password, Module::ID);
        }

        $this->syncShopUrl();
    }

    public function syncShopUrl(): void
    {
        $shopUrl = rtrim((string) Registry::getConfig()->getShopUrl(), '/');
        if ($shopUrl === '' || $this->get(Module::SETTING_SHOP_URL) === $shopUrl) {
            return;
        }

        $this->moduleSettingService->saveString(Module::SETTING_SHOP_URL, $shopUrl, Module::ID);
    }

    /**
     * @return array{shopUrl: string, email: string, password: string, platformType: string}
     */
    public function credentials(): array
    {
        $this->ensure();

        return [
            'shopUrl' => $this->get(Module::SETTING_SHOP_URL),
            'email' => $this->get(Module::SETTING_EMAIL),
            'password' => $this->get(Module::SETTING_PASSWORD),
            'platformType' => Module::PLATFORM_TYPE,
        ];
    }

    private function generateEmail(): string
    {
        $host = parse_url((string) Registry::getConfig()->getShopUrl(), PHP_URL_HOST);
        if (!is_string($host) || !str_contains($host, '.')) {
            $host = 'stathubs.local';
        }

        return 'stathubs-api@' . $host;
    }

    private function get(string $name): string
    {
        if (!$this->moduleSettingService->exists($name, Module::ID)) {
            return '';
        }

        return trim($this->moduleSettingService->getString($name, Module::ID)->toString());
    }
}
