<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\SearchArea;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SearchAreaTest extends TestCase
{
    public function testCreateSuccess(): void
    {
        $coordinates = Coordinates::fromFloats(45.0, 90.0);
        $area = SearchArea::create($coordinates, 1000.0);

        $this->assertSame($coordinates, $area->center());
        $this->assertSame(1000.0, $area->radiusMeters());
    }

    #[DataProvider('invalidRadiusProvider')]
    public function testCreateWithInvalidRadiusThrowsException(float $radius): void
    {
        $coordinates = Coordinates::fromFloats(45.0, 90.0);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Search radius must be greater than zero.');

        SearchArea::create($coordinates, $radius);
    }

    public static function invalidRadiusProvider(): array
    {
        return [
            [0.0],
            [-1.0],
            [-100.5],
        ];
    }
}
