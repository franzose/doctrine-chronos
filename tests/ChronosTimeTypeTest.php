<?php
declare(strict_types=1);

namespace Franzose\DoctrineChronos\Tests;

use Cake\Chronos\ChronosTime;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Franzose\DoctrineChronos\ChronosTimeType;
use Franzose\DoctrineChronos\ChronosType;
use PHPUnit\Framework\TestCase;

final class ChronosTimeTypeTest extends TestCase
{
    /**
     * @dataProvider getDataForConvertToDatabaseValueTest
     */
    public function testConvertToDatabaseValue(?ChronosTime $time, ?string $expectedValue): void
    {
        $actualValue = self::getType()->convertToDatabaseValue($time, new PostgreSQLPlatform());

        self::assertEquals($expectedValue, $actualValue);
    }

    private function getDataForConvertToDatabaseValueTest(): array
    {
        return [
            [
                null,
                null
            ],
            [
                new ChronosTime('11:45'),
                '11:45:00'
            ]
        ];
    }

    public function testConvertToDatabaseValueThrowsExceptionOnInvalidTypes(): void
    {
        $this->expectException(ConversionException::class);

        self::getType()->convertToDatabaseValue('11:45', new PostgreSQLPlatform());
    }

    /**
     * @dataProvider getDataForConvertToPHPValueTest
     */
    public function testConvertToPHPValue(
        ChronosTime|string|null $time,
        ?ChronosTime $expectedValue
    ): void {
        $actualValue = self::getType()->convertToPHPValue($time, new PostgreSQLPlatform());

        self::assertEquals($expectedValue, $actualValue);
    }

    private function getDataForConvertToPHPValueTest(): array
    {
        return [
            [
                null,
                null
            ],
            [
                new ChronosTime('11:45:23'),
                new ChronosTime('11:45:23')
            ],
            [
                '11:45',
                new ChronosTime('11:45:00')
            ]
        ];
    }

    public function testConvertToPHPValueOnInvalidTypes(): void
    {
        $this->expectException(ConversionException::class);

        self::getType()->convertToPHPValue('Not a date/time value.', new PostgreSQLPlatform());
    }

    private static function getType(): Type|ChronosType
    {
        if (!Type::hasType(ChronosTimeType::NAME)) {
            Type::addType(ChronosTimeType::NAME, ChronosTimeType::class);
        }

        return Type::getType(ChronosTimeType::NAME);
    }
}