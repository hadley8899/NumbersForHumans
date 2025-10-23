<?php

namespace RHDevelopment\Readable\Tests;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use PHPUnit\Framework\TestCase;
use RHDevelopment\Readable\Readable;
use RuntimeException;
use TypeError;

class ReadableTest extends TestCase
{
    /**
     * @dataProvider numberProvider
     */
    public function testGetNumberFormatsIntegers(int $value, string $delimiter, string $expected): void
    {
        self::assertSame($expected, Readable::getNumber($value, $delimiter));
    }

    public static function numberProvider(): array
    {
        return [
            'zero stays zero' => [0, ',', '0'],
            'positive with commas' => [1000, ',', '1,000'],
            'positive with spaces' => [1000, ' ', '1 000'],
            'negative values' => [-12345, ',', '-12,345'],
            'no delimiter' => [654321, '', '654321'],
        ];
    }

    /**
     * @dataProvider humanNumberProvider
     */
    public function testGetHumanNumberFormatsCorrectly($input, string $expected): void
    {
        self::assertSame($expected, Readable::getHumanNumber($input));
    }

    public static function humanNumberProvider(): array
    {
        return [
            'hundreds include single decimal' => [999, '999.0'],
            'thousand rounds to 1 decimal' => [1000, '1.0K'],
            'thousands with remainder' => [2500, '2.5K'],
            'hundreds of thousands' => [187764, '187.8K'],
            'millions' => [1000000, '1.0M'],
            'billions' => [1200000000, '1.2B'],
            'trillions' => [1846765676000, '1.8T'],
            'numeric string input' => ['12345', '12.3K'],
            'negative numbers keep sign' => [-1500, '-1.5K'],
            'zero returns zero string' => [0, '0'],
        ];
    }

    public function testGetHumanNumberWithoutDecimals(): void
    {
        self::assertSame('1K', Readable::getHumanNumber(1500, false));
        self::assertSame('-3M', Readable::getHumanNumber(-3400000, false));
    }

    public function testGetHumanNumberCustomDecimals(): void
    {
        self::assertSame('1.52M', Readable::getHumanNumber(1524999, true, 2));
    }

    public function testGetHumanNumberThrowsForNonNumericValues(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The value must be numeric!');
        Readable::getHumanNumber('invalid');
    }

    /**
     * @dataProvider readableStringProvider
     */
    public function testReadableStringReturnsSpelledOutNumbers($input, string $expected): void
    {
        self::assertSame($expected, Readable::readableString($input));
    }

    public static function readableStringProvider(): array
    {
        return [
            'simple number' => [1, 'one'],
            'three digit number' => [123, 'one hundred twenty-three'],
            'five digit number' => [12345, 'twelve thousand three hundred forty-five'],
            'negative number' => [-42, 'minus forty-two'],
        ];
    }

    public function testReadableStringThrowsForInvalidTypes(): void
    {
        $this->expectException(TypeError::class);
        Readable::readableString('bad input!');
    }

    /**
     * @dataProvider decimalProvider
     */
    public function testGetDecimalFormatsNumbersCorrectly($input, int $decimals, string $point, string $delimiter, string $expected): void
    {
        self::assertSame($expected, Readable::getDecimal($input, $decimals, $point, $delimiter));
    }

    public static function decimalProvider(): array
    {
        return [
            'integer with defaults' => [123, 2, '.', ',', '123.00'],
            'large number adds delimiter' => [1000, 2, '.', ',', '1,000.00'],
            'rounding with decimals' => [200.8, 2, '.', ',', '200.80'],
            'custom decimal and delimiter' => [1234.56, 1, ',', '.', '1.234,6'],
        ];
    }

    public function testGetDecimalRejectsInvalidTypes(): void
    {
        $this->expectException(TypeError::class);
        Readable::getDecimal('test');
    }

    /**
     * @dataProvider decIntProvider
     */
    public function testGetDecIntConvertsFloatsWithWholeValues($input, string $expected): void
    {
        self::assertSame($expected, Readable::getDecInt($input));
    }

    public static function decIntProvider(): array
    {
        return [
            'integer retains decimals by default' => [100, '100.00'],
            'float with fraction keeps decimals' => [123.45, '123.45'],
            'float without fraction strips decimals' => [200.0, '200'],
            'negative float without fraction strips decimals' => [-200.0, '-200'],
        ];
    }

    public function testGetDecIntRejectsInvalidTypes(): void
    {
        $this->expectException(TypeError::class);
        Readable::getDecInt('test');
    }

    /**
     * @dataProvider dateProvider
     */
    public function testGetDateFormatsStrings(string $input, string $expected): void
    {
        self::assertSame($expected, Readable::getDate($input));
    }

    public static function dateProvider(): array
    {
        return [
            ['2003-05-25', '25 May 2003'],
            ['2038-02-28', '28 February 2038'],
            ['29-05-2008', '29 May 2008'],
            ['2020-08-26 17:38:23', '26 August 2020'],
            ['2019/04/17', '17 April 2019'],
            ['04/17/27', '17 April 2027'],
        ];
    }

    public function testGetDateRespectsTimezones(): void
    {
        $input = '2020-08-26 01:00:00 UTC';
        self::assertSame('25 August 2020', Readable::getDate($input, 'America/New_York'));
    }

    public function testGetDateThrowsOnInvalidFormat(): void
    {
        $this->expectException(InvalidFormatException::class);
        Readable::getDate('not-a-date');
    }

    public function testGetTimeFormatsWithAndWithoutSeconds(): void
    {
        $input = '2020-08-26 17:38:23';
        self::assertSame('17:38', Readable::getTime($input));
        self::assertSame('17:38:23', Readable::getTime($input, true));
    }

    public function testGetTimeHandlesCarbonInstanceAndTimezone(): void
    {
        $input = Carbon::parse('2020-08-26 17:38:23', 'UTC');
        self::assertSame('18:38', Readable::getTime($input, false, 'Europe/London'));
    }

    public function testGetTimeThrowsOnInvalidFormat(): void
    {
        $this->expectException(InvalidFormatException::class);
        Readable::getTime('bad-time');
    }

    public function testGetDateTimeFormatsWithOptionalSeconds(): void
    {
        $input = '2020-08-26 17:38:23';
        self::assertSame('Wednesday, August 26, 2020 05:38 PM', Readable::getDateTime($input));
        self::assertSame('Wednesday, August 26, 2020 05:38:23 PM', Readable::getDateTime($input, true));
    }

    public function testGetDateTimeWithCarbonInputAndTimezone(): void
    {
        $input = Carbon::parse('2020-08-26 17:38:23', 'UTC');
        self::assertSame('Wednesday, August 26, 2020 07:38 PM', Readable::getDateTime($input, false, 'Europe/Paris'));
    }

    public function testGetDateTimeThrowsOnInvalidFormat(): void
    {
        $this->expectException(InvalidFormatException::class);
        Readable::getDateTime('invalid');
    }

    public function testGetDiffDateTimeSupportsStringsAndCarbonInstances(): void
    {
        $old = '2020-01-22 05:58:00';
        $new = '2020-01-22 05:59:00';
        self::assertSame('1 minute before', Readable::getDiffDateTime($old, $new));

        $oldCarbon = Carbon::parse('2020-01-22 05:58:00', 'UTC');
        $newCarbon = Carbon::parse('2020-01-23 05:58:00', 'UTC');
        self::assertSame('1 day before', Readable::getDiffDateTime($oldCarbon, $newCarbon, 'Europe/Berlin'));
    }

    public function testGetTimeLengthProducesHumanReadableOutput(): void
    {
        self::assertSame('2 minutes 3 seconds', Readable::getTimeLength(123));
        self::assertSame('1h, 1m, 1s', Readable::getTimeLength(3661, ', ', true));
    }

    public function testGetDateTimeLength(): void
    {
        $old = '2020-01-02 13:46:12';
        $new = '2020-01-02 13:48:14';
        self::assertSame('2 minutes 2 seconds before', Readable::getDateTimeLength($old, $new));
        self::assertSame(
            '1 hour • 30 minutes after',
            Readable::getDateTimeLength(
                Carbon::parse('2020-01-02 14:48:14'),
                Carbon::parse('2020-01-02 13:18:14'),
                ' • ',
                'Europe/London'
            )
        );
    }

    public function testGetSizeReturnsNullForNonPositiveValues(): void
    {
        self::assertNull(Readable::getSize(0));
        self::assertNull(Readable::getSize(-10));
    }

    public function testGetSizeFormatsBytesInDecimalAndBinary(): void
    {
        self::assertSame('1 KB', Readable::getSize(1000));
        self::assertSame('1.5 KB', Readable::getSize(1500));
        self::assertSame('1 KiB', Readable::getSize(1024, false));
        self::assertSame('1 MiB', Readable::getSize(1024 ** 2, false));
    }

    /**
     * @dataProvider ordinalProvider
     */
    public function testGetOrdinalReturnsCorrectSuffixes(int $input, string $expected): void
    {
        self::assertSame($expected, Readable::getOrdinal($input));
    }

    public static function ordinalProvider(): array
    {
        return [
            'first' => [1, '1st'],
            'second' => [2, '2nd'],
            'third' => [3, '3rd'],
            'fourth' => [4, '4th'],
            'eleventh' => [11, '11th'],
            'twelfth' => [12, '12th'],
            'thirteenth' => [13, '13th'],
            'twentyfirst' => [21, '21st'],
            'hundredandsecond' => [102, '102nd'],
            'negative' => [-1, '-1st'],
            'zero' => [0, '0th'],
        ];
    }

    /**
     * @dataProvider percentageProvider
     */
    public function testGetPercentageFormatsCorrectly($value, $total, $decimals, $expected): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            // call in a way that will throw
            Readable::getPercentage($value, $total, $decimals);
        } else {
            self::assertSame($expected, Readable::getPercentage($value, $total, $decimals));
        }
    }

    public static function percentageProvider(): array
    {
        return [
            'simple percentage' => [50, 200, 2, '25.00%'],
            'integer division' => [1, 3, 2, '33.33%'],
            'numeric strings' => ['25', '100', 0, '25%'],
            'total zero returns null' => [5, 0, 2, null],
            'non numeric throws' => ['a', 10, 2, new RuntimeException('Both value and total must be numeric!')],
        ];
    }
}
