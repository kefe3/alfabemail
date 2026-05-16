<?php

namespace App\Services;

class ProfanityFilter
{
    protected array $words = [
        'amına', 'amk', 'aq', 'orospu', 'piç', 'sik', 'sikik', 'sikey',
        'ananı', 'anana', 'anan', 'babanı', 'babana',
        'yarrak', 'yaraq', 'mal', 'salak', 'aptal', 'gerizekalı',
        'ibne', 'pezevenk', 'göt', 'got', 'bok', 'sıç', 'sic',
        'amcık', 'amcik', 'am', 'sikmek', 'sikme',
        'oruspu', 'puşt', 'pust', 'kahpe', 'kahbe',
        'mk', 'amq', 'sikerim', 'siker', 'sokarim',
        'allahını', 'allahsız', 'dinsiz', 'hıyar',
        'kancık', 'kaltak', 'şerefsiz', 'serefsiz',
        'çük', 'cük', 'çüş', 'cüş',
        'meme', 'memeler', 'göğüs', 'gogus',
        'seks', 'porno', 'sex',
        'oç', 'oc', 'ulan', 'sürtük', 'sortuk',
    ];

    protected array $patterns = [
        '/\b(a[mğ]k|am[mğ]|mq|orospu|piç|sik|yarrak|ibne|göt)\b/i',
        '/[aA4][mM][kKqQ]/',
        '/[oO][rR][oO][sS][pP][uU]/',
        '/[pP][iİ][çÇ]/',
        '/[sS][iİ][kK]/',
    ];

    public function containsProfanity(string $text): bool
    {
        $lower = mb_strtolower($text, 'UTF-8');
        $lower = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $lower);

        foreach ($this->words as $word) {
            $search = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $word);
            if (str_contains($lower, $search)) {
                return true;
            }
        }

        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    public function filter(string $text): string
    {
        $lower = mb_strtolower($text, 'UTF-8');
        $normalized = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $lower);

        $result = $text;
        foreach ($this->words as $word) {
            $search = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $word);
            $normalizedWord = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $word);
            $replacement = str_repeat('*', mb_strlen($word));
            $result = preg_replace('/' . preg_quote($word, '/') . '/iu', $replacement, $result);
        }

        return $result;
    }
}
