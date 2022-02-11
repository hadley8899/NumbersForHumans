<?php

namespace RHDevelopment\Readable\Tests;

use Carbon\Exceptions\InvalidFormatException;
use PHPUnit\Framework\TestCase;
use RHDevelopment\Readable\Readable;

class ReadableTest extends TestCase
{
    public function testGetNumberWithNoDelimiter()
    {
        for ($i = 0; $i < 100000; $i++) {
            $answer = Readable::getNumber($i, '');
            self::assertEquals($i, $answer);
        }
    }

    public function testGetNumberWithDelimiter()
    {
        $answer = Readable::getNumber(1000, ',');
        self::assertEquals('1,000', $answer);
    }

    public function testGetHumanNumber()
    {
        $answers = [
            [
                'expected' => '1.0K',
                'input' => 1000,
            ],
            [
                'expected' => '2.0K',
                'input' => 2000,
            ],
            [
                'expected' => '2.5K',
                'input' => 2500,
            ],
            [
                'expected' => '8.2K',
                'input' => 8234,
            ],
            [
                'expected' => '100.0K',
                'input' => 100000,
            ],
            [
                'expected' => '187.8K',
                'input' => 187764,
            ],
            [
                'expected' => '1.0M',
                'input' => 1000000,
            ],
            [
                'expected' => '2.0M',
                'input' => 2000000,
            ],
            [
                'expected' => '2.4M',
                'input' => 2400000,
            ],
            [
                'expected' => '2.8M',
                'input' => 2834567,
            ],
            [
                'expected' => '10.0M',
                'input' => 10000000,
            ],
            [
                'expected' => '100.0M',
                'input' => 100000000,
            ],
            [
                'expected' => '1.0B',
                'input' => 1000000000,
            ],
            [
                'expected' => '1.2B',
                'input' => 1200000000,
            ],
            [
                'expected' => '1.8B',
                'input' => 1846765676,
            ],
            [
                'expected' => '18.5B',
                'input' => 18467656760,
            ],
            [
                'expected' => '184.7B',
                'input' => 184676567600,
            ],
            [
                'expected' => '1.8T',
                'input' => 1846765676000,
            ],
        ];

        foreach ($answers as $v) {
            $result = Readable::getHumanNumber($v['input']);
            self::assertEquals($v['expected'], $result);
        }
    }

    public function testGetHumanNumberWithBadInput()
    {
        $this->expectException('RunTimeException');
        Readable::getHumanNumber('bad');
    }

    public function testReadableString()
    {
        $numbersToTest = [
            12345 => 'twelve thousand three hundred forty-five',
            123 => 'one hundred twenty-three',
            1 => 'one',
            500 => 'five hundred',
            456547657345567 => 'four hundred fifty-six trillion five hundred forty-seven billion six hundred fifty-seven million three hundred forty-five thousand five hundred sixty-seven',
        ];

        foreach ($numbersToTest as $number => $expected) {
            $output = Readable::readableString($number);
            self::assertEquals($expected, $output);
        }
    }

    public function testReadableStringError()
    {
        $this->expectException('TypeError');
        Readable::readableString('bad input!');
    }

    public function testGetDecimal()
    {
        $numbersToTest = [
            123.00 => '123.00',
            1000 => '1,000.00',
            200.8 => '200.00',
            456547657345567 => '456,547,657,345,567.00'
        ];

        foreach ($numbersToTest as $number => $expected) {
            $result = Readable::getDecimal($number);
            self::assertEquals($expected, $result);
        }

        $this->expectException('TypeError');
        Readable::getDecimal('test');
    }

    public function testGetDecInt()
    {
        $numbersToTest = [
            123.00 => '123.00',
            1000 => '1,000.00',
            200.8 => '200.00',
            456547657345567 => '456,547,657,345,567.00'
        ];

        foreach ($numbersToTest as $number => $expected) {
            $result = Readable::getDecInt($number);
            self::assertEquals($expected, $result);
        }

        $this->expectException('TypeError');
        Readable::getDecInt('test');
    }

    public function testGetDate()
    {
        $datesToTest = [
            '2003-05-25' => '25 May 2003',
            '2038-02-28' => '28 February 2038',
            '29-05-2008' => '29 May 2008',
            '2020-08-26 17:38:23' => '26 August 2020',
            '2019/04/17' => '17 April 2019',
            '04/17/27' => '17 April 2027',
        ];

        foreach ($datesToTest as $date => $expected) {
            $result = Readable::getDate($date);

            self::assertEquals($expected, $result);
        }

        $this->expectException(InvalidFormatException::class);
        Readable::getDate('dofjbndfb');
    }

    public function testGetTime()
    {
        $datesToTest = [
            '2003-05-25 17:05' => [
                'without' => '17:05',
                'with' => '17:05:00',
            ],
            '2038-02-28 12:54' => [
                'without' => '12:54',
                'with' => '12:54:00',
            ],
            '29-05-2008 1:01AM' => [
                'without' => '01:01',
                'with' => '01:01:00',
            ],
            '2020-08-26 17:38:23' => [
                'without' => '17:38',
                'with' => '17:38:23',
            ],
            '2019/04/17 17:38:23' => [
                'without' => '17:38',
                'with' => '17:38:23',
            ],
            '04/17/27 12:31' => [
                'without' => '12:31',
                'with' => '12:31:00',
            ],
        ];

        foreach ($datesToTest as $date => $expected) {
            $result = Readable::getTime($date);
            self::assertEquals($result, $expected['without']);
            $resultWithSeconds = Readable::getTime($date, true);
            self::assertEquals($expected['with'], $resultWithSeconds);
        }

        $this->expectException(InvalidFormatException::class);
        Readable::getDate('dofjbndfb');
    }

    public function testGetDateTime()
    {
        $datesToTest = [
            '2003-05-25' => 'Sunday, May 25, 2003 12:00 AM',
            '2038-02-28' => 'Sunday, February 28, 2038 12:00 AM',
            '29-05-2008' => 'Thursday, May 29, 2008 12:00 AM',
            '2020-08-26 17:38:23' => 'Wednesday, August 26, 2020 05:38 PM',
            '2019/04/17' => 'Wednesday, April 17, 2019 12:00 AM',
            '04/17/27' => 'Saturday, April 17, 2027 12:00 AM',
        ];

        foreach ($datesToTest as $date => $expected) {
            $result = Readable::getDateTime($date);

            self::assertEquals($expected, $result);
        }

        $this->expectException(InvalidFormatException::class);
        Readable::getDateTime('dofjbndfb');
    }

    public function testGetDiffDateTime()
    {
        $datesToTest = [
            0 => [
                'old' => '2020-01-22 5:58:00',
                'new' => '2020-01-22 5:59:00',
                'expected' => '1 minute before',
            ],
            1 => [
                'old' => '2019-01-22 5:58:00',
                'new' => '2020-01-22 5:59:00',
                'expected' => '1 year before',
            ],
            2 => [
                'old' => '2018-01-22 5:58:00',
                'new' => '2020-01-22 5:59:00',
                'expected' => '2 years before',
            ],
            3 => [
                'old' => '2018-02-22 5:58:00',
                'new' => '2020-01-22 5:59:00',
                'expected' => '1 year before',
            ],
            4 => [
                'old' => '2020-01-22 5:58:00',
                'new' => '2020-02-22 5:59:00',
                'expected' => '1 month before',
            ],
            5 => [
                'old' => '2020-01-22 5:58:00',
                'new' => '2020-03-22 5:59:00',
                'expected' => '2 months before',
            ],
            6 => [
                'old' => '2020-03-22 5:58:00',
                'new' => '2020-03-29 5:59:00',
                'expected' => '1 week before',
            ],
        ];

        foreach ($datesToTest as $dateToTest) {
            $result = Readable::getDiffDateTime($dateToTest['old'], $dateToTest['new']);

            self::assertEquals($dateToTest['expected'], $result);
        }
    }

    public function testGetTimeLength()
    {
        $datesToTest = [
            123 => '2 minutes 3 seconds',
            60 => '1 minute',
            120 => '2 minutes',
            4565324456745663454576 => '288666105304 years 10 months 3 weeks 6 days 23 hours 8 minutes 48 seconds',
            5678734 => '2 months 5 days 17 hours 25 minutes 34 seconds',
        ];

        foreach ($datesToTest as $dateToTest => $expected) {
            $result = Readable::getTimeLength($dateToTest);

            self::assertEquals($expected, $result);
        }
    }

    public function testGetDateTimeLength()
    {
        $datesToTest = [
            0 => [
                'old' => '2020-01-02 13:46:12',
                'new' => '2020-01-02 13:48:14',
                'expected' => '2 minutes 2 seconds before',
            ],
            1 => [
                'old' => '2020-01-02 13:48:14',
                'new' => '2020-01-02 13:46:12',
                'expected' => '2 minutes 2 seconds after',
            ],
            2 => [
                'old' => '2022-01-02 13:48:14',
                'new' => '2020-01-02 13:46:12',
                'expected' => '2 years 2 minutes 2 seconds after',
            ],
        ];

        foreach ($datesToTest as $dateArray) {
            $result = Readable::getDateTimeLength($dateArray['old'], $dateArray['new']);

            self::assertEquals($dateArray['expected'], $result);
        }
    }

    public function testGetSize()
    {
        $numbersToTest = [
            1 => '1 B',
            12 => '12 B',
            123 => '123 B',
            1234 => '1.23 KB',
            12345 => '12.35 KB',
            123456 => '123.46 KB',
            1234567 => '1.23 MB',
            12345678 => '12.35 MB',
            123456789 => '123.46 MB',
            1234567891 => '1.23 GB',
            12345678912 => '12.35 GB',
            123456789123 => '123.46 GB',
            1234567891234 => '1.23 TB',
            12345678912345 => '12.35 TB',
            123456789123456 => '123.46 TB',
            1234567891234567 => '1.23 PB',
            12345678912345678 => '12.35 PB',
            123456789123456789 => '123.46 PB',
            1234567891234567891 => '1.23 EX',
            PHP_INT_MAX => '9.22 EX',
        ];

        foreach($numbersToTest as $number => $expected) {
            $result = Readable::getSize($number);

            self::assertEquals($expected, $result);
        }
    }
}
