<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidCoordinatesException;

final readonly class Coordinates
{
    private function __construct(
        private float $latitude,
        private float $longitude,
    ) {
    }

    /**
     * @throws InvalidCoordinatesException
     */
    public static function fromFloats(float $latitude, float $longitude): self
    {
        if ($latitude < -90.0 || $latitude > 90.0) {
            throw InvalidCoordinatesException::invalidLatitude($latitude);
        }

        if ($longitude < -180.0 || $longitude > 180.0) {
            throw InvalidCoordinatesException::invalidLongitude($longitude);
        }

        return new self($latitude, $longitude);
    }

    /**
     * @throws InvalidCoordinatesException
     */
    public static function fromStrings(string $latitude, string $longitude): self
    {
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            throw InvalidCoordinatesException::notNumeric();
        }

        return self::fromFloats((float) $latitude, (float) $longitude);
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }
}
