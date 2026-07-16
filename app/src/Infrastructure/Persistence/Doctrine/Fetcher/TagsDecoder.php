<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

final class TagsDecoder
{
    /**
     * @return list<string>
     */
    public static function decode(string|array|null $raw): array
    {
        if ($raw === null || $raw === '' || $raw === '[]') {
            return [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                return [];
            }

            return self::decode($decoded);
        }

        $tags = [];
        foreach ($raw as $tag) {
            if (is_scalar($tag)) {
                $tags[] = (string) $tag;
            }
        }

        return array_values($tags);
    }
}
