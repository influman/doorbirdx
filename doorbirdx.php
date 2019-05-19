<?php
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";      
   //*********************************************************************
   // V1.2 : DoorbirdX - Influman 2018 - 2019
   // ********************************************************************	
	// recuperation des infos depuis la requete
    $action = getArg("action", true, 'status');
	$ip = getArg("ip", true);
	$login = getArg("login", true);
	$pwd = getArg("pwd", true);
	$value = getArg("value", false);
	$event = getArg("event", false);
	$debug = getArg("debug", false);
	// API eedomus
	$api_user = getArg("apiu", false, '');
	$api_secret = getArg("apis", false, '');
	// FTP
	$ftp_server = getArg('ftpserv', false, '');
	$ftp_user = getArg('ftpusr', false, '');
	$ftp_pass = getArg('ftppwd', false, '');
	// API DU PERIPHERIQUE APPELANT LE SCRIPT
    $periph_id = getArg('eedomus_controller_module_id'); 
	
	$headers = array('Authorization: Basic '.base64_encode("$login:$pwd"));

// ********************************************************************	
// STATUS : Info du firmware
// ********************************************************************	
if ($action == "status") {
	// Appel info device Doorbird, vérification de la connexion, enregistrement du nom de l'extension relais le cas échéant
	$url = 'http://'.$ip.'/bha-api/info.cgi';
    $return = sdk_json_decode(utf8_encode(httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers)));
	$xml .="<DOORBIRDX><STATUS>";
	$returncode = $return["BHA"]["RETURNCODE"];
	if ($returncode == "1") {
		$firmware = $return["BHA"]["VERSION"][0]["FIRMWARE"];
		$ext = "";
		$device = "unknown";
		if (array_key_exists("RELAYS", $return["BHA"]["VERSION"][0])) {
			$device = $return["BHA"]["VERSION"][0]["DEVICE-TYPE"];
			$relays = $return["BHA"]["VERSION"][0]["RELAYS"];
			$nbrelays = count($relays);
			saveVariable($login."_NBREL", $nbrelays);
			if ($nbrelays > 2) {
				$tabext = explode("@", $relays[2]);
				$ext = $tabext[0]."@";
				saveVariable($login."_EXT", $ext);
			}
		}
		$xml .= $device." (fw.".$firmware.") ".$nbrelays." relays";
	} else {
		if (loadVariable($login."_NBREL") != '') {
			$xml .= "Doorbird Connection was lost : ".$returncode;
		} else {
			$xml .= "Doorbird Connection Error : ".$returncode;
		}
	}
	$xml .="</STATUS></DOORBIRDX>";
	sdk_header('text/xml');
 	echo $xml;
	die();
// ********************************************************************	
// SHOWSNAP : Snapshot image/jpg
// ********************************************************************
} else if ($action == "showsnap") {
	// Obtention d'un snap
	$url = 'http://'.$ip.'/bha-api/image.cgi';
    $img = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
    sdk_header('image/jpg');
    echo $img;
    die();
// ************************************************************************
// SHOWHISTORY : Historique des snapshot (de 1 à 50, DOORBELL|MOTIONSENSOR)
// ************************************************************************
} else if ($action == "showhistory") {
	if ($value == '' || $value == 0 || $value > 50) {
		$value = 1;
	}
	if ($event != 'doorbell' && $event != 'motionsensor') {
		$event = 'doorbell';
	}
	// appel à l'historique des snap internes du Doorbird (1 à 50)
	$url = 'http://'.$ip.'/bha-api/history.cgi?index='.$value.'&event='.$event;
    $img = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
    sdk_header('image/jpg');
    echo $img;
    die();
// ********************************************************************************
// OPENDOOR : Déclenchement d'un relais (1|2 du doorbird, ggg@1|2|3 de l'extension)
// ********************************************************************************
} else if ($action == "opendoor") {
	$nbeerelay1 = loadVariable($login."_ANAEEREL1_USE");
	if ($nbeerelay1 == '') {
		$nbeerelay1 = 0;
	}
	$nbeerelay2 = loadVariable($login."_ANAEEREL2_USE");
	if ($nbeerelay2 == '') {
		$nbeerelay2 = 0;
	}
	$nbeerelay3 = loadVariable($login."_ANAEEREL3_USE");
	if ($nbeerelay3 == '') {
		$nbeerelay3 = 0;
	}
	$nbeerelay4 = loadVariable($login."_ANAEEREL4_USE");
	if ($nbeerelay4 == '') {
		$nbeerelay4 = 0;
	}
	$nbeerelay5 = loadVariable($login."_ANAEEREL5_USE");
	if ($nbeerelay5 == '') {
		$nbeerelay5 = 0;
	}
	
	$ext = "";
	if ($value == '' || $value == 0 || $value > 5) {
		$value = 1;
	}
	switch($value) {
		case "1": $nbeerelay1++;
		break;
		
		case "2": $nbeerelay2++;
		break;
		
		case "3": $nbeerelay3++;
		break;
		
		case "4": $nbeerelay4++;
		break;
		
		case "5": $nbeerelay5++;
		break;
	}
	if ($value > 2) {
		$value = $value - 2;
		$ext = loadVariable($login."_EXT");
	}
	
	// déclenchement d'un relais
	$url = 'http://'.$ip.'/bha-api/open-door.cgi?r='.$ext.$value;
    $return = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
	saveVariable($login."_ANAEEREL1_USE", $nbeerelay1);
	saveVariable($login."_ANAEEREL2_USE", $nbeerelay2);
	saveVariable($login."_ANAEEREL3_USE", $nbeerelay3);
	saveVariable($login."_ANAEEREL4_USE", $nbeerelay4);
	saveVariable($login."_ANAEEREL5_USE", $nbeerelay5);
	die();
// ********************************************************************
// LIGHTON : Allumage 3mn de la vision de nuit
// ********************************************************************
} else if ($action == "lighton") {
	$nbeelight = loadVariable($login."_ANAEELIGHT_USE");
	if ($nbeelight == '') {
		$nbeelight = 0;
	}
	$url = 'http://'.$ip.'/bha-api/light-on.cgi';
    $return = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
	$nbeelight++;
	saveVariable($login."_ANAEELIGHT_USE", $nbeelight);
	die();
// ********************************************************************
// RESTART : Reboot
// ********************************************************************
} else if ($action == "restart") {
	$url = 'http://'.$ip.'/bha-api/restart.cgi';
    $return = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
	die();
// ********************************************************************
// SNAPFTP : Enregistrement d'une image sur le ftp
// ********************************************************************
} else if ($action == "snapftp") {
	$nbeeftp = loadVariable($login."_ANAEEFTP_USE");
	if ($nbeeftp == '') {
		$nbeeftp = 0;
	}
	// Obtention d'un snapshot
	$url = 'http://'.$ip.'/bha-api/image.cgi';
	$img = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
	// enregistrement FTP
	$return = ftpUpload($ftp_server, $ftp_user, $ftp_pass, $img, 'doorbird_img.jpg');
	$nbeeftp++;
	saveVariable($login."_ANAEEFTP_USE", $nbeeftp);
	die();
// ********************************************************************
// INITIALIZE : Enregistrement de favorites/schedules
// ********************************************************************
} else if ($action == "initialize") {
	// listage des favorites/schedules
	$url = 'http://'.$ip.'/bha-api/favorites.cgi';
    $return = sdk_json_decode(httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers));
	
	// Mise à zéro des Favorites/Schedules
	foreach($return['http'] as $id => $val) {
		$url = 'http://'.$ip.'/bha-api/favorites.cgi?action=remove&type=http&id='.$id;
		$response = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
	}
	
	// création des Favorites eedomus (voir eedomus_plugin_json pour liste évènements)
	// recherche des valeurs du périphériques
	$tab_events = getPeriphValueList($periph_id);
	$favid = 0;
	//$events = array();
	foreach($tab_events As $event_value) {
		
		$event = $event_value["value"];
		$title = $event_value["state"]; 
		if ($event != 0 && $event != "0") {
			$favid++;
		
			// enregistrement du favorites (Visite HTTP)
			$eedomus_url = urlencode('https://api.eedomus.com/set?action=periph.value&periph_id='.$periph_id.'&value='.$event."&api_user=".$api_user."&api_secret=".$api_secret);
			$url = 'http://'.$ip.'/bha-api/favorites.cgi?action=save&type=http&title='.$title.'&value='.$eedomus_url;
			$response = httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers);
		}
	}
	
	// Enregistrement des Schedules associés
	$url = 'http://'.$ip.'/bha-api/favorites.cgi';
    $return = sdk_json_decode(httpQuery($url, 'GET', /*$post*/ NULL, /*$oauth_token*/ NULL, $headers));
	foreach($return['http'] as $id => $val) {
		$title = $val['title'];
		//input : doorbell|motion|rfid|input
		//input-param : <doorbellnumber>|<>|<transponderid>|<input-number>
		//output-param : <>|<sip-favorite-id>|<relaynumber>|<http-favorite-id>
		$output_param = $id; // id du favorites
		if (strtolower($title) == "doorbell") {
			$input = "doorbell";
			$input_param = 1; // doorbell n°1
			$schedule = '{"input":"doorbell","param":"1","output":[{"event":"notify","param":"","schedule":{"weekdays":[{"to":"82799","from":"82800"}]}},{"event":"http","param":"'.$output_param.'","schedule":{"weekdays":[{"to":"82799","from":"82800"}]}}]}';
			$url = 'http://'.$ip.'/bha-api/schedule.cgi';
			$response = httpQuery($url, 'POST', $schedule, /*$oauth_token*/ NULL, $headers);
			if (is_numeric($debug) && $debug > 0) {
				setValue($debug, "doorbell: ".$response);
			}
		} else if (strtolower($title) == "motion") {
			$input = "motion";
			$input_param = ""; 
			$schedule = '{"input":"motion","param":"","output":[{"event":"http","param":"'.$output_param.'","schedule":{"weekdays":[{"to":"82799","from":"82800"}]}}]}';
			$url = 'http://'.$ip.'/bha-api/schedule.cgi';
			$response = httpQuery($url, 'POST', $schedule, /*$oauth_token*/ NULL, $headers);
			if (is_numeric($debug) && $debug > 0) {
				setValue($debug, "motion: ".$response);
			}
		} else if (strpos(strtolower($title), "relay") !== false) {
			$input = "input";
			$input_param = 1;
			$pattern = '/.+([1-5]+) */'; 
			if (preg_match($pattern, $title, $matches) == 1) { 
				$input_param = $matches[1]; 
				$schedule = '{"input":"input","param":"'.$input_param.'","output":[{"event":"http","param":"'.$output_param.'","schedule":{"weekdays":[{"to":"82799","from":"82800"}]}}]}';
				$url = 'http://'.$ip.'/bha-api/schedule.cgi';
				$response = httpQuery($url, 'POST', $schedule, /*$oauth_token*/ NULL, $headers);
				if (is_numeric($debug) && $debug > 0) {
					setValue($debug, "relay: ".$response);
				}
			}
		} else if (strpos(strtolower($title), "rfid") !== false) {
			$input = "rfid";
			$input_param = "";
			$pattern = '/.+([0-9]+).*\((.+)\) */'; 
			if (preg_match($pattern, $title, $matches) == 1) { 
				$input_param = $matches[2]; 
				if ($input_param != "transponder_id") {
					$schedule = '{"input":"rfid","param":"'.$input_param.'","output":[{"param":"1","enabled":"1","schedule":{"weekdays":[{"to":"82799","from":"82800"}]},"event":"relay"},{"event":"http","param":"'.$output_param.'","schedule":{"weekdays":[{"to":"82799","from":"82800"}]}}]}';
					$url = 'http://'.$ip.'/bha-api/schedule.cgi';
					$response = httpQuery($url, 'POST', $schedule, /*$oauth_token*/ NULL, $headers);
					if (is_numeric($debug) && $debug > 0) {
						setValue($debug, "rfid: ".$response);
					}
				}
			}
		}
	}
// ********************************************************************
// ENREGISTREMENT DES STATISTIQUES DES EVENEMENTS DOORBIRD
// ********************************************************************
} else if ($action == "eventdoorbell") {
	$nbeventdoorbell = loadVariable($login."_ANADOORBELL_USE");
	if ($nbeventdoorbell == '') {
		$nbeventdoorbell = 0;
	}
	$nbeventdoorbell++;
	saveVariable($login."_ANADOORBELL_USE", $nbeventdoorbell);
	die();
} else if ($action == "eventmotion") {
	$nbeventmotion = loadVariable($login."_ANAMOTION_USE");
	if ($nbeventmotion == '') {
		$nbeventmotion = 0;
	}
	$nbeventmotion++;
	saveVariable($login."_ANAMOTION_USE", $nbeventmotion);
	die();
} else if ($action == "eventrelay1") {
	$nbeventrelay1 = loadVariable($login."_ANARELAY1_USE");
	if ($nbeventrelay1 == '') {
		$nbeventrelay1 = 0;
	}
	$nbeventrelay1++;
	saveVariable($login."_ANARELAY1_USE", $nbeventrelay1);
	die();
} else if ($action == "eventrelay2") {
	$nbeventrelay2 = loadVariable($login."_ANARELAY2_USE");
	if ($nbeventrelay2 == '') {
		$nbeventrelay2 = 0;
	}
	$nbeventrelay2++;
	saveVariable($login."_ANARELAY2_USE", $nbeventrelay2);
	die();
} else if ($action == "eventrelay3") {
	$nbeventrelay3 = loadVariable($login."_ANARELAY3_USE");
	if ($nbeventrelay3 == '') {
		$nbeventrelay3 = 0;
	}
	$nbeventrelay3++;
	saveVariable($login."_ANARELAY3_USE", $nbeventrelay3);
	die();
} else if ($action == "eventrelay4") {
	$nbeventrelay4 = loadVariable($login."_ANARELAY4_USE");
	if ($nbeventrelay4 == '') {
		$nbeventrelay4 = 0;
	}
	$nbeventrelay4++;
	saveVariable($login."_ANARELAY4_USE", $nbeventrelay4);
	die();
} else if ($action == "eventrelay5") {
	$nbeventrelay5 = loadVariable($login."_ANARELAY5_USE");
	if ($nbeventrelay5 == '') {
		$nbeventrelay5 = 0;
	}
	$nbeventrelay5++;
	saveVariable($login."_ANARELAY5_USE", $nbeventrelay5);
	die();
} else if ($action == "eventrfid1") {
	$nbeventrfid1 = loadVariable($login."_ANARFID1_USE");
	if ($nbeventrfid1 == '') {
		$nbeventrfid1 = 0;
	}
	$nbeventrfid1++;
	saveVariable($login."_ANARFID1_USE", $nbeventrfid1);
	die();
} else if ($action == "eventrfid2") {
	$nbeventrfid2 = loadVariable($login."_ANARFID2_USE");
	if ($nbeventrfid2 == '') {
		$nbeventrfid2 = 0;
	}
	$nbeventrfid2++;
	saveVariable($login."_ANARFID2_USE", $nbeventrfid2);
	die();
} else if ($action == "eventrfid3") {
	$nbeventrfid3 = loadVariable($login."_ANARFID3_USE");
	if ($nbeventrfid3 == '') {
		$nbeventrfid3 = 0;
	}
	$nbeventrfid3++;
	saveVariable($login."_ANARFID3_USE", $nbeventrfid3);
	die();
} else if ($action == "eventrfid4") {
	$nbeventrfid4 = loadVariable($login."_ANARFID4_USE");
	if ($nbeventrfid4 == '') {
		$nbeventrfid4 = 0;
	}
	$nbeventrfid4++;
	saveVariable($login."_ANARFID4_USE", $nbeventrfid4);
	die();
} else if ($action == "eventrfid5") {
	$nbeventrfid5 = loadVariable($login."_ANARFID5_USE");
	if ($nbeventrfid5 == '') {
		$nbeventrfid5 = 0;
	}
	$nbeventrfid5++;
	saveVariable($login."_ANARFID5_USE", $nbeventrfid5);
	die();
} else if ($action == "eventrfid6") {
	$nbeventrfid6 = loadVariable($login."_ANARFID6_USE");
	if ($nbeventrfid6 == '') {
		$nbeventrfid6 = 0;
	}
	$nbeventrfid6++;
	saveVariable($login."_ANARFID6_USE", $nbeventrfid6);
	die();
} else if ($action == "eventrfid7") {
	$nbeventrfid7 = loadVariable($login."_ANARFID7_USE");
	if ($nbeventrfid7 == '') {
		$nbeventrfid7 = 0;
	}
	$nbeventrfid7++;
	saveVariable($login."_ANARFID7_USE", $nbeventrfid7);
	die();
} else if ($action == "eventrfid8") {
	$nbeventrfid8 = loadVariable($login."_ANARFID8_USE");
	if ($nbeventrfid8 == '') {
		$nbeventrfid8 = 0;
	}
	$nbeventrfid8++;
	saveVariable($login."_ANARFID8_USE", $nbeventrfid8);
	die();
} else if ($action == "eventrfid9") {
	$nbeventrfid9 = loadVariable($login."_ANARFID9_USE");
	if ($nbeventrfid9 == '') {
		$nbeventrfid9 = 0;
	}
	$nbeventrfid9++;
	saveVariable($login."_ANARFID9_USE", $nbeventrfid9);
	die();
// ********************************************************************
// LECTURE DES STATISTIQUES
// ********************************************************************
} else if ($action == "analytics") {
	$nbeerelay1 = loadVariable($login."_ANAEEREL1_USE");
	if ($nbeerelay1 == '') {
		$nbeerelay1 = 0;
	}
	$nbeerelay2 = loadVariable($login."_ANAEEREL2_USE");
	if ($nbeerelay2 == '') {
		$nbeerelay2 = 0;
	}
	$nbeerelay3 = loadVariable($login."_ANAEEREL3_USE");
	if ($nbeerelay3 == '') {
		$nbeerelay3 = 0;
	}
	$nbeerelay4 = loadVariable($login."_ANAEEREL4_USE");
	if ($nbeerelay4 == '') {
		$nbeerelay4 = 0;
	}
	$nbeerelay5 = loadVariable($login."_ANAEEREL5_USE");
	if ($nbeerelay5 == '') {
		$nbeerelay5 = 0;
	}
	$nbeelight = loadVariable($login."_ANAEELIGHT_USE");
	if ($nbeelight == '') {
		$nbeelight = 0;
	}
	$nbeeftp = loadVariable($login."_ANAEEFTP_USE");
	if ($nbeeftp == '') {
		$nbeeftp = 0;
	}
	$nbeventdoorbell = loadVariable($login."_ANADOORBELL_USE");
	if ($nbeventdoorbell == '') {
		$nbeventdoorbell = 0;
	}
	$nbeventmotion = loadVariable($login."_ANAMOTION_USE");
	if ($nbeventmotion == '') {
		$nbeventmotion = 0;
	}
	$nbeventrelay1 = loadVariable($login."_ANARELAY1_USE");
	if ($nbeventrelay1 == '') {
		$nbeventrelay1 = 0;
	}
	$nbeventrelay2 = loadVariable($login."_ANARELAY2_USE");
	if ($nbeventrelay2 == '') {
		$nbeventrelay2 = 0;
	}
	$nbeventrelay3 = loadVariable($login."_ANARELAY3_USE");
	if ($nbeventrelay3 == '') {
		$nbeventrelay3 = 0;
	}
	$nbeventrelay4 = loadVariable($login."_ANARELAY4_USE");
	if ($nbeventrelay4 == '') {
		$nbeventrelay4 = 0;
	}
	$nbeventrelay5 = loadVariable($login."_ANARELAY5_USE");
	if ($nbeventrelay5 == '') {
		$nbeventrelay5 = 0;
	}
	$nbeventrfid1 = loadVariable($login."_ANARFID1_USE");
	if ($nbeventrfid1 == '') {
		$nbeventrfid1 = 0;
	}
	$nbeventrfid2 = loadVariable($login."_ANARFID2_USE");
	if ($nbeventrfid2 == '') {
		$nbeventrfid2 = 0;
	}
	$nbeventrfid3 = loadVariable($login."_ANARFID3_USE");
	if ($nbeventrfid3 == '') {
		$nbeventrfid3 = 0;
	}
	$nbeventrfid4 = loadVariable($login."_ANARFID4_USE");
	if ($nbeventrfid4 == '') {
		$nbeventrfid4 = 0;
	}
	$nbeventrfid5 = loadVariable($login."_ANARFID5_USE");
	if ($nbeventrfid5 == '') {
		$nbeventrfid5 = 0;
	}
	$nbeventrfid6 = loadVariable($login."_ANARFID6_USE");
	if ($nbeventrfid6 == '') {
		$nbeventrfid6 = 0;
	}
	$nbeventrfid7 = loadVariable($login."_ANARFID7_USE");
	if ($nbeventrfid7 == '') {
		$nbeventrfid7 = 0;
	}
	$nbeventrfid8 = loadVariable($login."_ANARFID8_USE");
	if ($nbeventrfid8 == '') {
		$nbeventrfid8 = 0;
	}
	$nbeventrfid9 = loadVariable($login."_ANARFID9_USE");
	if ($nbeventrfid9 == '') {
		$nbeventrfid9 = 0;
	}
	$nbeventrelay = $nbeventrelay1 + $nbeventrelay2 + $nbeventrelay3 + $nbeventrelay4 + $nbeventrelay5;
	$nbeventrfid = $nbeventrfid1 + $nbeventrfid2 + $nbeventrfid3 + $nbeventrfid4 + $nbeventrfid5 + $nbeventrfid6 + $nbeventrfid7 + $nbeventrfid8 + $nbeventrfid9;
	$summary = $nbeventdoorbell." DB | ".$nbeventmotion." MT | ".$nbeventrelay." RL | ".$nbeventrfid." RF | ".$nbeeftp." FTP";
	$xml .="<DOORBIRDX><ANALYTICS>";
	$xml .="<SUMMARY>".$summary."</SUMMARY>";
	$xml .="<NBRELAY>".$nbeventrelay."</NBRELAY><NBRELAY1>".$nbeventrelay1."</NBRELAY1><NBRELAY2>".$nbeventrelay2."</NBRELAY2><NBRELAY3>".$nbeventrelay3."</NBRELAY3><NBRELAY4>".$nbeventrelay4."</NBRELAY4><NBRELAY5>".$nbeventrelay5."</NBRELAY5>";
	$xml .="<NBRFID>".$nbeventrfid."</NBRFID><NBRFID1>".$nbeventrfid1."</NBRFID1><NBRFID2>".$nbeventrfid2."</NBRFID2><NBRFID3>".$nbeventrfid3."</NBRFID3><NBRFID4>".$nbeventrfid4."</NBRFID4><NBRFID5>".$nbeventrfid5."</NBRFID5><NBRFID6>".$nbeventrfid6."</NBRFID6><NBRFID7>".$nbeventrfid7."</NBRFID7><NBRFID8>".$nbeventrfid8."</NBRFID8><NBRFID9>".$nbeventrfid9."</NBRFID9>";
	$xml .="<NBEELIGHT>".$nbeelight."</NBEELIGHT>";
	$xml .="<NBEEFTP>".$nbeeftp."</NBEEFTP>";
	$xml .="<NBDOORBELL>".$nbeventdoorbell."</NBDOORBELL>";
	$xml .="<NBMOTION>".$nbeventmotion."</NBMOTION>";
	$xml .="<NBEERELAY1>".$nbeerelay1."</NBEERELAY1><NBEERELAY2>".$nbeerelay2."</NBEERELAY2><NBEERELAY3>".$nbeerelay3."</NBEERELAY3><NBEERELAY4>".$nbeerelay4."</NBEERELAY4><NBEERELAY5>".$nbeerelay5."</NBEERELAY5>";
	$xml .="</ANALYTICS></DOORBIRDX>";
	sdk_header('text/xml');
 	echo $xml;
	die();
} else if ($action == "void") {
		die();
}
?>