<?php

namespace phuminstudio;

use phpseclib\Crypt\AES;

class Payment
{

    private $private_key;
    private $public_key;

    private $apiURL = "http://localhost:8000/";

    // Kbank setting
    private $kbank_username;
    private $kbank_password;
    private $kbank_account;

    // SCB setting
    private $scb_username;
    private $scb_password;
    private $scb_account;

    // Wallet setting
    private $wallet_username;
    private $wallet_password;
    private $wallet_reference;

    public function __construct($private_key, $public_key)
    {
        $this->private_key = $private_key;
        $this->public_key = $public_key;
    }

    private function connect($url, $data = array())
    {
        $data['public_key'] = $this->public_key;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);

        return $this->returnResponse($output);
    }

    private function encryptData(Array $data = array()) {
        $ciper = new AES();
        $ciper->setPassword($this->private_key);
        $encrypt = $ciper->encrypt(json_encode($data));

        return $encrypt;
    }

    private function returnResponse($response) {
        return json_decode($response);
    }

    public function kbankSetting($username, $password, $account) {
        $this->kbank_username = $username;
        $this->kbank_password = $password;
        $this->kbank_account = str_replace("-", "", $account);
    }

    public function kbankCheck($date, $month, $year, $hour, $minute, $amount, $callback = false) {
        $data = $this->encryptData(array(
            "username" => $this->kbank_username,
            "password" => $this->kbank_password,
            "account" => $this->kbank_account,
        ));

        return $this->connect("api/statement/check/kbank", array(
            "gateway" => "kbank",
            "date" => $date,
            "month" => $month,
            "year" => $year,
            "hour" => $hour,
            "minute" => $minute,
            "amount" => $amount,
            "data" => $data,
            "callback" => $callback,
        ));
    }

    public function scbSetting($username, $password, $account) {
        $this->scb_username = $username;
        $this->scb_password = $password;
        $this->scb_account = str_replace("-", "", $account);
    }

    public function scbCheck($date, $month, $year, $hour, $minute, $amount, $callback = false) {
        $data = $this->encryptData(array(
            "username" => $this->scb_username,
            "password" => $this->scb_password,
            "account" => $this->scb_account,
        ));

        return $this->connect("api/statement/check/scb", array(
            "gateway" => "scb",
            "date" => $date,
            "month" => $month,
            "year" => $year,
            "hour" => $hour,
            "minute" => $minute,
            "amount" => $amount,
            "data" => $data,
            "callback" => $callback,
        ));
    }

    public function walletSetting($username, $password, $reference_token) {
        $this->wallet_username = $username;
        $this->wallet_password = $password;
        $this->wallet_reference = $reference_token;
    }

    public function walletCheck($txid, $callback = false) {
        $data = $this->encryptData(array(
            "username" => $this->wallet_username,
            "password" => $this->wallet_password,
            "reference_token" => $this->wallet_reference,
        ));

        return $this->connect("api/statement/check/wallet", array(
            "gateway" => "wallet",
            "txid" => $txid,
            "data" => $data,
            "callback" => $callback,
        ));
    }
}
