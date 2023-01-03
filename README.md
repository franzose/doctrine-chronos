# Doctrine + Chronos = ❤️
[Chronos](https://github.com/cakephp/chronos) is an implementation of enriched, immutable date/time objects. This repository provides two custom Doctrine DBAL date/time types, `chronos` and `chronostz`. Use them in case you want to replace standard `DateTimeImmutable` objects with instances of `Cake\Chronos\ChronosInterface` in your entities.

## Registration & Usage

Register the types with the following lines:
```php
<?php
// in the bootstrapping code

use Doctrine\DBAL\Types\Type;
use Franzose\DoctrineChronos\ChronosType;
use Franzose\DoctrineChronos\ChronosTzType;

Type::addType('chronos', ChronosType::class);
Type::addType('chronostz', ChronosTzType::class);
```

Or in case of Symfony:
```yaml
doctrine:
    dbal:
        types:
            chronos: Franzose\DoctrineChronos\ChronosType
            chronostz: Franzose\DoctrineChronos\ChronosTzType
```

Now you can use these types in Doctrine entities:
```php
<?php
declare(strict_types=1);

#[Entity, Table(name: '"user"')]
class User
{
    #[Column(name: 'registered_at', type: 'chronostz', nullable: false)]
    private ChronosInterface $registeredAt;

    #[Column(name: 'confirmed_at', type: 'chronostz')]
    private ?ChronosInterface $confirmedAt = null;

    #[Column(name: 'deactivated_at', type: 'chronostz')]
    private ?ChronosInterface $deactivatedAt = null;
}
```
