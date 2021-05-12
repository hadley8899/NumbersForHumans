<?php

namespace RHDevelopment\Readable\Tests;

use PHPUnit\Framework\TestCase;
use RHDevelopment\Readable\Readable;

class ReadableTest extends TestCase
{
    public function testGetNumberWithNoDelimiter()
    {
        for($i = 0; $i < 100000; $i++) {
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
                'expected' => '1K',
                'input' => 1000,
            ],
            [
                'expected' => '2K',
                'input' => 2000,
            ],
            [
                'expected' => '2K',
                'input' => 2500,
            ],
            [
                'expected' => '8K',
                'input' => 8234,
            ],
            [
                'expected' => '100K',
                'input' => 100000,
            ],
            [
                'expected' => '187K',
                'input' => 187764,
            ],
            [
                'expected' => '1M',
                'input' => 1000000,
            ],
            [
                'expected' => '2M',
                'input' => 2000000,
            ],
            [
                'expected' => '2.6M',
                'input' => 2500000,
            ],
        ];

        foreach($answers as $v) {
            $result = Readable::getHumanNumber($v['input']);
            self::assertEquals($v['expected'], $result);
        }
    }
}
