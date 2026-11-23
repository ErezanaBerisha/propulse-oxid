<?php

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'solu1propulse',
    'title' => [
        'de' => 'proPulse - Umsatz in deiner App',
        'en' => 'proPulse - Dashboard in your Mobile',
    ],
    'description' => [
        'de' => 'Erstellt Zugangsdaten für die proPulse-App. Die Einstellungen zeigen Shop-URL, E-Mail, Passwort und einen QR-Code zum Verbinden.',
        'en' => 'Creates credentials for the proPulse app. Settings show the shop URL, email, password and a QR code the mobile app can scan to connect.',
    ],
    'version' => '1.9.0',
    'author' => 'Profihost GmbH',
    'url' => 'https://stathubs.com/',
    'email' => 'info@profihost.com',
    'extend' => [],
    'controllers' => [
        'propulse_credentials' => \Solu1\ProPulse\Controller\Admin\CredentialsController::class,
        'propulse_api' => \Solu1\ProPulse\Controller\ApiController::class,
        'stathubs_api' => \Solu1\ProPulse\Controller\ApiController::class,
    ],
    'templates' => [
        'propulse_credentials.tpl' => 'solu1/propulse/views/admin/tpl/propulse_credentials.tpl',
    ],
    'events' => [
        'onActivate' => \Solu1\ProPulse\Core\ModuleEvents::class . '::onActivate',
        'onDeactivate' => \Solu1\ProPulse\Core\ModuleEvents::class . '::onDeactivate',
    ],
    'settings' => [
        [
            'name' => 'solu1propulse_shop_url',
            'type' => 'str',
            'value' => '',
        ],
        [
            'name' => 'solu1propulse_email',
            'type' => 'str',
            'value' => '',
        ],
        [
            'name' => 'solu1propulse_password',
            'type' => 'password',
            'value' => '',
        ],
    ],
];
