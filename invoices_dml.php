<?php

// Data functions for table invoices

// This script and data application were generated by AppGini 4.50
// Download AppGini for free from http://www.bigprof.com/appgini/download/

function invoices_insert(){
	global $Translation;

	if($_GET['insert_x']){$_POST=$_GET;}

	// mm: can member insert record?
	$arrPerm=getTablePermissions('invoices');
	if(!$arrPerm[1]){
		return 0;
	}

	$data['code'] = makeSafe($_POST['code']);
	$data['status'] = makeSafe($_POST['status']);
	$data['date_due'] = makeSafe($_POST['date_dueYear']) . '-' . makeSafe($_POST['date_dueMonth']) . '-' . makeSafe($_POST['date_dueDay']);
	$data['date_due'] = parseMySQLDate($data['date_due'], '1');
	$data['client'] = makeSafe($_POST['client']);
	$data['client_contact'] = makeSafe($_POST['client']);
	$data['client_address'] = makeSafe($_POST['client']);
	$data['client_phone'] = makeSafe($_POST['client']);
	$data['client_website'] = makeSafe($_POST['client']);
	$data['client_comments'] = makeSafe($_POST['client']);
	$data['discount'] = makeSafe($_POST['discount']);
	$data['comments'] = makeSafe($_POST['comments']);
	if($data['status'] == '') $data['status'] = "Due";
	if($data['status']== ''){
		echo StyleSheet() . "\n\n<div class=\"Error\">" . $Translation['error:'] . " 'Status': " . $Translation['field not null'] . '<br /><br />';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	if($data['discount'] == '') $data['discount'] = "0";
	if($data['total'] == '') $data['total'] = "0";

	// hook: invoices_before_insert
	if(function_exists('invoices_before_insert')){
		$args=array();
		if(!invoices_before_insert($data, getMemberInfo(), $args)){ return FALSE; }
	}

	sql('insert into `invoices` set `code`=' . (($data['code'] != '') ? "'{$data['code']}'" : 'NULL') . ', `status`=' . (($data['status'] != '') ? "'{$data['status']}'" : 'NULL') . ', `date_due`=' . (($data['date_due'] != '') ? "'{$data['date_due']}'" : 'NULL') . ', `client`=' . (($data['client'] != '') ? "'{$data['client']}'" : 'NULL') . ', `client_contact`=' . (($data['client_contact'] != '') ? "'{$data['client_contact']}'" : 'NULL') . ', `client_address`=' . (($data['client_address'] != '') ? "'{$data['client_address']}'" : 'NULL') . ', `client_phone`=' . (($data['client_phone'] != '') ? "'{$data['client_phone']}'" : 'NULL') . ', `client_website`=' . (($data['client_website'] != '') ? "'{$data['client_website']}'" : 'NULL') . ', `client_comments`=' . (($data['client_comments'] != '') ? "'{$data['client_comments']}'" : 'NULL') . ', `discount`=' . (($data['discount'] != '') ? "'{$data['discount']}'" : 'NULL') . ', `comments`=' . (($data['comments'] != '') ? "'{$data['comments']}'" : 'NULL'));
	$recID=mysql_insert_id();

	// hook: invoices_after_insert
	if(function_exists('invoices_after_insert')){
		$data['selectedID']=$recID;
		$args=array();
		if(!invoices_after_insert($data, getMemberInfo(), $args)){ return; }
	}

	// mm: save ownership data
	sql("insert into membership_userrecords set tableName='invoices', pkValue='$recID', memberID='".getLoggedMemberID()."', dateAdded='".time()."', dateUpdated='".time()."', groupID='".getLoggedGroupID()."'");

	return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID);
}

function invoices_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('invoices');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='invoices' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='invoices' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return FALSE;
	}

	// hook: invoices_before_delete
	if(function_exists('invoices_before_delete')){
		$args=array();
		if(!invoices_before_delete($selected_id, $skipChecks, getMemberInfo(), $args)){ return FALSE; }
	}

	// child table: invoice_items
	$res = sql("select `id` from `invoices` where `id`='$selected_id'");
	$id = mysql_fetch_row($res);
	$rires = sql("select count(1) from `invoice_items` where `invoice`='".addslashes($id[0])."'");
	$rirow = mysql_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "invoice_items", $RetMsg);
		return $RetMsg;
	}elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["confirm delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "invoice_items", $RetMsg);
		$RetMsg = str_replace("<Delete>", "<input type=button class=button value=\"".$Translation['yes']."\" onClick=\"window.location='invoices_view.php?SelectedID=".urlencode($selected_id)."&delete_x=1&confirmed=1';\">", $RetMsg);
		$RetMsg = str_replace("<Cancel>", "<input type=button class=button value=\"".$Translation['no']."\" onClick=\"window.location='invoices_view.php?SelectedID=".urlencode($selected_id)."';\">", $RetMsg);
		return $RetMsg;
	}

	sql("delete from `invoices` where `id`='$selected_id'");

	// hook: invoices_after_delete
	if(function_exists('invoices_after_delete')){
		$args=array();
		invoices_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='invoices' and pkValue='$selected_id'");
}

function invoices_update($selected_id){
	global $Translation;

	if($_GET['update_x']){$_POST=$_GET;}

	// mm: can member edit record?
	$arrPerm=getTablePermissions('invoices');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='invoices' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='invoices' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return;
	}

	$data['code'] = makeSafe($_POST['code']);
	$data['status'] = makeSafe($_POST['status']);
	if($data['status']==''){
		echo StyleSheet() . "\n\n<div class=\"Error\">{$Translation['error:']} 'Status': {$Translation['field not null']}<br /><br />";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['date_due'] = makeSafe($_POST['date_dueYear']) . '-' . makeSafe($_POST['date_dueMonth']) . '-' . makeSafe($_POST['date_dueDay']);
	$data['date_due'] = parseMySQLDate($data['date_due'], '1');
	$data['client'] = makeSafe($_POST['client']);
	$data['client_contact'] = makeSafe($_POST['client']);
	$data['client_address'] = makeSafe($_POST['client']);
	$data['client_phone'] = makeSafe($_POST['client']);
	$data['client_website'] = makeSafe($_POST['client']);
	$data['client_comments'] = makeSafe($_POST['client']);
	$data['discount'] = makeSafe($_POST['discount']);
	$data['comments'] = makeSafe($_POST['comments']);
	$data['selectedID']=makeSafe($selected_id);

	// hook: invoices_before_update
	if(function_exists('invoices_before_update')){
		$args=array();
		if(!invoices_before_update($data, getMemberInfo(), $args)){ return FALSE; }
	}

	sql('update `invoices` set `code`=' . (($data['code'] != '') ? "'{$data['code']}'" : 'NULL') . ', `status`=' . (($data['status'] != '') ? "'{$data['status']}'" : 'NULL') . ', `date_due`=' . (($data['date_due'] != '') ? "'{$data['date_due']}'" : 'NULL') . ', `client`=' . (($data['client'] != '') ? "'{$data['client']}'" : 'NULL') . ', `client_contact`=' . (($data['client_contact'] != '') ? "'{$data['client_contact']}'" : 'NULL') . ', `client_address`=' . (($data['client_address'] != '') ? "'{$data['client_address']}'" : 'NULL') . ', `client_phone`=' . (($data['client_phone'] != '') ? "'{$data['client_phone']}'" : 'NULL') . ', `client_website`=' . (($data['client_website'] != '') ? "'{$data['client_website']}'" : 'NULL') . ', `client_comments`=' . (($data['client_comments'] != '') ? "'{$data['client_comments']}'" : 'NULL') . ', `discount`=' . (($data['discount'] != '') ? "'{$data['discount']}'" : 'NULL') . ', `comments`=' . (($data['comments'] != '') ? "'{$data['comments']}'" : 'NULL') . " where `id`='".makeSafe($selected_id)."'");

	// hook: invoices_after_update
	if(function_exists('invoices_after_update')){
		$args=array();
		if(!invoices_after_update($data, getMemberInfo(), $args)){ return FALSE; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='invoices' and pkValue='".makeSafe($selected_id)."'");

}

function invoices_form($selected_id = "", $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;


	// mm: get table permissions
	$arrPerm=getTablePermissions('invoices');
	if(!$arrPerm[1] && $selected_id==""){ return ""; }
	// combobox: status
	$combo_status = new Combo;
	$combo_status->ListType = 2;
	$combo_status->ListBoxHeight = 10;
	$combo_status->RadiosPerLine = 1;
	$combo_status->ListItem = explode(";;", "Due;;Paid;;Cancelled");
	$combo_status->ListData = explode(";;", "Due;;Paid;;Cancelled");
	$combo_status->SelectName = "status";
	$combo_status->AllowNull = false;
	// combobox: date_due
	$combo_date_due = new DateCombo;
	$combo_date_due->DateFormat = "dmy";
	$combo_date_due->MinYear = 1900;
	$combo_date_due->MaxYear = 2100;
	$combo_date_due->DefaultDate = parseMySQLDate('1', '1');
	$combo_date_due->MonthNames = $Translation['month names'];
	$combo_date_due->CSSOptionClass = 'Option';
	$combo_date_due->CSSSelectedClass = 'SelectedOption';
	$combo_date_due->NamePrefix = 'date_due';
	// combobox: client
	$combo_client = new DataCombo;
	$combo_client->Query = "select `id`, `name` from `clients` order by 2";
	$combo_client->SelectName = 'client';
	$combo_client->ListType = 0;

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='invoices' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='invoices' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID){
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID){
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("select * from `invoices` where `id`='".makeSafe($selected_id)."'");
		$row = mysql_fetch_array($res);
		$combo_status->SelectedData = $row["status"];
		$combo_date_due->DefaultDate = $row["date_due"];
		$combo_client->SelectedData = $row["client"];
		$row['subtotal']=sqlValue("select FORMAT(`subtotal`, 2) from `invoices` where `id`='".makeSafe($selected_id)."'");
		$row['tax']=sqlValue("select FORMAT(`tax`, 2) from `invoices` where `id`='".makeSafe($selected_id)."'");
		$row['total']=sqlValue("select FORMAT(`total`, 2) from `invoices` where `id`='".makeSafe($selected_id)."'");
	}else{
		$combo_status->SelectedText = ( $_REQUEST['FilterField'][1]=='3' && $_REQUEST['FilterOperator'][1]=='<=>' ? (get_magic_quotes_gpc() ? stripslashes($_REQUEST['FilterValue'][1]) : $_REQUEST['FilterValue'][1]) : "Due");
		$combo_client->SelectedText = ( $_REQUEST['FilterField'][1]=='5' && $_REQUEST['FilterOperator'][1]=='<=>' ? (get_magic_quotes_gpc() ? stripslashes($_REQUEST['FilterValue'][1]) : $_REQUEST['FilterValue'][1]) : "");
	}
	$combo_status->Render();
	$combo_client->Render();

	// code for template based detail view forms

	// open the detail view template
	if(($_POST['dvprint_x'] || $_GET['dvprint_x']) && $selected_id){
		$templateCode=@implode('', @file('./templates/invoices_templateDVP.html'));
		$dvprint=true;
	}else{
		$templateCode=@implode('', @file('./templates/invoices_templateDV.html'));
		$dvprint=false;
	}

	// process form title
	$templateCode=str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Invoice data', $templateCode);
	// process buttons
	if($arrPerm[1]){ // allow insert?
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<input type="image" src="insert.gif" name="insert" alt="' . $Translation['add new record'] . '" onclick="return validateData();">', $templateCode);
	}else{
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}
	if($selected_id){
		$templateCode=str_replace('<%%DVPRINT_BUTTON%%>', '<input type="image" src="print.gif" vspace="1" name="dvprint" id="dvprint" alt="' . $Translation['printer friendly view'] . '" onclick="document.myform.reset(); return true;" style="margin-bottom: 20px;">', $templateCode);
		if($AllowUpdate){
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<input type="image" src="update.gif" vspace="1" name="update" alt="' . $Translation['update record'] . '" onclick="return validateData();">', $templateCode);
		}else{
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

			// set records to read only if user can't insert new records
			if(!$arrPerm[1]){
				$jsReadOnly.="\n\n\tif(document.getElementsByName('id').length){ document.getElementsByName('id')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('code').length){ document.getElementsByName('code')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('status').length){ var status=document.getElementsByName('status'); for(var i=0; i<status.length; i++){ status[i].disabled=true; } }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('date_due').length){ document.getElementsByName('date_due')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('date_dueDay').length){ var date_dueDay=document.getElementsByName('date_dueDay')[0]; date_dueDay.disabled=true; date_dueDay.style.backgroundColor='white'; date_dueDay.style.color='black'; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('date_dueMonth').length){ var date_dueMonth=document.getElementsByName('date_dueMonth')[0]; date_dueMonth.disabled=true; date_dueMonth.style.backgroundColor='white'; date_dueMonth.style.color='black'; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('date_dueYear').length){ var date_dueYear=document.getElementsByName('date_dueYear')[0]; date_dueYear.disabled=true; date_dueYear.style.backgroundColor='white'; date_dueYear.style.color='black'; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('client').length){ var client=document.getElementsByName('client')[0]; client.disabled=true; client.style.backgroundColor='white'; client.style.color='black'; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('subtotal').length){ document.getElementsByName('subtotal')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('discount').length){ document.getElementsByName('discount')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('tax').length){ document.getElementsByName('tax')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('total').length){ document.getElementsByName('total')[0].readOnly=true; }\n";

				$noUploads=true;
			}
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '<input type="image" src="delete.gif" vspace="1" name="delete" alt="' . $Translation['delete record'] . '" onClick="return confirm(\'' . $Translation['are you sure?'] . '\');">', $templateCode);
		}else{
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', "<input type=image src=deselect.gif vspace=1 name=deselect alt=\"" . $Translation['deselect record'] . "\" onclick=\"document.myform.reset(); return true;\">", $templateCode);
	}else{
		$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? "<input type=image src=cancel.gif vspace=1 name=deselect alt=\"" . $Translation['deselect record'] . "\" onclick=\"document.myform.reset(); return true;\">" : ''), $templateCode);
	}

	// process combos
	$templateCode=str_replace('<%%COMBO(status)%%>', $combo_status->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(status)%%>', $combo_status->SelectedData, $templateCode);
	$templateCode=str_replace('<%%COMBO(date_due)%%>', $combo_date_due->GetHTML(), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(date_due)%%>', $combo_date_due->GetHTML(true), $templateCode);
	$templateCode=str_replace('<%%COMBO(client)%%>', $combo_client->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(client)%%>', $combo_client->MatchText, $templateCode);

	// process foreign key links
	if($selected_id){
		$templateCode=str_replace('<%%PLINK(client)%%>', ($combo_client->SelectedData ? "<span id=clients_plink1 style=\"visibility: hidden;\"><a href=clients_view.php?SelectedID=".$combo_client->SelectedData."><img border=0 src=lookup.gif></a></span>" : ''), $templateCode);
	}

	// process images
	$templateCode=str_replace('<%%UPLOADFILE(id)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(code)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(status)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(date_due)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(client)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(subtotal)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(discount)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(tax)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(total)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(comments)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(id)%%>', htmlspecialchars($row['id'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(code)%%>', htmlspecialchars($row['code'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(status)%%>', htmlspecialchars($row['status'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(date_due)%%>', date('j/n/Y', strtotime(htmlspecialchars($row['date_due'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%VALUE(client)%%>', htmlspecialchars($row['client'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(subtotal)%%>', htmlspecialchars($row['subtotal'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(discount)%%>', htmlspecialchars($row['discount'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(tax)%%>', htmlspecialchars($row['tax'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%VALUE(total)%%>', htmlspecialchars($row['total'], ENT_QUOTES), $templateCode);
		if($AllowUpdate || $AllowInsert){
			$templateCode=str_replace('<%%HTMLAREA(comments)%%>', '<textarea name="comments" id="comments" cols="50" rows="5" class="TextBox">'.htmlspecialchars($row['comments'], ENT_QUOTES).'</textarea>', $templateCode);
		}else{
			$templateCode=str_replace('<%%HTMLAREA(comments)%%>', $row['comments'], $templateCode);
		}
		$templateCode=str_replace('<%%VALUE(comments)%%>', $row['comments'], $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode=str_replace('<%%VALUE(code)%%>', '', $templateCode);
		$templateCode=str_replace('<%%VALUE(status)%%>', 'Due', $templateCode);
		$templateCode=str_replace('<%%VALUE(date_due)%%>', '1', $templateCode);
		$templateCode=str_replace('<%%VALUE(client)%%>', '', $templateCode);
		$templateCode=str_replace('<%%VALUE(subtotal)%%>', '', $templateCode);
		$templateCode=str_replace('<%%VALUE(discount)%%>', '0', $templateCode);
		$templateCode=str_replace('<%%VALUE(tax)%%>', '', $templateCode);
		$templateCode=str_replace('<%%VALUE(total)%%>', '0', $templateCode);
		$templateCode=str_replace('<%%HTMLAREA(comments)%%>', '<textarea name="comments" id="comments" cols="50" rows="5" class="TextBox"></textarea>', $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans){
		$templateCode=str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode=str_replace('<%%', '<!--', $templateCode);
	$templateCode=str_replace('%%>', '-->', $templateCode);
	// hide links to inaccessible tables
	$templateCode.="\n\n<script>\n";
	$arrTables=getTableList();
	foreach($arrTables as $name=>$caption){
		$templateCode.="\tif(document.getElementById('".$name."_link')!=undefined){\n";
		$templateCode.="\t\tdocument.getElementById('".$name."_link').style.visibility='visible';\n";
		$templateCode.="\t}\n";
		for($i=1; $i<10; $i++){
			$templateCode.="\tif(document.getElementById('".$name."_plink$i')!=undefined){\n";
			$templateCode.="\t\tdocument.getElementById('".$name."_plink$i').style.visibility='visible';\n";
			$templateCode.="\t}\n";
		}
	}

	$templateCode.=$jsReadOnly;

	$templateCode.="\n\tfunction validateData(){";
	$templateCode.="\n\t\tif(\$F('status')==''){ alert('".addslashes($Translation['error:']).' "Status": '.addslashes($Translation['field not null'])."'); \$('status').focus(); return false; }";
	$templateCode.="\n\t\treturn true;";
	$templateCode.="\n\t}";
	$templateCode.="\n</script>\n";


	// ajaxed auto-fill fields
	$templateCode.="<script>";
	$templateCode.="window.onload=function(){";

	$templateCode.="\tfunction clientChanged(){\n";
	$templateCode.="\t\tnew Ajax.Request(\n";
	if($dvprint){
	$templateCode.="\t\t\t'invoices_autofill.php?mfk=client&id='+encodeURIComponent('".addslashes($row['client'])."'),\n";
	$templateCode.="\t\t\t{encoding: 'iso-8859-1', method: 'get'}\n";
	}else{
	$templateCode.="\t\t\t'invoices_autofill.php?mfk=client&id='+encodeURIComponent(\$F('client')),\n";
	$templateCode.="\t\t\t{encoding: 'iso-8859-1', method: 'get', onCreate: function(){ \$('client').disable(); \$('clientLoading').innerHTML='<img src=loading.gif align=top>'; }, onComplete: function(){".(($arrPerm[1] || (($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3)) ? "\$('client').enable(); " : "\$('client').disable(); ")."\$('clientLoading').innerHTML='';}}\n";
	}
	$templateCode.="\t\t);\n";
	$templateCode.="\t};\n";
	$templateCode.="\tclientChanged();\n";
	if(!$dvprint) $templateCode.="\t\$('client').onchange=clientChanged;\n";


	$templateCode.="}";
	$templateCode.="</script>";

	// handle enforced parent values for read-only lookup fields
	if( $_REQUEST['FilterField'][1]=='9' && $_REQUEST['FilterOperator'][1]=='<=>'){
		$templateCode.="\n<input type=hidden name=client_email value=\"".htmlspecialchars((get_magic_quotes_gpc() ? stripslashes($_REQUEST['FilterValue'][1]) : $_REQUEST['FilterValue'][1]))."\">\n";
	}

	// don't include blank images in lightbox gallery
	$templateCode=preg_replace('/blank.gif" rel="lightbox\[.*?\]"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	// hook: invoices_dv
	if(function_exists('invoices_dv')){
		$args=array();
		invoices_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>