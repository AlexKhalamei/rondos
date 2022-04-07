<?php


$settings_array = parse_ini_file("../settings.ini");
$json_contact_value = new stdClass();

	
try {
	
	$name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : null;
	$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
	$utm = isset($_POST['utm']) ? filter_var($_POST['utm'], FILTER_SANITIZE_STRING) : null;

	// echo $name;
	// echo $email;
	// echo $utm;

	//die();

	$contact = new stdClass();

	$contact->firstName = $name;

	$contact->channels = array(
			array('type'=>'email', 'value' => $email, 'firstname' => $name )
			);

	$json_contact_value->contact = $contact;
	$json_contact_value->groups = array('Подписчики Rondos');	
	//$json_contact_value->groups = array('99459493');
					
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_contact_value));
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_URL, $settings_array[subscribe_contact_url]);
	curl_setopt($ch,CURLOPT_USERPWD, $settings_array[user_esputnik].':'.$settings_array[password_esputnik]);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	$output = curl_exec($ch);
	//print_r($output);

	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if($httpCode == 200) {
		$status = true;
	} else {
		$status = false;
	}
	curl_close($ch);

    $connection = new PDO("mysql:host=$settings_array[servername];dbname=$settings_array[dbname];charset:utf8",$settings_array[username], $settings_array[password]);


    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->exec('set names utf8;');
	
    $from = null;

    if (isset($_SERVER['HTTP_REFERER'])) {
        $matches = [];

        if (preg_match('/[\\w-]+(?=\\.)/', filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL), $matches)) {
            $from = array_shift($matches);
        }
    }

    $sql = sprintf(
        'insert into applications (name, email, utm) values ("%s", "%s", "%s")',
        $name,
        strtolower($email),
        strtolower($utm)
    );
	
    $result = $connection->exec($sql);

	
	if ($result)
	{
		$data = [
		  'success' => "Message has been send",
		];
	}
	else
	{
		$data = [
		  "error" => "Error send message",
		];
	}
	
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($data);
	
} catch (PDOException $exception) {
    var_dump($exception->getMessage());
}

$connection = null;