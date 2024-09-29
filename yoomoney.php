<?php
class YooMoneyPayments{
    protected string $wallet;
    protected string $secret;
    public function __construct(Array $param){
        $this->wallet = $param["wallet"];
        $this->secret = $param["webhook"];
    }
    private function answer(Int $status, String $text){
        return [
            "code" => $status,
            "data" => $text
        ];
    }
    public function createLink(Array $param){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://yoomoney.ru/quickpay/confirm");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        $param["receiver"] = $this->wallet;
        $param["quickpay-form"] = "button";
        $param["paymentType"] = "PC";
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $answer = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpcode !== 200){
            return $this->answer(400, "An error occurred while generating the payment link: ".$answer);
        }
        return $this->answer(200, str_replace("Found. Redirecting to ", "", $answer));
    }
    public function webhookCheck($request){
        $methods = ["POST"];
        if(in_array($_SERVER["REQUEST_METHOD"], $methods)){
            return $this->answer(400, "A ".$_SERVER["REQUEST_METHOD"]." request type was sent when the server was expecting ".json_encode($methods).".");
        }
        $hash = sha1($request["notification_type"]."&".$request["operation_id"]."&".$request["amount"]."&".$request["currency"]."&".$request["datetime"]."&".$request["sender"]."&".$request["codepro"]."&".$this->secret."&".$request["label"]);
        if($request["sha1_hash"] != $hash){
            return $this->answer(400, "The generated hash does not match the sent hash. Generated: ".$hash.". Sent: ".$request["sha1_hash"].".");
        }
        return $this->answer(200, "This is a request from YooMoney.");
    }
}
?>