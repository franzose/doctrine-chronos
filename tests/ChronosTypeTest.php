<?php
declare(strict_types=1);

namespace Franzose\DoctrineChronos\Tests;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Franzose\DoctrineChronos\ChronosType;
use PHPUnit\Framework\TestCase;

final class ChronosTypeTest extends TestCase
{
    /**
     * @dataProvider getDataForConvertToDatabaseValueTest
     */
    public function testConvertToDatabaseValue(?ChronosInterface $dateTime, ?string $expectedValue): void
    {
        $actualValue = self::getType()->convertToDatabaseValue($dateTime, new PostgreSQLPlatform());

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
                Chronos::createFromFormat('d.m.Y H:i:s', '03.01.2022 11:22:33'),
                '2022-01-03 11:22:33'
            ]
        ];
    }

    public function testConvertToDatabaseValueThrowsExceptionOnInvalidTypes(): void
    {
        $this->expectException(ConversionException::class);

        self::getType()->convertToDatabaseValue('03.01.2022 11:22:33', new PostgreSQLPlatform());
    }

    /**
     * @dataProvider getDataForConvertToPHPValueTest
     */
    public function testConvertToPHPValue(
        ChronosInterface|string|null $dateTime,
        ?ChronosInterface $expectedValue
    ): void {
        $actualValue = self::getType()->convertToPHPValue($dateTime, new PostgreSQLPlatform());

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
                Chronos::createFromFormat('d.m.Y H:i:s', '03.01.2022 11:22:33'),
                Chronos::createFromFormat('d.m.Y H:i:s', '03.01.2022 11:22:33')
            ],
            [
                '2022-01-03 11:22:33',
                Chronos::createFromFormat('Y-m-d H:i:s', '2022-01-03 11:22:33')
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
        if (!Type::hasType(ChronosType::NAME)) {
            Type::addType(ChronosType::NAME, ChronosType::class);
        }

        return Type::getType(ChronosType::NAME);
    }
}
