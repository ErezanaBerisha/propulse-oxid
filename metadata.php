<?php

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'solu1stathubs',
    'title' => [
        'de' => 'Stathubs - Umsatz in deiner App',
        'en' => 'StatHubs - Dashboard in your Mobile',
    ],
    'description' => [
        'de' => 'Erstellt Zugangsdaten für die StatHubs-App. Die Einstellungen zeigen Shop-URL, E-Mail, Passwort und einen QR-Code zum Verbinden.',
        'en' => 'Creates credentials for the StatHubs app. Settings show the shop URL, email, password and a QR code the mobile app can scan to connect.',
    ],
    'version' => '1.0.1',
    'author' => 'Profihost GmbH',
    'url' => 'https://stathubs.com/',
    'email' => 'info@profihost.com',
    'extend' => [],
    'controllers' => [
        'stathubs_credentials' => \Solu1\StatHubs\Controller\Admin\CredentialsController::class,
        'stathubs_api' => \Solu1\StatHubs\Controller\ApiController::class,
    ],
    'events' => [
        'onActivate' => \Solu1\StatHubs\Core\ModuleEvents::class . '::onActivate',
        'onDeactivate' => \Solu1\StatHubs\Core\ModuleEvents::class . '::onDeactivate',
    ],
    'settings' => [
        [
            'name' => 'solu1stathubs_shop_url',
            'type' => 'str',
            'value' => '',
        ],
        [
            'name' => 'solu1stathubs_email',
            'type' => 'str',
            'value' => '',
        ],
        [
            'name' => 'solu1stathubs_password',
            'type' => 'password',
            'value' => '',
        ],
    ],
];
