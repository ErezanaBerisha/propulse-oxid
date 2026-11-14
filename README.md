# StatHubs for OXID eShop

Connect your OXID eShop to [StatHubs](https://stathubs.com/) and see your sales in the StatHubs mobile app.

## What the module does

- When you activate it, the module creates StatHubs connection credentials (an email-style login and a random password) for your shop.
- Adds **Extensions → StatHubs** to the OXID admin. That page shows the shop URL, login email, password and a QR code. Scan the QR code with the StatHubs app to connect the shop.
- Provides a small read-only endpoint, `index.php?cl=stathubs_api`, that returns order data to StatHubs after it checks those credentials.

The module adds no cron jobs, no frontend output, no changes to existing shop classes and no outgoing network calls.

## Compatibility

| | |
|---|---|
| Tested on | OXID eShop Community Edition 7.5.2 (metapackage 7.5.1) with the Twig admin theme, PHP 8.3 |
| Declared requirement | OXID eShop 7.5.x (`oxid-esales/oxideshop-ce` ^7.5), PHP 8.3 or later |
| Not supported | OXID eShop 6.x and earlier (Smarty admin) |

Professional and Enterprise Editions and sub-shops have not been tested yet.

## Requirements

- Shell (SSH) access to the server. If you don't have it, send this page to your developer or hosting provider.
- The shop must be reachable over **HTTPS** from the internet. The StatHubs servers call `https://<your-shop>/index.php?cl=stathubs_api`.
- That URL must not be blocked by a firewall, WAF or basic-auth protection for the StatHubs servers.

## Installation

Run these commands in the shop root directory (the folder that contains `composer.json`, `source/` and `vendor/`), as the user that owns the shop files:

```sh
composer require stathubs/oxid
vendor/bin/oe-console oe:module:activate solu1stathubs
```

That's it. Continue with [Connect the shop to StatHubs](#connect-the-shop-to-stathubs).

- Instead of the second command, you can activate the module in the admin under **Extensions → Modules → StatHubs → Activate**.
- For a sub-shop, add `--shop-id=<id>` to the activate command.
- OXID eShop cannot install modules from a ZIP uploaded in the admin panel.

### Without Composer (ZIP)

1. Download `stathubs-oxid.zip` from the [Releases](../../releases) page.
2. Extract it into `source/modules/solu1/stathubs`:

   ```sh
   mkdir -p source/modules/solu1/stathubs
   unzip stathubs-oxid.zip -d source/modules/solu1/stathubs
   ```

   `source/modules/solu1/stathubs/metadata.php` must exist afterwards.
3. Add the module's namespace to the `autoload` section of the shop's root `composer.json`:

   ```json
   "autoload": {
       "psr-4": {
           "Solu1\\StatHubs\\": "./source/modules/solu1/stathubs/src/"
       }
   }
   ```

4. Register and activate the module:

   ```sh
   composer dump-autoload
   vendor/bin/oe-console oe:module:install source/modules/solu1/stathubs
   vendor/bin/oe-console oe:module:activate solu1stathubs
   ```

## Connect the shop to StatHubs

1. In the OXID admin, open **Extensions → StatHubs**.
2. In the StatHubs app, add a new shop and scan the QR code on that page.
   Or enter the shop URL, email and password shown on the page by hand.
3. StatHubs imports your orders. The first sync can take a few minutes on large shops.

Check that the shop URL on that page starts with `https://`. If it doesn't, fix the shop URL in your shop configuration (`source/config.inc.php`, `sShopURL`/`sSSLShopURL`) before you connect.

## Permissions and data access

The StatHubs credentials only work for the module's own endpoint. They are not an OXID admin or customer account, and they cannot sign in to the shop or the admin.

The endpoint is read-only. It has two actions:

| Action | Returns |
|---|---|
| `fnc=ping` | `{"ok": true}` if the credentials are valid |
| `fnc=orders` | Orders of the current shop, at most 100 per request |

Each order contains only these fields from `oxorder`: `OXID`, `OXORDERNR`, `OXORDERDATE`, `OXTOTALNETSUM`, `OXTOTALORDERSUM`, `OXPAYMENTTYPE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXSENDDATE`, `OXPAID`, `OXTIMESTAMP`, `OXSTORNO`.

**No customer data** (names, email addresses, addresses), no order items and no product data are sent.

## Credentials and security

- The password is a random 32-character hexadecimal string, generated on your shop when you activate the module. The package contains no credentials or API keys.
- OXID stores module settings in the shop's configuration files. The credentials are in `var/configuration/shops/<shop-id>/modules/solu1stathubs.yaml`. Keep the `var/` directory out of version control and public web access, as you would for the rest of the shop configuration.
- The password is shown in plain text on **Extensions → StatHubs**, so that it can be scanned or copied. Restrict admin access to trusted users.
- Credentials are compared in constant time. Requests without valid credentials get HTTP 401.
- **Deactivating the module turns off the endpoint immediately.** StatHubs can no longer read data.

### Rotate the password

The module has no button to create a new password yet. To get new credentials, remove the module configuration and install it again:

```sh
vendor/bin/oe-console oe:module:deactivate solu1stathubs
vendor/bin/oe-console oe:module:uninstall solu1stathubs
vendor/bin/oe-console oe:module:install vendor/stathubs/oxid
vendor/bin/oe-console oe:module:activate solu1stathubs
vendor/bin/oe-console oe:cache:clear
```

If you installed from the ZIP, use `source/modules/solu1/stathubs` instead of `vendor/stathubs/oxid`.

Then reconnect the shop in the StatHubs app with the new QR code.

## Update

**Installed with Composer:**

```sh
composer update stathubs/oxid
vendor/bin/oe-console oe:cache:clear
```

**Installed from the ZIP:** download the new ZIP, replace the contents of `source/modules/solu1/stathubs` with it, then run `composer dump-autoload` and `vendor/bin/oe-console oe:cache:clear`.

Your existing StatHubs credentials are kept.

## Uninstall

First deactivate the module (**Extensions → Modules → StatHubs → Deactivate**, or `vendor/bin/oe-console oe:module:deactivate solu1stathubs`).

**Installed with Composer:**

```sh
composer remove stathubs/oxid
```

This removes the code and the module configuration, including the stored credentials.

**Installed from the ZIP:**

```sh
vendor/bin/oe-console oe:module:uninstall solu1stathubs
rm -rf source/modules/solu1/stathubs
```

Then remove the `Solu1\\StatHubs\\` line from the `autoload` section of the shop's `composer.json`, and run `composer dump-autoload`.

Finally, remove the shop in the StatHubs app.

## Troubleshooting

**StatHubs is not listed under Extensions → Modules.**
Run `vendor/bin/oe-console oe:cache:clear`. If you installed from the ZIP, check that the autoload entry was added, that `composer dump-autoload` was run, and run `vendor/bin/oe-console oe:module:install source/modules/solu1/stathubs`.

**Activation fails with a "class not found" error.**
The `Solu1\StatHubs\` namespace is not autoloaded. Run `composer dump-autoload`. If you installed from the ZIP, also check the `autoload` entry in the shop's `composer.json`.

**Extensions → StatHubs is missing after activation.**
Run `vendor/bin/oe-console oe:cache:clear`, then sign out of the admin and sign in again.

**The StatHubs app cannot connect.**
Test the endpoint from your computer. `curl` asks for the password, so the password doesn't end up in your shell history:

```sh
curl -u 'stathubs-api@your-shop.example' 'https://your-shop.example/index.php?cl=stathubs_api&fnc=ping'
```

- `{"ok":true}`: the module works. Check the URL and credentials entered in the app.
- `{"ok":false}` with HTTP 401: the credentials are wrong, or the web server strips the `Authorization` header before PHP. On Apache with PHP-FPM/CGI, add `CGIPassAuth On` to the shop's `.htaccess` or virtual host.
- An HTML page, 403 or timeout: a firewall, WAF, maintenance page or basic-auth protection is blocking the request.

**Orders are missing.**
The endpoint returns orders of the shop the request is routed to. Check that the shop URL in StatHubs is the URL of the right (sub-)shop.

## Support

- Website: <https://stathubs.com/>
- Email: <info@profihost.com>

When you report a problem, include your OXID eShop version and edition, PHP version and module version. **Never send the StatHubs password.**

## License

See `composer.json` for the module license.

It includes [QR Code Generator for JavaScript](https://github.com/kazuhikoarase/qrcode-generator) by Kazuhiko Arase, licensed under the MIT license (see the header of `assets/src/js/qrcode-generator.js`). "QR Code" is a registered trademark of DENSO WAVE INCORPORATED.
