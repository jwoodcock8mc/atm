<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class AtmTest extends TestCase
{
    /**
     * Test 1 : test input and output matches specification
     *
     * @return void
     */
    public function testMeetsSpecification()
    {
        $filename = "test-meets-spec";
        $expected = [
            500,
            400,
            90,
            "FUNDS_ERR",
            0
        ];
        Artisan::call('atm:process-and-output', [
            'filename' => $filename
        ]);
        $actual = $this->getOutputDataFromFile($filename);
        if ($this->assertEquals($expected, json_decode($actual))) {
            $this->info('test 1 successful');
        }
    }

    /**
     * Test 2 : ATM_ERR returned when ATM has less than withdrawal amount
     */
    public function testAtmErr()
    {
        $filename = "test-atm-err";
        $expected = [
            "ATM_ERR"
        ];
        Artisan::call('atm:process-and-output', [
            'filename' => $filename
        ]);
        $actual = $this->getOutputDataFromFile($filename);
        $this->assertEquals($expected, json_decode($actual));
    }

    /**
     * Test 3 ACCOUNT_ERR returned when actual PIN and entered PIN don't match, and nothing after ACCOUNT_ERR.
     */
    public function testAccountErr()
    {
        $filename = "test-account-err";
        $expected = [
            "ACCOUNT_ERR"
        ];
        Artisan::call('atm:process-and-output', [
            'filename' => $filename
        ]);
        $actual = $this->getOutputDataFromFile($filename);
        $this->assertEquals($expected, json_decode($actual));
    }

    /**
     * @param $filename
     * @return bool|string
     */
    protected function getOutputDataFromFile($filename) {
        $path = storage_path() . "/json/" . $filename . "-output.json";
        return file_get_contents($path);
    }
}
