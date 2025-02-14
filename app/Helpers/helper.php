<?php

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Generate uuid
 *
 * @return string
 */
function generate_uuid()
{
    return (string) Str::uuid();
}
/**
 * Get parsed date
 */
function carbon_parse($date)
{
    return Carbon::parse($date);
}

/**
 * Get selected keys of an array.
 *
 * @param  mixed  $default
 */
function array_get(array $arr, string $key, $default = null)
{
    return Arr::get($arr, $key, $default);
}

/**
 * array_except() wrapper
 */
function array_except(array $arr, array $keys)
{
    return Arr::except($arr, $keys);
}

/**
 * array_only() wrapper
 */
function array_only(array $arr, array $keys)
{
    return Arr::only($arr, $keys);
}

/**
 * array_has() wrapper
 *
 * @param  string  $keys
 */
function array_has(array $arr, string $key)
{
    return Arr::has($arr, $key);
}

/**
 * generate cryptographic unique ids.
 *
 * @param int length
 * @return string
 */
function uniqidReal($lenght = 13)
{
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists('random_bytes')) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception('no cryptographically secure random function available');
    }

    return substr(bin2hex($bytes), 0, $lenght);
}

/**
 * Decrypt data from a CryptoJS json encoding string
 *
 * @param  mixed  $passphrase
 * @param  mixed  $encryptedString
 * @return mixed
 */
function cryptoJsAesDecrypt($passphrase, $encryptedString)
{
    $arr = array_pad(
        explode('.', $encryptedString, 3),
        3,
        null
    );

    $jsondata = [
        'ct' => $arr[0],
        'iv' => $arr[1],
        's' => $arr[2],
    ];
    try {
        $salt = hex2bin($jsondata['s']);
        $iv = hex2bin($jsondata['iv']);
    } catch (Exception $e) {
        return null;
    }

    $ct = base64_decode($jsondata['ct']);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = [];
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];

    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }

    $key = substr($result, 0, 32);

    return openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
}

/**
 * Divide number into given parts such that sum of the parts is equal to the number
 *
 * @param  int  $number  - Number to be split up
 * @param  int  $parts  - Number of parts to be splitted up
 */
function splitNumberIntoGivenParts($number, $parts)
{
    $divided = [];
    $partToBeDivided = $parts;

    if ($number % $partToBeDivided == 0) {
        $num = (int) $number / $partToBeDivided;
        for ($i = $partToBeDivided; $i > 0; $i--) {
            array_push($divided, $num);
        }
    }

    if ($number % $partToBeDivided != 0) {
        $num = (int) ($number / $partToBeDivided);
        array_push($divided, ((int) ($number % $partToBeDivided) + $num));
        for ($i = ($partToBeDivided - 1); $i > 0; $i--) {
            array_push($divided, (int) ($number / $partToBeDivided));
        }
    }

    return $divided;
}

/**
 * Get Number of days between two dates
 */
function getNumberOfDaysBetweenDates($from_date, $to_date)
{
    $from_date = Carbon::parse($from_date);
    $to_date = Carbon::parse($to_date);

    return $from_date->diffInDays($to_date);
}


function getTimeInLocalTimeZone($UTC_time)
{
    $UTC_time = new DateTime($UTC_time, new DateTimeZone('UTC'));
    $local_time = $UTC_time->setTimeZone(new DateTimeZone('Asia/Kolkata'));

    return $local_time;
}

function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = [];
    $words = [
        0 => '',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
    ];
    $digits = ['', 'hundred', 'thousand', 'lakh', 'crore'];
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? 'and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else {
            $str[] = null;
        }
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? '.' . ($words[$decimal / 10] . ' ' . $words[$decimal % 10]) . ' Paise' : '';

    return $Rupees . $paise . ' Only';
}

function removeExcelFiles($disk, $ext)
{
    $files = Storage::disk($disk)->allFiles();

    foreach ($files as $file) {
        if (!preg_match("/^.+\.$ext$/", $file)) {
            continue;
        }
        $time = Storage::disk($disk)->lastModified($file);
        $fileModifiedDateTime = Carbon::parse($time);

        if (Carbon::now()->gt($fileModifiedDateTime->addDays(2))) {
            Storage::disk($disk)->delete($file);
        }
    }
}


function getUpcoming10thDate()
{
    $today = Carbon::today();

    // Check if today is the 10th
    if ($today->day === 10) {
        return $today->format('d-m-Y'); // Return today's date in the desired format
    }

    // Get the 10th of this month
    $currentMonth10th = Carbon::create($today->year, $today->month, 10);

    // If today is after the 10th, get the 10th of the next month
    if ($today->gt($currentMonth10th)) {
        $nextMonth10th = $today->addMonth()->startOfMonth()->addDays(9);

        return $nextMonth10th->format('d-m-Y'); // Format the 10th of next month
    }

    // Otherwise, return the 10th of the current month
    return $currentMonth10th->format('d-m-Y'); // Format the 10th of this month
}

function generateReferralCode()
{
    return Str::upper(Str::random(10));
}
