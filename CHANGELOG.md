# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Support for PHP 8.0–8.3 and Symfony 6.4 / 7.4.
- GitHub Actions test matrix (PHP 7.4 / 8.1 / 8.3, lowest and highest dependencies).
- `make phpcompat-83-src` target (PHPCompatibility 7.4–8.3).

### Fixed
- Added `void` return type to `MmsIdempotentConsumerExtension::load()`
  (Symfony 6.4/7.4 deprecation).

### Changed
- Explicit version constraints: `php ^7.4 || ^8.0`, Symfony
  `^3.4 || ^4.4 || ^5.4 || ^6.4 || ^7.4` (previously open-ended `>=`).

### Removed
- `roave/security-advisories` from require-dev (not installable on PHP 7.4);
  use `composer audit` instead.


## [<VERSION NO>] - <DATE>

### Added

### Fixed

### Changed

### Removed
