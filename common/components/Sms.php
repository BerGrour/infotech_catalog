<?php
namespace common\components;

use yii\base\Component;

class Sms extends Component
{
    public $apikey;

    public function send($smsData)
    {
        $sender = 'INFORM';
        
        $send = array(
            'apikey' => $this->apikey,
            'from' => 'INFORM',
            'send' => $smsData
        );

        $result = file_get_contents('https://smspilot.ru/api2.php', false, stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode( $send ),
            ),
        )));
    }
}