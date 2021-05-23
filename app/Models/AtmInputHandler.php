<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AtmInputHandler extends Model
{
    /**
     * @param $filename
     * @return mixed|string
     */
    public function processAtmData($filename)
    {
        if(empty($filename)) {
            return [
                "status" => "failure",
                "errorMessage" => "Please supply the --filename= argument, e.g. --filename=atm-input.json",
                "data" => ""
            ];
        }
        $decoded = $this->decodeJson($filename);
        if ($decoded['status'] === "failure") {
            return $decoded;
        }
        $atmResponse = $this->atmLogic($decoded['data']);
        if ($atmResponse['status'] === 'success') {
            $this->writeJsonOutputFile($filename, $atmResponse['data']);
        }
        return $atmResponse;
    }

    protected function writeJsonOutputFile($filename, $data)
    {
        $path = storage_path() . "/json/" . $filename . "-output.json";
        file_put_contents($path, json_encode($data));
        return;
    }

    /**
     * @param $filename
     * @return array - status (success / failure), errorMessage, data.
     */
    protected function decodeJson($filename)
    {
        $path = storage_path() . "/json/" . $filename . ".json";
        $json = json_decode(file_get_contents($path), true);
        if (empty ($json)){
            return [
                "status" => "failure",
                "errorMessage" => "Empty JSON file, please try another filename, or edit the json",
                "data" => ""
            ];
        }
        return [
            "status" => "success",
            "errorMessage" => "",
            "data" => $json
        ];
    }

    protected function atmLogic($inputLines)
    {
        $atmBalance = $inputLines[0];
        if(!empty($inputLines[1])) {
            return [
                "status" => "failure",
                "errorMessage" => "Incorrectly formatted file - line after ATM balance should be empty",
                "data" => ""
            ];
        }
        $noOfLines = count($inputLines);
        $response = [];
        $accountErr = false;
        $currentBalance = 0;
        $overdraft = 0;
        for ($index = 2; $index < $noOfLines; $index++) {
            $thisLine = $inputLines[$index];
            if (Line::isAccountAndPin($thisLine) && Line::accountAndPinLineValid($thisLine) === false) {
                $response[] = "ACCOUNT_ERR";
                $accountErr = true;
                continue;
            }
            if (Line::isEndOfSession($thisLine) === true) {
                $accountErr = false;
                continue;
            }
            if (Line::isEndOfSession($thisLine) === false && $accountErr === true) {
                continue;
            }
            if (Line::isBalanceAndOverdraft($thisLine)) {
                $currentBalance = Line::getCurrentBalance($thisLine);
                $overdraft = Line::getOverdraft($thisLine);
                continue;
            }
            if (Line::isBalanceEnquiry($thisLine)) {
                $response[] = (integer) $currentBalance;
                continue;
            }
            if (Line::isCashWithdrawal($thisLine)) {
                $errorCheck = Line::withdrawErrorCheck($thisLine, $currentBalance, $overdraft, $atmBalance);
                if ($errorCheck !== "success") {
                    $response[] = $errorCheck;
                    continue;
                }
                $response[] = Line::withdraw($thisLine, $currentBalance);
                $atmBalance = $atmBalance - Line::getWithdrawalAmount($thisLine);
                $currentBalance = $currentBalance - Line::getWithdrawalAmount($thisLine);
            }
        }
        return  [
            "status" => "success",
            "errorMessage" => "",
            "data" => $response
        ];
    }
}
