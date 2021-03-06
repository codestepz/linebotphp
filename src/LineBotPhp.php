<?php

namespace Codestep;

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotPhp extends LINEBot {
	
    /* ====================================================================================
     * Variable
     * ==================================================================================== */
	
    private $bot            = null;

    private $httpClient     = null;
    private $endpointBase   = null;
    private $channelSecret  = null;
	
    public $content         = null;
    public $events          = null;
	
    public $isEvents        = false;
    public $isText          = false;
    public $isImage         = false;
    public $isSticker       = false;

    public $displayName     = null;
    public $pictureUrl      = null;
    public $statusMessage   = null;

    public $text            = null;
    public $replyToken      = null;
    public $source          = null;
    public $message         = null;
    public $timestamp       = null;
	
    public $response        = null;
	
    /* ====================================================================================
     * Custom
     * ==================================================================================== */
	
    public function __construct ($channelSecret, $access_token) {

	$this->bot            = new \LINE\LINEBot(new CurlHTTPClient($access_token), [ 'channelSecret' => $channelSecret ]);

        $this->httpClient     = new CurlHTTPClient($access_token);
        $this->channelSecret  = $channelSecret;
        $this->endpointBase   = LINEBot::DEFAULT_ENDPOINT_BASE;
		
        $this->content        = file_get_contents('php://input');
        $events               = json_decode($this->content, true);
		
        if (!empty($events['events'])) {
			
            $this->isEvents = true;
            $this->events   = $events['events'];
			
            foreach ($events['events'] as $event) {
				
		// get profile
                $this->getProfile($event['source']['userId']);

                $this->replyToken = $event['replyToken'];
                $this->source     = (object) $event['source'];
                $this->message    = (object) $event['message'];
                $this->timestamp  = $event['timestamp'];
				
                if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
                    $this->isText = true;
                    $this->text   = $event['message']['text'];
                }
				
                if ($event['type'] == 'message' && $event['message']['type'] == 'image') {
                    $this->isImage = true;
                }
				
                if ($event['type'] == 'message' && $event['message']['type'] == 'sticker') {
                    $this->isSticker = true;
                }
				
            }

        }
	    
	// file_put_contents(__DIR__ . '/logs/log_' . date("Y-m-d-HH-ii-ss") . '.txt', $events);
	
        parent::__construct($this->httpClient, [ 'channelSecret' => $channelSecret ]);
		
    }
	
    public function sendMessageNew ($to = null, $message = null) {
        $messageBuilder = new TextMessageBuilder($message);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
            'to' => $to,
            // 'toChannel' => 'Channel ID,
            'messages'  => $messageBuilder->buildMessage()
        ]);
    }
	
    public function replyMessageNew ($replyToken = null, $message = null) {
        $messageBuilder = new TextMessageBuilder($message);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $messageBuilder->buildMessage(),
        ]);
    }

    public function getProfile ($userId = null) {
	$res = $this->bot->getProfile(!empty($userId) ? $userId : $this->source->userId);
	if ($res->isSucceeded()) {
            $arrData             = $res->getJSONDecodedBody(); // return array
            $this->displayName   = $arrData['displayName'];
            $this->pictureUrl    = str_replace("\\", '', $arrData['pictureUrl']);
            $this->statusMessage = $arrData['statusMessage'];
        }
    }

    public function isSuccess () {
        return !empty($this->response->isSucceeded()) ? true : false;
    }
	
    public function verify ($access_token) {
		
        $ch = curl_init('https://api.line.me/v1/oauth/verify');
		
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $access_token ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
		
    }

}
