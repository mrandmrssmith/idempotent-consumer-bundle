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
