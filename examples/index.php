<?php

$autoloadFiles = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../autoload.php',
];

$autoloadLoaded = false;
foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require $autoloadFile;
        $autoloadLoaded = true;
        break;
    }
}

if (!$autoloadLoaded) {
    die('Unable to load Composer autoloader. Run "composer install" first.');
}

use Carbon\Carbon;
use RHDevelopment\Readable\Readable;

$date = '2020-08-26 17:38:23';
$old = Carbon::parse('2020-01-22 05:58:00', 'UTC');
$new = Carbon::parse('2020-01-23 05:58:00', 'UTC');

$examples = [
    [
        'title' => 'Numbers',
        'description' => 'Format plain numbers with custom delimiters.',
        'outputs' => [
            'Readable::getNumber(1234567890)' => Readable::getNumber(1234567890),
            'Readable::getNumber(-1234567, " ")' => Readable::getNumber(-1234567, ' '),
        ],
    ],
    [
        'title' => 'Human Numbers',
        'description' => 'Convert large values into friendly social-style abbreviations.',
        'outputs' => [
            'Readable::getHumanNumber(1524999)' => Readable::getHumanNumber(1524999),
            'Readable::getHumanNumber(1524999, true, 2)' => Readable::getHumanNumber(1524999, true, 2),
            'Readable::getHumanNumber(-3400000, false)' => Readable::getHumanNumber(-3400000, false),
        ],
    ],
    [
        'title' => 'Readable String',
        'description' => 'Spell out numbers as words.',
        'outputs' => [
            'Readable::readableString(12345)' => Readable::readableString(12345),
            'Readable::readableString(-42)' => Readable::readableString(-42),
        ],
    ],
    [
        'title' => 'Decimal Helpers',
        'description' => 'Format decimal values with different separators.',
        'outputs' => [
            "Readable::getDecimal(1234.567, 2, '.', ',')" => Readable::getDecimal(1234.567, 2, '.', ','),
            "Readable::getDecInt(200.0)" => Readable::getDecInt(200.0),
            "Readable::getDecInt(123.45)" => Readable::getDecInt(123.45),
        ],
    ],
    [
        'title' => 'Date & Time',
        'description' => 'Convert ISO strings into human friendly dates and times.',
        'outputs' => [
            "Readable::getDate('{$date}')" => Readable::getDate($date),
            "Readable::getTime('{$date}', true)" => Readable::getTime($date, true),
            "Readable::getDateTime('{$date}')" => Readable::getDateTime($date),
        ],
    ],
    [
        'title' => 'Relative Dates',
        'description' => 'Compare two points in time.',
        'outputs' => [
            'Readable::getDiffDateTime($old, $new)' => Readable::getDiffDateTime($old, $new),
            'Readable::getTimeLength(3661, ", ", true)' => Readable::getTimeLength(3661, ', ', true),
            'Readable::getDateTimeLength($new, $old)' => Readable::getDateTimeLength($new, $old),
        ],
    ],
    [
        'title' => 'File Size',
        'description' => 'Display byte counts using decimal or binary units.',
        'outputs' => [
            'Readable::getSize(1500)' => Readable::getSize(1500),
            'Readable::getSize(1024 ** 2, false)' => Readable::getSize(1024 ** 2, false),
        ],
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Numbers for Humans Demo</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 0;
            padding: 2rem;
            background: #f5f5f5;
            color: #1f2933;
        }
        header {
            max-width: 960px;
            margin: 0 auto 2rem;
        }
        header h1 {
            margin-bottom: 0.5rem;
        }
        section {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            max-width: 960px;
        }
        h2 {
            margin-top: 0;
        }
        dl {
            display: grid;
            grid-template-columns: minmax(280px, 45%) 1fr;
            gap: 0.75rem 1.5rem;
            margin: 0;
        }
        dt {
            font-family: "Fira Code", "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 0.95rem;
            color: #3a506b;
        }
        dd {
            margin: 0;
            font-weight: 600;
        }
        footer {
            text-align: center;
            color: #6c7a89;
            font-size: 0.85rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Numbers for Humans Demo</h1>
        <p>Examples showing every helper in the library. Update the code and refresh to experiment with new values.</p>
    </header>

    <?php foreach ($examples as $example): ?>
        <section>
            <h2><?= htmlspecialchars($example['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($example['description'], ENT_QUOTES, 'UTF-8') ?></p>
            <dl>
                <?php foreach ($example['outputs'] as $caption => $output): ?>
                    <dt><?= htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') ?></dt>
                    <dd><?= htmlspecialchars((string)$output, ENT_QUOTES, 'UTF-8') ?></dd>
                <?php endforeach; ?>
            </dl>
        </section>
    <?php endforeach; ?>

    <footer>
        Built with <strong>rhdevelopment/numbers-for-humans</strong>.
    </footer>
</body>
</html>
