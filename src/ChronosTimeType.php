<?php
declare(strict_types=1);

namespace Franzose\DoctrineChronos;

use Cake\Chronos\ChronosTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class ChronosTimeType extends Type
{
    public const NAME = 'chronos_time';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof ChronosTime) {
            return $value->format($platform->getTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', ChronosTime::class]
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof ChronosTime) {
            return $value;
        }

        try {
            return ChronosTime::parse($value);
        } catch (\Exception $ex) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getTimeFormatString(),
                $ex
            );
        }
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}