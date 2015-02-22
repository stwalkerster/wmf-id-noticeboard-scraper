<?php

$api = "http://meta.wikimedia.org/w/api.php";
$query = "action=query&titles=Identification+noticeboard&prop=revisions&rvprop=content";

$ch = curl_init($api . "?" . $query . "&format=php");

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "iduser/0.1 (ID Noticeboard scraper; simon@stwalkerster.co.uk)");
$noticeboard = curl_exec($ch);
curl_close($ch);

$noticeboard = unserialize($noticeboard);

$idn_content = $noticeboard['query']['pages'][113261]['revisions'][0]['*'];

$users = array();

$current_index = -1; // make too small so we catch the first element

while($current_index < strlen($idn_content))
{
	//echo $current_index . "\n";
	if(substr($idn_content, ++$current_index, 8) == "{{/user|")
	{
		$name = "";
	
		// find start of diff...
		do {$test = substr($idn_content, ++$current_index, 1);}
		while ($test != "|" && $current_index < strlen($idn_content));
		// find end of diff...
		do {$test = substr($idn_content, ++$current_index, 1);}
		while ($test != "|" && $current_index < strlen($idn_content));
		
		
		do 
		{
			$test = substr($idn_content, ++$current_index, 1); 
			$name .= $test;
		}
		while ($test != "|" && $test != "}");
		
		$name = trim($name, '|} \t');
		
		$users[]=$name;
	}
}

if(isset($_GET['format']))
{
	switch($_GET['format'])
	{
		case "php":
			echo serialize($users);
			break;
		default:
			header('Content-Type: text/plain');
			print_r($users);
			break;
	}
	die;
}
print_r($users);
die;
