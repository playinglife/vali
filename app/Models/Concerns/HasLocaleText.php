<?php

namespace App\Models\Concerns;

trait HasLocaleText
{
    /**
     * Resolved text for the given locale, with fallback to the other language when empty.
     */
    public function textForLocale(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'ro') {
            if ($this->romanian !== null && $this->romanian !== '') {
                return $this->romanian;
            }
            if ($this->english !== null && $this->english !== '') {
                return $this->english;
            }

            return null;
        }

        if ($this->english !== null && $this->english !== '') {
            return $this->english;
        }
        if ($this->romanian !== null && $this->romanian !== '') {
            return $this->romanian;
        }

        return null;
    }
}
