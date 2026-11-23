<?php

declare(strict_types=1);

namespace Solu1\ProPulse\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use Solu1\ProPulse\Core\Module;
use Throwable;

final class CredentialProvisioner
{
    private $moduleSettingBridge;

    public function __construct(ModuleSettingBridgeInterface $moduleSettingBridge)
    {
        $this->moduleSettingBridge = $moduleSettingBridge;
    }

    public static function create(): self
    {
        return new self(
            ContainerFactory::getInstance()
                ->getContainer()
                ->get(ModuleSettingBridgeInterface::class)
        );
    }

    public function ensure(): void
    {
        $email = $this->get(Module::SETTING_EMAIL);
        $password = $this->get(Module::SETTING_PASSWORD);

        if ($email === '' || $password === '') {
            $email = $this->generateEmail();
            $password = bin2hex(random_bytes(16));
            $this->moduleSettingBridge->save(Module::SETTING_EMAIL, $email, Module::ID);
            $this->moduleSettingBridge->save(Module::SETTING_PASSWORD, $password, Module::ID);
        }

        $this->syncShopUrl();
    }

    public function syncShopUrl(): void
    {
        $shopUrl = rtrim((string) Registry::getConfig()->getShopUrl(), '/');
        if ($shopUrl === '' || $this->get(Module::SETTING_SHOP_URL) === $shopUrl) {
            return;
        }

        $this->moduleSettingBridge->save(Module::SETTING_SHOP_URL, $shopUrl, Module::ID);
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

    public function get(string $name): string
    {
        try {
            $value = $this->moduleSettingBridge->get($name, Module::ID);
        } catch (Throwable $e) {
            return '';
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function generateEmail(): string
    {
        $host = parse_url((string) Registry::getConfig()->getShopUrl(), PHP_URL_HOST);
        if (!is_string($host) || strpos($host, '.') === false) {
            $host = 'propulse.local';
        }

        return 'propulse-api@' . $host;
    }
}
