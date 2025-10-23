<?php

namespace RHDevelopment\Readable;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use NumberFormatter;
use RuntimeException;
use TypeError;

class Readable
{
    /**
     * Get Readable Integer Number
     *
     * @param int $input
     * @param string $delimiter
     * @return string
     */
    public static function getNumber(int $input, string $delimiter = ','): string
    {
        return number_format($input, 0, '.', $delimiter);
    }

    /**
     * Get Readable Social Number
     *
     * @param int|float|string $input
     * @param bool $showDecimal
     * @param int $decimals
     * @return string
     **/
    public static function getHumanNumber($input, bool $showDecimal = true, int $decimals = 0): string
    {
        if (!is_numeric($input)) {
            throw new RuntimeException('The value must be numeric!');
        }

        $decimals = $showDecimal && $decimals === 0 ? 1 : $decimals;
        $value = (float)$input;
        $sign = $value < 0 ? '-' : '';
        $value = abs($value);

        $floorNumber = 0;
        $getFloor = floor(0);
        $suffix = '';

        if ($value >= 0 && $value < 1000) {
            // 1 - 999
            $getFloor = floor($value);
        } elseif ($value >= 1000 && $value < 1000000) {
            // 1k-999k
            $getFloor = floor($value / 1000);
            $floorNumber = 1000;
            $suffix = 'K';
        } elseif ($value >= 1000000 && $value < 1000000000) {
            // 1m-999m
            $getFloor = floor($value / 1000000);
            $floorNumber = 1000000;
            $suffix = 'M';
        } elseif ($value >= 1000000000 && $value < 1000000000000) {
            // 1b-999b
            $getFloor = floor($value / 1000000000);
            $floorNumber = 1000000000;
            $suffix = 'B';
        } elseif ($value >= 1000000000000) {
            // 1t+
            $getFloor = floor($value / 1000000000000);
            $floorNumber = 1000000000000;
            $suffix = 'T';
        }

        // Decimals
        if ($showDecimal && $floorNumber > 0) {
            $value -= ($getFloor * $floorNumber);
            if ($value > 0) {
                $value /= $floorNumber;
                $getFloor += $value;
            }
        }

        if (empty($getFloor . $suffix)) {
            return '0';
        }

        return ($sign ? '-' : '') . number_format($getFloor, $decimals) . $suffix;
    }

    /**
     * Get Readable String of Number
     *
     * @param int|double $input
     * @param string $lang
     * @return string
     **/
    public static function readableString($input, string $lang = 'en'): string
    {
        if (!in_array(gettype($input), ['integer', 'double', 'float'])) {
            throw new TypeError('Wrong Input Type.');
        }

        $digit = new NumberFormatter($lang, NumberFormatter::SPELLOUT);
        $formattedString = $digit->format($input);

        if (!$formattedString) {
            return 'unknown';
        }

        return $lang === 'ar' ? str_replace('و ', 'و', $formattedString) : $formattedString;
    }

    /**
     * Get Readable Decimal Number
     *
     * @param int|double $input
     * @param int $decimals
     * @param string $point
     * @param string $delimiter
     * @return string
     **/
    public static function getDecimal($input, int $decimals = 2, string $point = '.', string $delimiter = ','): ?string
    {
        if (!in_array(gettype($input), ['integer', 'double', 'float'])) {
            throw new TypeError('Wrong Input Type.');
        }

        return number_format($input, $decimals, $point, $delimiter);
    }

    /**
     * Get Readable ( Decimal Number => Decimal || Integer )
     *
     * @param int|float $input
     * @param int $decimals_length
     * @param string $point
     * @param string $delimiter
     * @return string
     **/
    public static function getDecInt($input, int $decimals_length = 2, string $point = '.', string $delimiter = ','): ?string
    {
        if (!in_array(gettype($input), ['integer', 'double', 'float'])) {
            throw new TypeError("Wrong Input Type.");
        }

        // Convert Decimal to Integer if $decimals_length == 0 || use the limiter
        if (is_float($input)) {
            $decInt = $input - (int)$input;

            if (abs($decInt) < PHP_FLOAT_EPSILON) {
                $input = (int)$input;
                $decimals_length = 0;
            }
        }

        return number_format($input, $decimals_length, $point, $delimiter);
    }

    /**
     * Prepare DateTime Variable => Object
     *
     *
     * @param string|Carbon $input
     * @param null|string $tz
     * @return Carbon
     * @throws InvalidFormatException
     */
    public static function prepareDateTime($input, string $tz = null)
    {
        $carbon = $input instanceof Carbon ? $input->copy() : Carbon::parse($input);

        if ($tz) {
            $carbon->setTimezone($tz);
        }

        return $carbon;
    }

    /**
     * Get Readable Date
     *
     * @param string $input
     * @param string|null $timezone
     * @return string
     */
    public static function getDate(string $input, string $timezone = null): ?string
    {
        $input = self::prepareDateTime($input, $timezone);
        return $input->day . ' ' . $input->monthName . ' ' . $input->year;
    }

    /**
     * Get Readable Time
     *
     * @param int|Carbon $input
     * @param bool $hasSeconds
     * @param null|string $timezone
     * @return string
     */
    public static function getTime($input, bool $hasSeconds = false, string $timezone = null): ?string
    {
        $input = self::prepareDateTime($input, $timezone);

        return $input->format('H:i' . ($hasSeconds ? ':s' : ''));
    }

    /**
     * Get Readable DateTime
     *
     * @param int|Carbon $input
     * @param bool $hasSeconds
     * @param null|string $timezone
     * @return string
     */
    public static function getDateTime($input, bool $hasSeconds = false, string $timezone = null): ?string
    {
        $input = self::prepareDateTime($input, $timezone);
        return $input->isoFormat('dddd, MMMM DD, YYYY ' . 'hh:mm' . ($hasSeconds ? ':ss' : '') . ' A');
    }

    /**
     * Get Readable DateTime
     *
     * @param int|Carbon $old
     * @param null|int|Carbon $new
     * @param null|string $timezone
     * @return string
     **/
    public static function getDiffDateTime($old, $new = null, string $timezone = null): ?string
    {
        $old = self::prepareDateTime($old, $timezone);
        $new = self::prepareDateTime($new, $timezone);

        return $old->diffForHumans($new);
    }

    /**
     * Get Readable DateTime Length from Seconds
     *
     * @param int $input
     * @param string $comma
     * @param boolean $short
     * @return string
     *
     * @throws Exception
     */
    public static function getTimeLength(int $input, string $comma = ' ', bool $short = false): ?string
    {
        //years
        $years = floor($input / 31104000);
        $input -= $years * 31104000;

        //months
        $months = floor($input / 2592000);
        $input -= $months * 2592000;

        //days
        $days = floor($input / 86400);
        $input -= $days * 86400;

        //hours
        $hours = floor($input / 3600);
        $input -= $hours * 3600;

        //minutes
        $minutes = floor($input / 60);
        $input -= $minutes * 60;

        //seconds
        $seconds = $input % 60;

        $obj = new CarbonInterval($years, $months, null, $days, $hours, $minutes, $seconds);
        return $obj->forHumans(['join' => $comma, 'short' => $short]);
    }

    /**
     * Get Readable DateTime Length from DateTimes
     *
     * @param int|Carbon $old
     * @param null|int|Carbon $new
     * @param bool $full
     * @param string $comma
     * @param null|string $timezone
     * @return string
     */
    public static function getDateTimeLength($old, $new = null, string $comma = ' ', string $timezone = null): ?string
    {
        $old = self::prepareDateTime($old, $timezone);
        $new = self::prepareDateTime($new, $timezone);

        return $old->diffForHumans($new, ['parts' => 7, 'join' => $comma]);
    }

    /**
     * Get Readable File Size
     *
     * @param int $bytes
     * @param bool $decimal
     * @return string
     */
    public static function getSize(int $bytes, bool $decimal = true): ?string
    {
        if ($bytes <= 0) {
            return null;
        }

        $calcBase = $decimal ? 1000 : 1024;

        $bytes = (int)$bytes;
        $base = log($bytes) / log($calcBase);
        // Correct binary/decimal suffixes (EB / EiB for exabytes)
        $suffixes = $decimal ? ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'] : ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB'];

        return round($calcBase ** ($base - floor($base)), 2) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Get percentage string for a value out of a total (e.g. "40.00%")
     *
     * @param int|float|string $value
     * @param int|float|string $total
     * @param int $decimals
     * @param string $point
     * @param string $delimiter
     * @return string|null
     */
    public static function getPercentage($value, $total, int $decimals = 2, string $point = '.', string $delimiter = ','): ?string
    {
        if (!is_numeric($value) || !is_numeric($total)) {
            throw new RuntimeException('Both value and total must be numeric!');
        }

        $total = (float)$total;
        if ($total == 0.0) {
            return null;
        }

        $percent = ((float)$value / $total) * 100;

        return number_format($percent, $decimals, $point, $delimiter) . '%';
    }

    /**
     * Get ordinal string for integers (English, e.g. 1st, 2nd, 3rd, 11th)
     *
     * @param int $number
     * @return string
     */
    public static function getOrdinal(int $number): string
    {
        // Preserve sign
        $sign = $number < 0 ? '-' : '';
        $abs = abs($number);

        $mod100 = $abs % 100;
        if ($mod100 >= 11 && $mod100 <= 13) {
            $suffix = 'th';
        } else {
            switch ($abs % 10) {
                case 1:
                    $suffix = 'st';
                    break;
                case 2:
                    $suffix = 'nd';
                    break;
                case 3:
                    $suffix = 'rd';
                    break;
                default:
                    $suffix = 'th';
            }
        }

        return $sign . $abs . $suffix;
    }
}
