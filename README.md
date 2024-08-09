# Symfony idempotent consumer core bundle

This is core bundle for idempotent consumer. It provides basic functionality 
to make your consumer idempotent.

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

