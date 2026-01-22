<?php

namespace App\Traits;

trait HasTranslations
{
    /**
     * Get a translated attribute.
     */
    public function getTranslated(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $translationsField = "{$attribute}_translations";

        if (!isset($this->casts[$translationsField]) || $this->casts[$translationsField] !== 'array') {
            return $this->{$attribute};
        }

        $translations = $this->{$translationsField} ?? [];

        if (isset($translations[$locale])) {
            return $translations[$locale];
        }

        // Fallback to English
        if (isset($translations['en'])) {
            return $translations['en'];
        }

        // Fallback to Arabic if English not found
        if (isset($translations['ar'])) {
            return $translations['ar'];
        }

        return $this->{$attribute};
    }

    /**
     * Dynamic magic method to handle translated attributes.
     * Overriding dynamic access if needed.
     */
    // Note: We can use accessors in models for cleaner syntax, e.g., getNameAttribute.
}
