<?php
declare(strict_types=1);

namespace Franzose\DoctrineChronos;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class ChronosTzType extends Type
{
    public const NAME = 'chronostz';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof ChronosInterface) {
            return $value->format($platform->getDateTimeTzFormatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', ChronosInterface::class]
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof ChronosInterface) {
            return $value;
        }

        try {
            $dateTime = Chronos::createFromFormat($platform->getDateTimeTzFormatString(), $value);
        } catch (\Exception $ex) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeTzFormatString(),
                $ex
            );
        }

        return $dateTime;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTzTypeDeclarationSQL($column);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
