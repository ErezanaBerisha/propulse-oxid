<?php

declare(strict_types=1);

namespace Solu1\StatHubs\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use Solu1\StatHubs\Core\Module;

/**
 * Read-only order feed for the StatHubs backend.
 * Authenticate with the email and password shown in the module settings.
 */
class ApiController extends FrontendController
{
    public function ping(): void
    {
        if (!$this->authorized()) {
            $this->json(['ok' => false], 401);
        }

        $this->json(['ok' => true]);
    }

    public function orders(): void
    {
        if (!$this->authorized()) {
            $this->json(['ok' => false], 401);
        }

        $start = max(0, (int) Registry::getRequest()->getRequestParameter('start'));
        $count = (int) Registry::getRequest()->getRequestParameter('count');
        if ($count < 1) {
            $count = 50;
        }
        $count = min(100, $count);

        $sql = 'SELECT OXID, OXORDERNR, OXORDERDATE, OXTOTALNETSUM, OXTOTALORDERSUM, OXPAYMENTTYPE,
                       OXFOLDER, OXTRANSSTATUS, OXSENDDATE, OXPAID, OXTIMESTAMP, OXSTORNO
                FROM oxorder
                WHERE OXSHOPID = ?';
        $params = [(int) Registry::getConfig()->getShopId()];

        $since = trim((string) Registry::getRequest()->getRequestParameter('since'));
        if ($since !== '' && preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}$/', $since) === 1) {
            $sql .= ' AND OXTIMESTAMP >= ?';
            $params[] = $since;
        }

        $sql .= ' ORDER BY OXORDERDATE DESC LIMIT ' . $count . ' OFFSET ' . $start;

        $rows = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql, $params);
        $orders = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $order = [];
            foreach ($row as $column => $value) {
                $order[strtoupper((string) $column)] = is_scalar($value) ? (string) $value : '';
            }
            $orders[] = $order;
        }

        $this->json(['ok' => true, 'orders' => $orders]);
    }

    public function render()
    {
        $this->json(['ok' => false], 404);

        return '';
    }

    private function authorized(): bool
    {
        [$email, $password] = $this->presentedCredentials();

        return $this->same($this->setting(Module::SETTING_EMAIL), $email)
            && $this->same($this->setting(Module::SETTING_PASSWORD), $password);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function presentedCredentials(): array
    {
        $email = (string) ($_SERVER['PHP_AUTH_USER'] ?? '');
        $password = (string) ($_SERVER['PHP_AUTH_PW'] ?? '');
        if ($email !== '' || $password !== '') {
            return [$email, $password];
        }

        $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        if (str_starts_with($header, 'Basic ')) {
            $decoded = base64_decode(substr($header, 6), true);
            if (is_string($decoded) && str_contains($decoded, ':')) {
                return explode(':', $decoded, 2);
            }
        }

        return [
            (string) ($_SERVER['HTTP_X_STATHUBS_EMAIL'] ?? ''),
            (string) ($_SERVER['HTTP_X_STATHUBS_PASSWORD'] ?? ''),
        ];
    }

    private function setting(string $name): string
    {
        $settings = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettingServiceInterface::class);

        if (!$settings->exists($name, Module::ID)) {
            return '';
        }

        return trim($settings->getString($name, Module::ID)->toString());
    }

    private function same(string $known, string $given): bool
    {
        if ($known === '' || strlen($known) !== strlen($given)) {
            return false;
        }

        return hash_equals($known, $given);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function json(array $payload, int $status = 200): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=UTF-8');
        }

        echo json_encode($payload);
        exit;
    }
}
