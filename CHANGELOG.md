# Changelog

All notable changes to this module are documented here. The project follows [Semantic Versioning](https://semver.org/).

## [1.1.1] - 2026-11-20

### Fixed
- Extensions → StatHubs page failed to open on OXID eShop 7.0.
- Order endpoint returned invalid JSON when the shop shows PHP warnings or notices on screen.

## [1.1.0] - 2026-11-19

### Changed
- Supports all OXID eShop 7 versions (`oxid-esales/oxideshop-ce` ^7.0) instead of 7.5 only.
- Minimum PHP version lowered from 8.3 to 8.1.

## [1.0.1] - 2026-11-15

### Changed
- Composer package renamed from `solu1/stathubs` to `stathubs/oxid`. Install with `composer require stathubs/oxid`.
- README: one-command Composer installation.

### Removed
- `.sha256` checksum file from release downloads.

## [1.0.0] - 2026-11-14

### Added
- StatHubs connection credentials, created on activation.
- Extensions → StatHubs admin page with shop URL, email, password and QR code.
- Read-only order endpoint `index.php?cl=stathubs_api` (`ping`, `orders`) protected by those credentials.
- German and English admin translations.
