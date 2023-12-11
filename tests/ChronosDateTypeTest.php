<?php
declare(strict_types=1);

namespace Franzose\DoctrineChronos\Tests;

use Cake\Chronos\ChronosDate;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Franzose\DoctrineChronos\ChronosDateType;
use Franzose\DoctrineChronos\ChronosType;
use PHPUnit\Framework\TestCase;

final class ChronosDateTypeTest extends TestCase
{
    /**
     * @dataProvider getDataForConvertToDatabaseValueTest
     */
    public function testConvertToDatabaseValue(?ChronosDate $date, ?string $expectedValue): void
    {
        $actualValue = self::getType()->convertToDatabaseValue($date, new PostgreSQLPlatform());

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
                ChronosDate::createFromFormat('d.m.Y', '03.01.2022'),
                '2022-01-03'
            ]
        ];
    }

    public function testConvertToDatabaseValueThrowsExceptionOnInvalidTypes(): void
    {
        $this->expectException(ConversionException::class);

        self::getType()->convertToDatabaseValue('03.01.2022', new PostgreSQLPlatform());
    }

    /**
     * @dataProvider getDataForConvertToPHPValueTest
     */
    public function testConvertToPHPValue(
        ChronosDate|string|null $date,
        ?ChronosDate $expectedValue
    ): void {
        $actualValue = self::getType()->convertToPHPValue($date, new PostgreSQLPlatform());

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
                ChronosDate::createFromFormat('d.m.Y', '03.01.2022'),
                ChronosDate::createFromFormat('d.m.Y', '03.01.2022')
            ],
            [
                '2022-01-03',
                ChronosDate::createFromFormat('Y-m-d', '2022-01-03')
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
        if (!Type::hasType(ChronosDateType::NAME)) {
            Type::addType(ChronosDateType::NAME, ChronosDateType::class);
        }

        return Type::getType(ChronosDateType::NAME);
    }
}