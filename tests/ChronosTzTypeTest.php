<?php
declare(strict_types=1);

namespace Franzose\DoctrineChronos\Tests;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Franzose\DoctrineChronos\ChronosTzType;
use PHPUnit\Framework\TestCase;

final class ChronosTzTypeTest extends TestCase
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
                Chronos::createFromFormat('d.m.Y H:i:s', '03.01.2022 11:22:33', '+00:00'),
                '2022-01-03 11:22:33+0000'
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
                Chronos::createFromFormat('d.m.Y H:i:s', '03.01.2022 11:22:33', '+00:00'),
                Chronos::createFromFormat('d.m.Y H:i:s', '03.01.2022 11:22:33', '+00:00')
            ],
            [
                '2022-01-03 11:22:33+00:00',
                Chronos::createFromFormat('Y-m-d H:i:s', '2022-01-03 11:22:33', '+00:00')
            ]
        ];
    }

    public function testConvertToPHPValueOnInvalidTypes(): void
    {
        $this->expectException(ConversionException::class);

        self::getType()->convertToPHPValue('Not a date/time value.', new PostgreSQLPlatform());
    }

    private static function getType(): Type|ChronosTzType
    {
        if (!Type::hasType(ChronosTzType::NAME)) {
            Type::addType(ChronosTzType::NAME, ChronosTzType::class);
        }

        return Type::getType(ChronosTzType::NAME);
    }
}
