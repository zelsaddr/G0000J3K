<?php

/**
 * @author Copyright 2019 Izzeldin Addarda <zeldin@merahputih.id>
 * @package Auto Create Account Gojek & Redeem Voucher
 * Change the copyright doesn't make you as a Proffesional CODER.
 **/

require "./gojek.class.php";
echo "[#] Nomor : "; $nomor = trim(fgets(STDIN));
$gojek = new Gojek($nomor);
$register = $gojek->register();
if(isset($register['success'])){
    if($register['message'] == null){
        die("\tUser already registered.");
    }
    echo "[#] OTP : "; $otp = trim(fgets(STDIN));
    $send = $gojek->send_otp($register['otp_token'], $otp);
    do{
        echo "\t[!] Invalid OTP.".PHP_EOL;
        echo "[#] OTP : "; $otp = trim(fgets(STDIN));
        $send = $gojek->send_otp($register['otp_token'], $otp);
    }while($send['success'] == false);
    echo "\t[+] Access token for number ".$nomor." is ".$send['access_token'].PHP_EOL;
    echo "\t[$] Try to redeem GOFOODBOBA07".PHP_EOL;
    echo $gojek->redeem("GOFOODBOBA07", 0, $send['access_token'])['success'] == true ? "\t\t[$] Success redeem GOFOODBOBA07..".PHP_EOL : "\t\t[!] Failed redeem GOFOODBOBA07..".PHP_EOL;
    sleep(15);
    echo "\t[$] Try to redeem COBAINGOJEK".PHP_EOL;
    echo $gojek->redeem("COBAINGOJEK", 0, $send['access_token'])['success'] == true ? "\t\t[$] Success redeem COBAINGOJEK..".PHP_EOL : "\t\t[!] Failed redeem COBAINGOJEK..".PHP_EOL;
    sleep(15);
    echo "\t[$] Try to redeem AYOCOBAGOJEK".PHP_EOL;
    echo $gojek->redeem("AYOCOBAGOJEK", 0, $send['access_token'])['success'] == true ? "\t\t[$] Success redeem AYOCOBAGOJEK..".PHP_EOL : "\t\t[!] Failed redeem AYOCOBAGOJEK..".PHP_EOL;
    echo "\t[$] Try to redeem Batch Mc Donald's Cashback 20k".PHP_EOL;
    sleep(15);
    echo $gojek->redeem(0, "44c78b9b-69ec-4fe0-9c63-feb4e6075aed", $send['access_token'])['success'] == true ? "\t\t[$] Success redeem Mc Donald's..".PHP_EOL : "\t\t[!] Failed redeem Mc Donald's..".PHP_EOL;
    echo "\tDone Redeem..".PHP_EOL;
}else{
    print("\t".$register['message'].PHP_EOL);
}
?>
