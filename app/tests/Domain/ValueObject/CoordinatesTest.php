<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\Exception\InvalidCoordinatesException;
use App\Domain\ValueObject\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CoordinatesTest extends TestCase
{
    public function testFromFloatsSuccess(): void
    {
        $coordinates = Coordinates::fromFloats(45.5, 90.5);

        $this->assertSame(45.5, $coordinates->latitude());
        $this->assertSame(90.5, $coordinates->longitude());
    }

    public function testFromStringsSuccess(): void
    {
        $coordinates = Coordinates::fromStrings('45.5', '90.5');

        $this->assertSame(45.5, $coordinates->latitude());
        $this->assertSame(90.5, $coordinates->longitude());
    }

    #[DataProvider('invalidLatitudeProvider')]
    public function testInvalidLatitudeThrowsException(float $latitude): void
    {
        $this->expectException(InvalidCoordinatesException::class);
        $this->expectExceptionMessage('Invalid latitude');

        Coordinates::fromFloats($latitude, 0.0);
    }

    public static function invalidLatitudeProvider(): array
    {
        return [
            [-90.1],
            [90.1],
            [-100.0],
            [100.0],
        ];
    }

    #[DataProvider('invalidLongitudeProvider')]
    public function testInvalidLongitudeThrowsException(float $longitude): void
    {
        $this->expectException(InvalidCoordinatesException::class);
        $this->expectExceptionMessage('Invalid longitude');

        Coordinates::fromFloats(0.0, $longitude);
    }

    public static function invalidLongitudeProvider(): array
    {
        return [
            [-180.1],
            [180.1],
            [-200.0],
            [200.0],
        ];
    }

    #[DataProvider('nonNumericStringsProvider')]
    public function testFromStringsWithNonNumericThrowsException(string $latitude, string $longitude): void
    {
        $this->expectException(InvalidCoordinatesException::class);
        $this->expectExceptionMessage('Coordinates must be numeric.');

        Coordinates::fromStrings($latitude, $longitude);
    }

    public static function nonNumericStringsProvider(): array
    {
        return [
            ['abc', '90.5'],
            ['45.5', 'def'],
            ['abc', 'def'],
            ['', ''],
            [' ', ' '],
        ];
    }
}
