<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

final class PlacemarkerNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Placemarker with id "%s" was not found.', $id));
    }
}
