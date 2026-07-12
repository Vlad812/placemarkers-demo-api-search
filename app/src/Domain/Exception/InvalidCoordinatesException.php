<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

final class InvalidCoordinatesException extends DomainException
{
    public static function invalidLatitude(float $latitude): self
    {
        return new self(sprintf('Invalid latitude: %f. Must be between -90 and 90.', $latitude));
    }

    public static function invalidLongitude(float $longitude): self
    {
        return new self(sprintf('Invalid longitude: %f. Must be between -180 and 180.', $longitude));
    }

    public static function notNumeric(): self
    {
        return new self('Coordinates must be numeric.');
    }
}
