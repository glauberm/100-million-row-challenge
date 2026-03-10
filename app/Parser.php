<?php

namespace App;

use App\Commands\Visit;

final class Parser
{
    public static function parse($inputPath, $outputPath): void
    {
        \gc_disable();

        $fileSize   = \filesize($inputPath);
        $dateIds    = [];
        $dateLabels = [];
        $numDates   = 0;

        for ($year = 21; $year <= 26; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $daysInMonth = match ($month) {
                    2           => ($year % 4 === 0) ? 29 : 28,
                    4, 6, 9, 11 => 30,
                    default     => 31,
                };

                $monthStr   = $month < 10 ? '0' . $month : (string) $month;
                $datePrefix = ($year % 10) . '-' . $monthStr . '-';

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $key                   = $datePrefix . ($day < 10 ? '0' . $day : (string) $day);
                    $dateIds[$key]         = $numDates;
                    $dateLabels[$numDates] = $key;
                    $numDates++;
                }
            }
        }

        $slugBase   = [];
        $slugLabels = [];
        $numSlugs   = 0;
        foreach (Visit::SLUGS as $slug) {
            $slugBase[$slug]       = $numSlugs * $numDates;
            $slugLabels[$numSlugs] = $slug;
            $numSlugs++;
        }

        $counts     = \array_fill(0, $numSlugs * $numDates, 0);
        $fileHandle = \fopen($inputPath, 'rb');
        \stream_set_read_buffer($fileHandle, 0);
        $remaining  = $fileSize;

        while ($remaining > 0) {
            $chunk       = \fread($fileHandle, $remaining > 524_288 ? 524_288 : $remaining);
            $chunkLength = \strlen($chunk);
            $remaining  -= $chunkLength;
            $lastNl      = \strrpos($chunk, "\n");

            if ($over = $chunkLength - $lastNl - 1) {
                \fseek($fileHandle, -$over, \SEEK_CUR);
                $remaining += $over;
            }

            $pos  = 25;
            $safe = $lastNl - 1000;

            while ($pos < $safe) {
                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;

                $sep = \strpos($chunk, ',', $pos + 4);
                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;
            }

            while ($pos < $lastNl) {
                $sep = \strpos($chunk, ',', $pos + 4);

                if ($sep === false || $sep >= $lastNl) {
                    break;
                }

                $counts[$slugBase[\substr($chunk, $pos, $sep - $pos)] + $dateIds[\substr($chunk, $sep + 4, 7)]]++;
                $pos = $sep + 52;
            }
        }

        \fclose($fileHandle);

        $outputHandle = \fopen($outputPath, 'wb');
        \stream_set_write_buffer($outputHandle, 4_194_304);

        $datePfx  = [];
        $datePfxC = [];
        for ($dateId = 0; $dateId < $numDates; $dateId++) {
            $entry             = '        "202' . $dateLabels[$dateId] . '": ';
            $datePfx[$dateId]  = $entry;
            $datePfxC[$dateId] = ",\n" . $entry;
        }

        $slugOpen = [];
        for ($slugId = 0; $slugId < $numSlugs; $slugId++) {
            $slugOpen[$slugId] = "\n    \"\/blog\/" . \str_replace('/', '\/', $slugLabels[$slugId]) . "\": {\n";
        }

        \fwrite($outputHandle, '{');
        $first = true;

        for ($slugId = 0; $slugId < $numSlugs; $slugId++) {
            $base = $slugId * $numDates;
            $body = '';

            for ($dateId = 0; $dateId < $numDates; $dateId++) {
                if ($count = $counts[$base + $dateId]) {
                    $body .= ($body !== '' ? $datePfxC[$dateId] : $datePfx[$dateId]) . $count;
                }
            }

            if ($body === '') {
                continue;
            }

            \fwrite($outputHandle, ($first ? '' : ',') . $slugOpen[$slugId] . $body . "\n    }");
            $first = false;
        }

        \fwrite($outputHandle, "\n}");
        \fclose($outputHandle);
    }
}
