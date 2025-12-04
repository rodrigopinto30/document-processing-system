<?php

namespace App\Services;

use Illuminate\Support\Str;

class DocumentAnalyzerService
{
    public function countLines(string $text): int
    {
        if ($text === '') return 0;

        return count(preg_split("/\r\n|\n|\r/", $text));
    }

    public function countWords(string $text): int
    {
        $clean = trim(strip_tags($text));
        if ($clean === '') return 0;

        preg_match_all('/\p{L}[\p{L}\p{Mn}\p{Pd}\']*/u', $clean, $matches);
        return count($matches[0]);
    }

    public function countCharacters(string $text): int
    {
        return mb_strlen($text);
    }

    public function extractMostFrequentWords(string $text, int $stop = 10): array
    {

        $clean = mb_strtolower(strip_tags($text));

        $clean = preg_replace('/[^\p{L}\p{N}\'-]+/u', ' ', $clean);
        $tokens = preg_split('/\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($tokens)) return [];

        $stopwords = $this->getStopWords();
        $counts = [];

        foreach ($tokens as $t) {
            if (mb_strlen($t) <= 1) continue;
            if (in_array($t, $stopwords, true)) continue;
            $counts[$t] = ($counts[$t] ?? 0) + 1;
        }

        arsort($counts);
        return array_slice(array_keys($counts), 0, $stop);
    }

    public function generateSummary(string $text, int $sentences = 3): string
    {
        $clean = trim(strip_tags($text));
        if ($clean === '') return '';

        $parts = preg_split('/(?<=[.?!])\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) return Str::limit($clean, 300);

        $summaryParts = array_slice($parts, 0, $sentences);
        return implode(' ', $summaryParts);
    }

    protected function getStopWords(): array
    {
        return [
            'the',
            'and',
            'a',
            'to',
            'of',
            'in',
            'is',
            'it',
            'that',
            'with',
            'for',
            'as',
            'on',
            'are',
            'this',
            'an',
            'be',
            'by',
            'or',
            'from'

        ];
    }
}
