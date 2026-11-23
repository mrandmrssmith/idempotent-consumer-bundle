# Symfony idempotent consumer core bundle

This is core bundle for idempotent consumer. It provides basic functionality 
to make your consumer idempotent.

## Requirements

| PHP          | Symfony                      |
|--------------|------------------------------|
| 7.4, 8.0–8.3 | 3.4, 4.4, 5.4, 6.4, 7.4      |

Tested in CI with: PHP 7.4 + Symfony 3.4 (lowest), PHP 7.4 + Symfony 5.4,
PHP 8.1 + Symfony 6.4 and PHP 8.3 + Symfony 7.4.

## Installation

Add this package to your project
```shell
composer require mrandmrssmith/idempotent-consumer-bundle
```

## Usage
1. Structure:
    - `Persistance` - Provide interfaces for persistance 
    - `Resolver` - Interface which should be use to retrive idempotent key from message and register of these resolvers
    - `Checker` - main service which will handle idempotent logic on message entry you should use this checker before message processing and based on result of check you should decide to process message or not
    - `Finalizer` - main service which will handle idempotent logic on message exit you should use this finalizer after message processing to mark message as processed or when message failed.
2. Implement `Persistance` interface
   - you can implement you own persistence by implement these interfaces and register them in services.
   - there is one ready persistence provided by `mrandmrssmith/idempotent-consumer-doctrine-persistence-bundle` which provide persistence layer using doctrine
3. Resolvers:
   - you have to implement key resolvers for your consumers. 
   - it works as strategy pattern. You have to register your resolver and add tag `idempotent.key_resolver`
4. Checker and Finalizer
   - you have to use these services with your consumer checker on entry of message and finalizer after message processing
   - you can use package for symfony messenger `mrandmrssmith/idempotent-symfony-messenger-consumer-bundle`
5. Settings
   - By default failed messages which have status failed are skipped but it may be possible that
   you may want to try handle again message with status failed so there are 2 options for this
   - first option is in your configuration for this bundle set `process_failed_messages` to true
   ```yaml
      mms_idempotent_consumer:
        process_failed_messages: true
   ```
   that will change default value (false) of `$wantToProcessFailedMessage` in `DefaultProcessFailedMessageVoter` to value which you set here
   - second option is implement own voter - that may be solution when you want to implement own logic which tell if we should handle this message
   again or not for that you should
   ```yaml
      mms_idempotent_consumer:
        custom_process_failed_messages_voter: id_of_your_voter_service
   ```
   you can replace default voter to your own implementation of `ProcessFailedMessageVoter` what's provide you
   full control over processing failed messages.

---

## Development

Dockerfile added for testing of local build...

To build the image see the `Makefile` for all available commands.

```shell
make build74
```

To run a composer install use;

```shell
make composer-install
```

---

### Unit Tests

To run them.

Run composer install first as above.


```shell
make tests-php7
```

On a PHP 8 image (e.g. `make build83`), use the PHPUnit 10/11 configuration:

```shell
make tests-php8
```

### Security advisories

`roave/security-advisories` is not used, as it cannot be installed on PHP 7.4.
Check dependencies for known vulnerabilities with Composer 2.4+ instead:

```shell
composer audit
```

### PHP Compatibility Testing

https://github.com/PHPCompatibility/PHPCompatibility

Run composer install first as above.

#### Project Files PHP 7.4

```shell
make phpcompat-74-src
```

#### Vendor Files PHP 7.4

```shell
make phpcompat-74-vendor
```

#### Project Files PHP 7.4 - 8.3

```shell
make phpcompat-83-src
```
