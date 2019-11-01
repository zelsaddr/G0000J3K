<?php

/**
 * @author Copyright 2019 Izzeldin Addarda <zeldin@merahputih.id>
 * @package Auto Create Account Gojek & Redeem Voucher
 * Change the copyright doesn't make you as a Proffesional CODER.
 **/

class Gojek
{
    public $number;
    private $base_uri = "https://api.gojekapi.com";
    private $secret = "83415d06-ec4e-11e6-a41b-6c40088ab51e";
    public function __construct($number)
    {
		$numbers = $number[0].$number[1];
        $numberx = $number[5];
        strlen($number) < 8 ? die("Nomor harus lebih dari 8 karakter.") : "";
		if($numbers == "08") { 
			$this->number = str_replace("08","628", $number);
		}elseif ($numberx == " ") {
			$number = preg_replace("/[^0-9]/", "",$number);
			$this->number = "1".$number;
		}else{
            $this->number = $number;
        }
    }
    public function register()
    {
        $name = $this->get_random_name();
        $headers = $this->headers();
        $data_post1 = '{"name":"' . $name['fullname'] . '","email":"' . $name['mail'] . '","phone":"+' . $this->number . '","signed_up_country":"ID"}';
        $register = json_decode($this->cURL($this->base_uri."/v5/customers", $data_post1, $headers), true);
        if(isset($register['success'])){
            return array(
                "success" => true,
                "otp_token" => $register['data']['otp_token'],
                "message" => $register['data']['message']
            );
        }else{
            return array(
                "success" => false,
                "message" => "Failed to create account. maybe you should change to new number."
            );
        }
    }
    public function send_otp($otp_token, $otp)
    {
        $headers = $this->headers();
        $data_post2 = '{"client_name":"gojek:cons:android","data":{"otp":"'.$otp.'","otp_token":"'.$otp_token.'"},"client_secret":"'.$this->secret .'"}';
        $otp_post = json_decode($this->cURL($this->base_uri."/v5/customers/phone/verify", $data_post2, $headers), true);
        if(isset($otp_post['success'])){
            return array(
                "success" => true,
                "access_token" => $otp_post['data']['access_token']
            );
        }else{
            return array(
                "success" => false,
                "message" => "invalid otp."
            );
        }
    }
    public function redeem($voucher = 0, $voucher_batch = 0, $access_token) // use voucher code for usually redeem, use voucher batch if you know.
    {
        $headers = $this->headers();
        $headers[] = 'Authorization: Bearer '.$access_token.'';
        if($voucher){
            $code = '{"promo_code":"'.$voucher.'"}';
            $curl = json_decode($this->cURL($this->base_uri."/go-promotions/v1/promotions/enrollments", $code, $headers), true);
            return $curl;
        }elseif($voucher_batch){
            $batch = "{\"gopay_pin\":\"\",\"payment_type\":\"points\",\"voucher_batch_id\":\"".$voucher_batch."\",\"voucher_count\":0}";
            $batch_post = json_decode($this->cURL($this->base_uri."/gopoints/v1/orders", $batch, $headers), true);
            return $batch_post;
        }else{
            return "Select method.";
        }
    }
    private function get_random_name(){
        $name = $this->cURL("https://fakenametool.net/generator/random/id_ID/indonesia");
        preg_match('#<h3><b>(.*?)</b></h3>#si', $name, $curl);
        while(strlen($curl[1]) < 1){
            $name = $this->cURL("https://fakenametool.net/generator/random/id_ID/indonesia");
            preg_match('#<h3><b>(.*?)</b></h3>#si', $name, $curl);
        }
        return array(
            "fullname" => $curl[1],
            "password" => $curl[1].$this->generateRandomString(3),
            "mail" => str_replace(" ", ".", strtolower($curl[1])).$this->generateRandomString(3)."@gmail.com"
            );
    }
    private function gen_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));
    }
    private function generateRandomString($length = 10) 
    { // length
        $characters = '0123456789ABCDEFGHIJKLMNabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function headers()
    {
        $headers[] = 'X-Appversion: 3.39.2';
        $headers[] = 'X-Appid: com.gojek.app';
        $headers[] = 'X-Platform: Android';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept-Language: id-ID';
        $headers[] = 'X-User-Locale: id_ID';
        $headers[] = 'X-Location: -6.'.rand(111,222).'9016,106.'.rand(111,444).'7473';
        $headers[] = 'Host: api.gojekapi.com';
        return $headers;
    }
    private function cURL($url, $post = 0, $headers = 0)
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_TIMEOUT         => 5,
            CURLOPT_CONNECTTIMEOUT  => 5,
        ];
        if($headers){
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        if($post){
            $options[CURLOPT_POST]  = true;
            $options[CURLOPT_POSTFIELDS] = $post;
        }
        curl_setopt_array($ch, $options);
        $exec = curl_exec($ch);
        return $exec;
    }
}
?>
