<?php
// This script and data application were generated by AppGini 4.50
// Download AppGini for free from http://www.bigprof.com/appgini/download/

	$d=dirname(__FILE__);
	include("$d/defaultLang.php");
	include("$d/language.php");
	include("$d/lib.php");

	header("Content-type: text/javascript; charset=iso-8859-1");

	$mfk=$_GET['mfk'];
	$id=makeSafe($_GET['id']);

	if(!$mfk || !$id){
		die('// no js code available!');
	}

	switch($mfk){

		case 'client':
			$res=sql("select * from `clients` where `id`='$id' limit 1");
			if($row=mysql_fetch_assoc($res)){
				?>
				$('client_contact').innerHTML='<?php echo addslashes(str_replace(array("\r", "\n"), "<br />", $row['contact'])); ?>&nbsp;';
				$('client_address').innerHTML='<?php echo addslashes(str_replace(array("\r", "\n"), "<br />", $row['address'])); ?>&nbsp;';
				$('client_phone').innerHTML='<?php echo addslashes(str_replace(array("\r", "\n"), "<br />", $row['phone'])); ?>&nbsp;';
				$('client_email').innerHTML='<?php echo addslashes(str_replace(array("\r", "\n"), "<br />", $row['email'])); ?>&nbsp;';
				$('client_website').innerHTML='<?php echo addslashes(str_replace(array("\r", "\n"), "<br />", $row['website'])); ?>&nbsp;';
				$('client_comments').innerHTML='<?php echo addslashes(str_replace(array("\r", "\n"), "<br />", $row['comments'])); ?>&nbsp;';
				<?php
			}
			break;


	}

?>