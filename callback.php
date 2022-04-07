<?php

require_once '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

// log bot interaction
// the default date format is "Y-m-d\TH:i:sP"
$dateFormat = "d-m-Y, H:i:s";
// the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
$output = "%datetime% > %level_name% > %message% %context% %extra%\n";
// finally, create a formatter
$formatter = new LineFormatter($output, $dateFormat);
$stream = new StreamHandler('../logs/mail.log');
$stream->setFormatter($formatter);

$log = new Logger('mail');
$log->pushHandler($stream);
$webProcessor = new Monolog\Processor\WebProcessor();
$log->pushProcessor($webProcessor);

if (isset($_POST['name'])&&isset($_POST['email'])) {
	$transport = new Swift_SendmailTransport('/usr/sbin/sendmail -t -i -f no-reply@site.com');

	// Create the Mailer using your created Transport
	$mailer = new Swift_Mailer($transport);

	// Create a message
	$message = (new Swift_Message('Заявка с rondos.com.ua'))
	  ->setFrom(['no-reply@site.com' => 'info'])
	  ->setTo(['email@site.com'])
	  ->setBody('<p>Имя: ' . $_POST['name'] . '</p>' . '<p>Телефон: '.$_POST['email'].'</p>', 'text/html')
	  ;
	
	// Send the message
	if ($mailer->send($message))
	//if (true)
	{
		$log->info('success', ['name' => $_POST['name'], 'email' => $_POST['email']]);
		$data = [
		  'success' => "Message has been send",
		];
	}
	else
	{
		$log->info('error', ['name' => $_POST['name'], 'email' => $_POST['email']]);
		$data = [
		  "error" => "Error send message",
		];
	}
	

	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($data);
}