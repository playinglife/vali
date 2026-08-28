<?php

namespace App\Support;

class Gtm
{
    public const COOKIE = 'cookie_consent';

    public static function id(): ?string
    {
        $id = strtoupper(trim((string) config('services.gtm.id')));

        return preg_match('/^GTM-[A-Z0-9]+$/', $id) === 1 ? $id : null;
    }

    public static function consent(): ?string
    {
        $value = request()->cookie(self::COOKIE);

        return in_array($value, ['granted', 'denied'], true) ? $value : null;
    }

    public static function isStorefront(): bool
    {
        return ! request()->is('admin', 'admin/*');
    }

    public static function granted(): bool
    {
        return self::isStorefront() && self::id() !== null && self::consent() === 'granted';
    }

    public static function showBanner(): bool
    {
        return self::isStorefront() && self::id() !== null && self::consent() === null;
    }
}
