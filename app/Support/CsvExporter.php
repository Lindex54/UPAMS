<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /** @param list<string> $headings @param iterable<array<int, mixed>> $rows */
    public static function download(string $filename, array $headings, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows): void {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headings);
            foreach ($rows as $row) {
                fputcsv($stream, array_map(self::safeCell(...), $row));
            }
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private static function safeCell(mixed $value): mixed
    {
        return is_string($value) && preg_match('/^[=+\-@]/', $value) ? "'{$value}" : $value;
    }
}
