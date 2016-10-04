<?php


$channelObjs = array(	
						"SMS_NEXMO" => new NexmoSMSChannel("594b5de6", "bd95b0d2"), 
						"SMS_CLICKATELL" => new ClickatellSMSChannel("Iumh1p1jI7LRMPaGQRPQnbXXbROp.n.59qAR3OD9rW7XmF.WHEOrCjMAj4WKZe4Dyt3Vf9z"), 
						"EMAIL_TEXT" => new SampleEmailChannel(false), 
						"EMAIL_HTML" => new SampleEmailChannel(true),
						"TWITTER_KHALED" => new TwitterChannel(array('oauth_access_token' => "285353386-yFQgrhQPSUHtQMGlMiwI0KxnxpGgjNtWlerlqDVK", 'oauth_access_token_secret' => "QbHabXyy0RO4AKQ3saZtQ5nUkbpjNnLPNh5OHZpSPJDlM", 'consumer_key' => "dEBS9iN83ZbL6DMjj7IGiPKmY", 'consumer_secret' => "PhWJj2C0WrGxyXJ8GJMAHm4q6ftJYxG0Bk0hq4RPSfs5ywu6DF")),
						"TWITTER_PUBLIC" => new TwitterChannel(array('oauth_access_token' => "1527545066-7jbGtrSzl3Imj2OgJ1OOoB2Jmv5OFKrFg6RrMbm", 'oauth_access_token_secret' => "8aRhxEP1wgJ5epWVLIpD53LpdcpEDm5MfhNSMVQLQtslv", 'consumer_key' => "dYhZSD1ux5drCjwOFzRtxCBJO", 'consumer_secret' => "RxMKFRXrC4fnlGz3sjRKxALjxZKJ3iiw75CdXffyHJtT4nENdF")),
						"TELEGRAM_MESSAGE" => new TelegramChannel("243675596:AAFCxV_kqq6tpLCOjCfzifFij6ML81ZbmsQ"),
					);


$dbLogger = new DatabaseLogger("localhost:3306", "message_svc", "DXmbfucUSVcUCv8X");
$dbLogger->connect();



?>