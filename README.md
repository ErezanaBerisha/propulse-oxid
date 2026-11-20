<p>
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/ErezanaBerisha/propulse-oxid/main/.github/assets/propulse-logo-white.png">
    <img src="https://raw.githubusercontent.com/ErezanaBerisha/propulse-oxid/main/.github/assets/propulse-logo-black.png" alt="proPulse" width="280">
  </picture>
</p>

# proPulse for OXID eShop

Connect your OXID eShop to [proPulse](https://stathubs.com/) and see your sales in the proPulse mobile app.

## What the module does

- When you activate it, the module creates proPulse connection credentials (an email-style login and a random password) for your shop.
- Adds **Extensions → proPulse** to the OXID admin. That page shows the shop URL, login email, password and a QR code. Scan the QR code with the proPulse app to connect the shop.
- Provides a small read-only endpoint, `index.php?cl=propulse_api`, that returns order data to proPulse after it checks those credentials.

The module adds no cron jobs, no frontend output, no changes to existing shop classes and no outgoing network calls.

## Compatibility

| | |
|---|---|
| Tested on | OXID eShop Community Edition 7.5.2 (metapackage 7.5.1) with PHP 8.3, and 7.0.5 with PHP 8.1, both with the Twig admin theme |
| Declared requirement | OXID eShop 7.x (`oxid-esales/oxideshop-ce` ^7.0) with the Twig admin theme, PHP 8.1 or later |
| Not supported | OXID eShop 6.x and earlier (Smarty admin) |

Professional and Enterprise Editions and sub-shops have not been tested yet.

## Requirements

- Shell (SSH) access to the server. If you don't have it, send this page to your developer or hosting provider.
- The shop must be reachable over **HTTPS** from the internet. The proPulse servers call the module's endpoint at `https://<your-shop>/index.php`.
- That URL must not be blocked by a firewall, WAF or basic-auth protection for the proPulse servers.

## Installation

Run these commands in the shop root directory (the folder that contains `composer.json`, `source/` and `vendor/`), as the user that owns the shop files:

```sh
composer require propulse/oxid
vendor/bin/oe-console oe:module:activate solu1propulse
```

That's it. Continue with [Connect the shop to proPulse](#connect-the-shop-to-propulse).

- Instead of the second command, you can activate the module in the admin under **Extensions → Modules → proPulse → Activate**.
- For a sub-shop, add `--shop-id=<id>` to the activate command.
- OXID eShop cannot install modules from a ZIP uploaded in the admin panel.

### Without Composer (ZIP)

1. Download `propulse-oxid.zip` from the [Releases](../../releases) page.
2. Extract it into `source/modules/solu1/propulse`:

   ```sh
   mkdir -p source/modules/solu1/propulse
   unzip propulse-oxid.zip -d source/modules/solu1/propulse
   ```

   `source/modules/solu1/propulse/metadata.php` must exist afterwards.
3. Add the module's namespace to the `autoload` section of the shop's root `composer.json`:

   ```json
   "autoload": {
       "psr-4": {
           "Solu1\\ProPulse\\": "./source/modules/solu1/propulse/src/"
       }
   }
   ```

4. Register and activate the module:

   ```sh
   composer dump-autoload
   vendor/bin/oe-console oe:module:install source/modules/solu1/propulse
   vendor/bin/oe-console oe:module:activate solu1propulse
   ```

## Connect the shop to proPulse

1. In the OXID admin, open **Extensions → proPulse**.
2. In the proPulse app, add a new shop and scan the QR code on that page.
   Or enter the shop URL, email and password shown on the page by hand.
3. proPulse imports your orders. The first sync can take a few minutes on large shops.

Check that the shop URL on that page starts with `https://`. If it doesn't, fix the shop URL in your shop configuration (`source/config.inc.php`, `sShopURL`/`sSSLShopURL`) before you connect.

## Permissions and data access

The proPulse credentials only work for the module's own endpoint. They are not an OXID admin or customer account, and they cannot sign in to the shop or the admin.

The endpoint is read-only. It has two actions:

| Action | Returns |
|---|---|
| `fnc=ping` | `{"ok": true}` if the credentials are valid |
| `fnc=orders` | Orders of the current shop, at most 100 per request |

Each order contains only these fields from `oxorder`: `OXID`, `OXORDERNR`, `OXORDERDATE`, `OXTOTALNETSUM`, `OXTOTALORDERSUM`, `OXPAYMENTTYPE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXSENDDATE`, `OXPAID`, `OXTIMESTAMP`, `OXSTORNO`.

**No customer data** (names, email addresses, addresses), no order items and no product data are sent.

## Credentials and security

- The password is a random 32-character hexadecimal string, generated on your shop when you activate the module. The package contains no credentials or API keys.
- OXID stores module settings in the shop's configuration files. The credentials are in `var/configuration/shops/<shop-id>/modules/solu1propulse.yaml`. Keep the `var/` directory out of version control and public web access, as you would for the rest of the shop configuration.
- The password is shown in plain text on **Extensions → proPulse**, so that it can be scanned or copied. Restrict admin access to trusted users.
- Credentials are compared in constant time. Requests without valid credentials get HTTP 401.
- **Deactivating the module turns off the endpoint immediately.** proPulse can no longer read data.

### Rotate the password

The module has no button to create a new password yet. To get new credentials, remove the module configuration and install it again:

```sh
vendor/bin/oe-console oe:module:deactivate solu1propulse
vendor/bin/oe-console oe:module:uninstall solu1propulse
vendor/bin/oe-console oe:module:install vendor/propulse/oxid
vendor/bin/oe-console oe:module:activate solu1propulse
vendor/bin/oe-console oe:cache:clear
```

If you installed from the ZIP, use `source/modules/solu1/propulse` instead of `vendor/propulse/oxid`.

Then reconnect the shop in the proPulse app with the new QR code.

## Update

**Installed with Composer:**

```sh
composer update propulse/oxid
vendor/bin/oe-console oe:cache:clear
```

**Installed from the ZIP:** download the new ZIP, replace the contents of `source/modules/solu1/propulse` with it, then run `composer dump-autoload` and `vendor/bin/oe-console oe:cache:clear`.

Your existing proPulse credentials are kept.

## Uninstall

First deactivate the module (**Extensions → Modules → proPulse → Deactivate**, or `vendor/bin/oe-console oe:module:deactivate solu1propulse`).

**Installed with Composer:**

```sh
composer remove propulse/oxid
```

This removes the code and the module configuration, including the stored credentials.

**Installed from the ZIP:**

```sh
vendor/bin/oe-console oe:module:uninstall solu1propulse
rm -rf source/modules/solu1/propulse
```

Then remove the `Solu1\\ProPulse\\` line from the `autoload` section of the shop's `composer.json`, and run `composer dump-autoload`.

Finally, remove the shop in the proPulse app.

## Troubleshooting

**proPulse is not listed under Extensions → Modules.**
Run `vendor/bin/oe-console oe:cache:clear`. If you installed from the ZIP, check that the autoload entry was added, that `composer dump-autoload` was run, and run `vendor/bin/oe-console oe:module:install source/modules/solu1/propulse`.

**Activation fails with a "class not found" error.**
The `Solu1\ProPulse\` namespace is not autoloaded. Run `composer dump-autoload`. If you installed from the ZIP, also check the `autoload` entry in the shop's `composer.json`.

**Extensions → proPulse is missing after activation.**
Run `vendor/bin/oe-console oe:cache:clear`, then sign out of the admin and sign in again.

**The proPulse app cannot connect.**
Test the endpoint from your computer. `curl` asks for the password, so the password doesn't end up in your shell history:

```sh
curl -u 'propulse-api@your-shop.example' 'https://your-shop.example/index.php?cl=propulse_api&fnc=ping'
```

- `{"ok":true}`: the module works. Check the URL and credentials entered in the app.
- `{"ok":false}` with HTTP 401: the credentials are wrong, or the web server strips the `Authorization` header before PHP. On Apache with PHP-FPM/CGI, add `CGIPassAuth On` to the shop's `.htaccess` or virtual host.
- An HTML page, 403 or timeout: a firewall, WAF, maintenance page or basic-auth protection is blocking the request.

**Orders are missing.**
The endpoint returns orders of the shop the request is routed to. Check that the shop URL in proPulse is the URL of the right (sub-)shop.

## Support

- Website: <https://stathubs.com/>
- Email: <info@profihost.com>

When you report a problem, include your OXID eShop version and edition, PHP version and module version. **Never send the proPulse password.**

## License

See `composer.json` for the module license.

It includes [QR Code Generator for JavaScript](https://github.com/kazuhikoarase/qrcode-generator) by Kazuhiko Arase, licensed under the MIT license (see the header of `assets/src/js/qrcode-generator.js`). "QR Code" is a registered trademark of DENSO WAVE INCORPORATED.
