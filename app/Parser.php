<?php

namespace App;

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
        $slugIds    = [];
        $slugLabels = [];
        $numSlugs   = 0;
        foreach (
            [
                'which-editor-to-choose',
                'tackling_responsive_images-part_1',
                'tackling_responsive_images-part_2',
                'image_optimizers',
                'static_sites_vs_caching',
                'stitcher-alpha-4',
                'simplest-plugin-support',
                'stitcher-alpha-5',
                'php-generics-and-why-we-need-them',
                'stitcher-beta-1',
                'array-objects-with-fixed-types',
                'performance-101-building-the-better-web',
                'process-forks',
                'object-oriented-generators',
                'responsive-images-as-css-background',
                'a-programmers-cognitive-load',
                'mastering-key-bindings',
                'stitcher-beta-2',
                'phpstorm-performance',
                'optimised-uuids-in-mysql',
                'asynchronous-php',
                'mysql-import-json-binary-character-set',
                'where-a-curly-bracket-belongs',
                'mysql-query-logging',
                'mysql-show-foreign-key-errors',
                'responsive-images-done-right',
                'phpstorm-tips-for-power-users',
                'what-php-can-be',
                'phpstorm-performance-issues-on-osx',
                'dependency-injection-for-beginners',
                'liskov-and-type-safety',
                'acquisition-by-giants',
                'visual-perception-of-code',
                'service-locator-anti-pattern',
                'the-web-in-2045',
                'eloquent-mysql-views',
                'laravel-view-models',
                'laravel-view-models-vs-view-composers',
                'organise-by-domain',
                'array-merge-vs + ',
                'share-a-blog-assertchris-io',
                'phpstorm-performance-october-2018',
                'structuring-unstructured-data',
                'share-a-blog-codingwriter-com',
                'new-in-php-73',
                'share-a-blog-betterwebtype-com',
                'have-you-thought-about-casing',
                'comparing-dates',
                'share-a-blog-sebastiandedeyne-com',
                'analytics-for-developers',
                'announcing-aggregate',
                'php-jit',
                'craftsmen-know-their-tools',
                'laravel-queueable-actions',
                'php-73-upgrade-mac',
                'array-destructuring-with-list-in-php',
                'unsafe-sql-functions-in-laravel',
                'starting-a-newsletter',
                'short-closures-in-php',
                'solid-interfaces-and-final-rant-with-brent',
                'php-in-2019',
                'starting-a-podcast',
                'a-project-at-spatie',
                'what-are-objects-anyway-rant-with-brent',
                'tests-and-types',
                'typed-properties-in-php-74',
                'preloading-in-php-74',
                'things-dependency-injection-is-not-about',
                'a-letter-to-the-php-team',
                'a-letter-to-the-php-team-reply-to-joe',
                'guest-posts',
                'can-i-translate-your-blog',
                'laravel-has-many-through',
                'laravel-custom-relation-classes',
                'new-in-php-74',
                'php-74-upgrade-mac',
                'php-preload-benchmarks',
                'php-in-2020',
                'enums-without-enums',
                'bitwise-booleans-in-php',
                'event-driven-php',
                'minor-versions-breaking-changes',
                'combining-event-sourcing-and-stateful-systems',
                'array-chunk-in-php',
                'php-8-in-8-code-blocks',
                'builders-and-architects-two-types-of-programmers',
                'the-ikea-effect',
                'php-74-in-7-code-blocks',
                'improvements-on-laravel-nova',
                'type-system-in-php-survey',
                'merging-multidimensional-arrays-in-php',
                'what-is-array-plus-in-php',
                'type-system-in-php-survey-results',
                'constructor-promotion-in-php-8',
                'abstract-resources-in-laravel-nova',
                'braille-and-the-history-of-software',
                'jit-in-real-life-web-applications',
                'php-8-match-or-switch',
                'why-we-need-named-params-in-php',
                'shorthand-comparisons-in-php',
                'php-8-before-and-after',
                'php-8-named-arguments',
                'my-journey-into-event-sourcing',
                'differences',
                'annotations',
                'dont-get-stuck',
                'attributes-in-php-8',
                'the-case-for-transpiled-generics',
                'phpstorm-scopes',
                'why-light-themes-are-better-according-to-science',
                'what-a-good-pr-looks-like',
                'front-line-php',
                'php-8-jit-setup',
                'php-8-nullsafe-operator',
                'new-in-php-8',
                'php-8-upgrade-mac',
                'when-i-lost-a-few-hundred-leads',
                'websites-like-star-wars',
                'php-reimagined',
                'a-storm-in-a-glass-of-water',
                'php-enums-before-php-81',
                'php-enums',
                'dont-write-your-own-framework',
                'honesty',
                'thoughts-on-event-sourcing',
                'what-event-sourcing-is-not-about',
                'fibers-with-a-grain-of-salt',
                'php-in-2021',
                'parallel-php',
                'why-we-need-multi-line-short-closures-in-php',
                'a-new-major-version-of-laravel-event-sourcing',
                'what-about-config-builders',
                'opinion-driven-design',
                'php-version-stats-july-2021',
                'what-about-request-classes',
                'cloning-readonly-properties-in-php-81',
                'an-event-driven-mindset',
                'php-81-before-and-after',
                'optimistic-or-realistic-estimates',
                'we-dont-need-runtime-type-checks',
                'the-road-to-php',
                'why-do-i-write',
                'rational-thinking',
                'named-arguments-and-variadic-functions',
                're-on-using-psr-abstractions',
                'my-ikea-clock',
                'php-81-readonly-properties',
                'birth-and-death-of-a-framework',
                'php-81-new-in-initializers',
                'route-attributes',
                'generics-in-php-video',
                'php-81-in-8-code-blocks',
                'new-in-php-81',
                'php-81-performance-in-real-life',
                'php-81-upgrade-mac',
                'how-to-be-right-on-the-internet',
                'php-version-stats-january-2022',
                'php-in-2022',
                'how-i-plan',
                'twitter-home-made-me-miserable',
                'its-your-fault',
                'dealing-with-dependencies',
                'php-in-2021-video',
                'generics-in-php-1',
                'generics-in-php-2',
                'generics-in-php-3',
                'generics-in-php-4',
                'goodbye',
                'strategies',
                'dealing-with-deprecations',
                'attribute-usage-in-top-php-packages',
                'php-enum-style-guide',
                'clean-and-minimalistic-phpstorm',
                'stitcher-turns-5',
                'php-version-stats-july-2022',
                'evolution-of-a-php-object',
                'uncertainty-doubt-and-static-analysis',
                'road-to-php-82',
                'php-performance-across-versions',
                'light-colour-schemes-are-better',
                'deprecated-dynamic-properties-in-php-82',
                'php-reimagined-part-2',
                'thoughts-on-asymmetric-visibility',
                'uses',
                'php-82-in-8-code-blocks',
                'readonly-classes-in-php-82',
                'deprecating-spatie-dto',
                'php-82-upgrade-mac',
                'php-annotated',
                'you-cannot-find-me-on-mastodon',
                'new-in-php-82',
                'all-i-want-for-christmas',
                'upgrading-to-php-82',
                'php-version-stats-january-2023',
                'php-in-2023',
                'tabs-are-better',
                'sponsors',
                'why-curly-brackets-go-on-new-lines',
                'my-10-favourite-php-functions',
                'acronyms',
                'code-folding',
                'light-colour-schemes',
                'slashdash',
                'thank-you-kinsta',
                'cloning-readonly-properties-in-php-83',
                'limited-by-committee',
                'things-considered-harmful',
                'procedurally-generated-game-in-php',
                'dont-be-clever',
                'override-in-php-83',
                'php-version-stats-july-2023',
                'is-a-or-acts-as',
                'rfc-vote',
                'new-in-php-83',
                'i-dont-know',
                'passion-projects',
                'php-version-stats-january-2024',
                'the-framework-that-gets-out-of-your-way',
                'a-syntax-highlighter-that-doesnt-suck',
                'building-a-custom-language-in-tempest-highlight',
                'testing-patterns',
                'php-in-2024',
                'tagged-singletons',
                'twitter-exit',
                'a-vocal-minority',
                'php-version-stats-july-2024',
                'you-should',
                'new-with-parentheses-php-84',
                'html-5-in-php-84',
                'array-find-in-php-84',
                'its-all-just-text',
                'improved-lazy-loading',
                'i-dont-code-the-way-i-used-to',
                'php-84-at-least',
                'extends-vs-implements',
                'a-simple-approach-to-static-generation',
                'building-a-framework',
                'tagging-tempest-livestream',
                'things-i-learned-writing-a-fiction-novel',
                'unfair-advantage',
                'new-in-php-84',
                'php-version-stats-january-2025',
                'theoretical-engineers',
                'static-websites-with-tempest',
                'request-objects-in-tempest',
                'php-verse-2025',
                'tempest-discovery-explained',
                'php-version-stats-june-2025',
                'pipe-operator-in-php-85',
                'a-year-of-property-hooks',
                'readonly-or-private-set',
                'things-i-wish-i-knew',
                'impact-charts',
                'whats-your-motivator',
                'vendor-locked',
                'reducing-code-motion',
                'sponsoring-open-source',
                'my-wishlist-for-php-in-2026',
                'game-changing-editions',
                'new-in-php-85',
                'flooded-rss',
                'php-2026',
                'open-source-strategies',
                'not-optional',
                'processing-11-million-rows',
                'ai-induced-skepticism',
                'php-86-partial-function-application',
                '11-million-rows-in-seconds',
            ] as $slug
        ) {
            $slugBase[$slug]       = $numSlugs * $numDates;
            $slugIds[$slug]        = $numSlugs;
            $slugLabels[$numSlugs] = $slug;
            $numSlugs++;
        }

        $counts     = \array_fill(0, $numSlugs * $numDates, 0);
        $fileHandle = \fopen($inputPath, 'rb');
        \stream_set_read_buffer($fileHandle, 0);

        $probeChunk = \fread($fileHandle, \min($fileSize, 524_288));
        $probeNl    = \strrpos($probeChunk, "\n");
        $slugSeen   = \array_fill(0, $numSlugs, false);
        $slugOrder  = [];
        $probeFound = 0;
        if ($probeNl !== false) {
            $ppos = 25;
            while ($ppos < $probeNl) {
                $psep = \strpos($probeChunk, ',', $ppos + 4);
                if ($psep === false || $psep > $probeNl) {
                    break;
                }
                $pid = $slugIds[\substr($probeChunk, $ppos, $psep - $ppos)];
                if (!$slugSeen[$pid]) {
                    $slugOrder[]    = $pid;
                    $slugSeen[$pid] = true;
                    if (++$probeFound === $numSlugs) {
                        break;
                    }
                }
                $ppos = $psep + 52;
            }
        }
        for ($i = 0; $i < $numSlugs; $i++) {
            if (!$slugSeen[$i]) $slugOrder[] = $i;
        }
        \fseek($fileHandle, 0);

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

        foreach ($slugOrder as $slugId) {
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
