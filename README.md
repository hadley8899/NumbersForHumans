# Numbers for Humans

Turn plain numbers, dates, and byte counts into friendly, human-oriented strings.  
`rhdevelopment/numbers-for-humans` wraps a handful of common formatting tasks into a single, lightweight helper class.

## Installation

```bash
composer require rhdevelopment/numbers-for-humans
```

The package requires PHP `^7.4 || ^8.0`, the `intl` extension, and [nesbot/carbon](https://carbon.nesbot.com/).

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use RHDevelopment\Readable\Readable;

echo Readable::getHumanNumber(1524999); // 1.5M
echo Readable::getDateTime('2020-08-26 17:38:23'); // Wednesday, August 26, 2020 05:38 PM
echo Readable::getSize(1500); // 1.5 KB
```

All methods are static, so you can call them directly from anywhere in your project.

## API overview

| Helper | Description |
| --- | --- |
| `getNumber(int $input, string $delimiter = ','): string` | Format integers with thousands separators. |
| `getHumanNumber(int|float|string $input, bool $showDecimal = true, int $decimals = 0): string` | Render “social” style abbreviations such as `1.5K` or `2.4M`. Handles negative numbers. |
| `readableString(int|float $input, string $lang = 'en'): string` | Convert numbers into spelled-out words via the PHP intl extension. |
| `getDecimal(int|float $input, int $decimals = 2, string $point = '.', string $delimiter = ','): string` | Format decimal numbers with configurable separators. |
| `getDecInt(int|float $input, int $decimals = 2, string $point = '.', string $delimiter = ','): string` | Automatically drop the fractional part when the value is a whole number. |
| `getDate(string $input, string $timezone = null): string` | Produce `day month year` strings from ISO-ish inputs or Carbon instances. |
| `getTime(string|Carbon\Carbon $input, bool $hasSeconds = false, string $timezone = null): string` | Format times as `HH:MM[:SS]`. |
| `getDateTime(string|Carbon\Carbon $input, bool $hasSeconds = false, string $timezone = null): string` | Combine date and time in an ISO-like readable format. |
| `getDiffDateTime(string|Carbon\Carbon $old, string|Carbon\Carbon|null $new = null, string $timezone = null): string` | Human readable difference between two timestamps (uses Carbon’s `diffForHumans`). |
| `getTimeLength(int $seconds, string $join = ' ', bool $short = false): string` | Convert durations in seconds to human friendly intervals. |
| `getDateTimeLength(string|Carbon\Carbon $old, string|Carbon\Carbon|null $new = null, string $join = ' ', string $timezone = null): string` | Express the length between two timestamps with granular parts. |
| `getSize(int $bytes, bool $decimal = true): ?string` | Convert byte counts into KB/MB/TB (decimal) or KiB/MiB/TiB (binary) units. |

### Detailed examples

```php
Readable::getNumber(1234567890);                // "1,234,567,890"
Readable::getNumber(-1234567, ' ');             // "-1 234 567"

Readable::getHumanNumber(1524999);              // "1.5M"
Readable::getHumanNumber(1524999, true, 2);     // "1.52M"
Readable::getHumanNumber(-3400000, false);      // "-3M"

Readable::readableString(12345);                // "twelve thousand three hundred forty-five"
Readable::readableString(-42);                  // "minus forty-two"

Readable::getDecimal(1234.567, 2);              // "1,234.57"
Readable::getDecInt(200.0);                     // "200"

Readable::getDate('2020-08-26 17:38:23');       // "26 August 2020"
Readable::getTime('2020-08-26 17:38:23', true); // "17:38:23"
Readable::getDateTime('2020-08-26 17:38:23');   // "Wednesday, August 26, 2020 05:38 PM"

$old = '2020-01-22 05:58:00';
$new = '2020-01-23 05:58:00';
Readable::getDiffDateTime($old, $new);          // "1 day before"
Readable::getTimeLength(3661, ', ', true);      // "1h, 1m, 1s"
Readable::getDateTimeLength($new, $old);        // "1 day after"

Readable::getSize(1500);                        // "1.5 KB"
Readable::getSize(1024 ** 2, false);            // "1 MiB"
```

## CLI demo

Install the package (or run `composer install` inside this repository) and execute:

```bash
vendor/bin/numbers-for-humans
```

You’ll see every helper in action with sample inputs and outputs. The CLI script automatically locates the Composer autoloader whether you’re working from this repository or using the package as a dependency.

## Web demo

There’s also a small showcase page at `examples/index.php`.

```bash
composer install
php -S 127.0.0.1:8000 -t examples
```

Then browse to [http://127.0.0.1:8000](http://127.0.0.1:8000) to experiment interactively.

## Testing

Run the full PHPUnit suite:

```bash
composer install
composer test
```

The test suite covers every helper, including edge cases for negative numbers, timezone conversions, and binary versus decimal file size calculations.

## Releasing on Packagist

1. Ensure the repository is public on GitHub.
2. Tag a semantic version: `git tag v1.0.0 && git push --tags`.
3. Submit the repository URL (`https://github.com/rhdevelopment/NumbersForHumans`) to [Packagist.org](https://packagist.org/packages/submit).
4. Configure the GitHub service hook or Packagist auto-update to keep releases in sync.

Once published, others can install the package with `composer require rhdevelopment/numbers-for-humans`.

## License

Licensed under the [MIT License](LICENSE).
