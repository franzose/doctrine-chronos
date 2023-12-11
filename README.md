# Doctrine + Chronos = ❤️
[Chronos](https://github.com/cakephp/chronos) is an implementation of enriched, immutable date/time objects. This repository provides four custom Doctrine DBAL date/time types: `chronos`, `chronostz`, `chronos_date`, and `chronos_time`. Use them in case you want to replace standard `DateTimeImmutable` objects with instances of `Cake\Chronos\Chronos`, `Cake\Chronos\ChronosDate`, and `Cake\Chronos\ChronosTime` respectively in your entities.

## Registration & Usage

Register the types with the following lines:
```php
<?php
// in the bootstrapping code

use Doctrine\DBAL\Types\Type;
use Franzose\DoctrineChronos\ChronosDateType;
use Franzose\DoctrineChronos\ChronosTimeType;
use Franzose\DoctrineChronos\ChronosType;
use Franzose\DoctrineChronos\ChronosTzType;

Type::addType('chronos_date', ChronosDateType::class);
Type::addType('chronos_time', ChronosTimeType::class);
Type::addType('chronos', ChronosType::class);
Type::addType('chronostz', ChronosTzType::class);
```

Or in case of Symfony:
```yaml
doctrine:
    dbal:
        types:
            chronos_date: Franzose\DoctrineChronos\ChronosDateType
            chronos_time: Franzose\DoctrineChronos\ChronosTimeType
            chronos: Franzose\DoctrineChronos\ChronosType
            chronostz: Franzose\DoctrineChronos\ChronosTzType
```

Now you can use these types in Doctrine entities:
```php
<?php
declare(strict_types=1);

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosDate;
use Cake\Chronos\ChronosTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: '"user"')]
class User
{
    #[Column(name: 'registered_at', type: 'chronostz', nullable: false)]
    private Chronos $registeredAt;

    #[Column(name: 'confirmed_at', type: 'chronostz')]
    private ?Chronos $confirmedAt = null;

    #[Column(name: 'deactivated_at', type: 'chronostz')]
    private ?Chronos $deactivatedAt = null;
    
    #[Column(name: 'birth_date', type: 'chronos_date')]
    private ?ChronosDate $birthDate = null;
    
    #[Column(name: 'usual_wake_up', type: 'chronos_time')]
    private ?ChronosTime $usualWakeUp = null;
}
```
