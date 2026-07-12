<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class SearchArea
{
    private function __construct(
        private Coordinates $center,
        private float $radiusMeters,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function create(Coordinates $center, float $radiusMeters): self
    {
        if ($radiusMeters <= 0.0) {
            throw new InvalidArgumentException('Search radius must be greater than zero.');
        }

        return new self($center, $radiusMeters);
    }

    public function center(): Coordinates
    {
        return $this->center;
    }

    public function radiusMeters(): float
    {
        return $this->radiusMeters;
    }
}
