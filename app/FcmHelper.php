<?php 

namespace App;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;


use App\Models\FcmToken;

class FcmHelper {
	public static function sendDownstreamMessageToDevice($token, $title, $message) {
		
		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder($title);
		$notificationBuilder->setBody($body)
						    ->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

		return $downstreamResponse;
	}

	public static function sendDownstreamMessageToDevices($tokens, $title, $message) {
		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder($title);
		$notificationBuilder->setBody($messages)
						    ->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();


		$downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

		return $downstreamResponse;
	}

	public static function getFCMTokens() {
		$fcmTokens = [];
		$tokens = FcmToken::all();

		foreach($tokens as $token) {
			$fcmTokens[] = $token->fcm_token;
		}

		return $fcmTokens;
	}

	public static function sendPushNotificationToApp() {
		$tokens = $this->getFCMTokens();
		if (count($tokens) == 0) {
			throw new Exception("No FCM Tokens found!");
		}

		try {
			return $this->sendDownstreamMessageToDevices($tokens, '[APP]', 'Hello, App notification recieved!');
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}




}