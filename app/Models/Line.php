<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    public static function isAccountAndPin($line)
    {
        $lineArray = explode(" ", $line);
        if (count($lineArray) !== 3) {
            return false;
        }

        $accountNumber = $lineArray[0];
        $correctPin = $lineArray[1];
        $enteredPin = $lineArray[2];

        if (strlen($accountNumber) === 8 &&
            strlen($correctPin) === 4 &&
            strlen($enteredPin) === 4) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $line
     * @return bool
     */
    public static function accountAndPinLineValid($line)
    {
        $lineArray = explode(" ", $line);
        $correctPin = $lineArray[1];
        $enteredPin = $lineArray[2];
        if ($correctPin === $enteredPin) {
            return true;
        }
        return false;
    }

    /**
     * @param $line
     * @return bool
     */
    public static function isBalanceAndOverdraft($line)
    {
        $lineArray = explode(" ", $line);
        if (count($lineArray) === 2 && is_numeric($lineArray[0]) && is_numeric($lineArray[1])) {
            return true;
        }
        return false;
    }

    /**
     * @param $line
     * @return mixed
     */
    public static function getCurrentBalance($line)
    {
        $lineArray = explode(" ", $line);
        return $lineArray[0];
    }

    /**
     * @param $line
     * @return mixed
     */
    public static function getOverdraft($line)
    {
        $lineArray = explode(" ", $line);
        return $lineArray[1];
    }

    /**
     * @param $line
     * @return bool
     */
    public static function isBalanceEnquiry($line)
    {
        if ($line === "B") {
            return true;
        }
        return false;
    }

    /**
     * @param $line
     * @return bool
     */
    public static function isCashWithdrawal($line) {
        $lineArray = explode(" ", $line);
        if ($lineArray[0] == "W") {
            return true;
        }
        return false;
    }

    /**
     * @param $line
     * @param $balance
     * @param $overdraft
     * @param $atmAmount
     * @return string
     */
    public static function withdrawErrorCheck($line, $balance, $overdraft, $atmAmount) {
        $lineArray = explode(" ", $line);
        $withdrawalAmount = $lineArray[1];
        $withdrawableAmount = $balance + $overdraft;
        if ($withdrawalAmount > $atmAmount) {
            return "ATM_ERR";
        }
        if ($withdrawalAmount > $withdrawableAmount) {
            return "FUNDS_ERR";
        }
        return "success";
    }

    /**
     * @param $line
     * @param $balance
     * @return mixed
     */
    public static function withdraw($line, $balance) {
        $withdrawalAmount = self::getWithdrawalAmount($line);
        return (integer) $balance - $withdrawalAmount;  //would be a float in the real world
    }

    /**
     * @param $line
     * @return mixed
     */
    public static function getWithdrawalAmount($line) {
        $lineArray = explode(" ", $line);
        return $lineArray[1];
    }

    /**
     * @param $line
     * @return bool
     */
    public static function isEndOfSession($line) {
        if ($line === "") {
            return true;
        }
        return false;
    }
}
