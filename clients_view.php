<?php
// This script and data application were generated by AppGini 4.50
// Download AppGini for free from http://www.bigprof.com/appgini/download/

	$d=dirname(__FILE__);
	include("$d/defaultLang.php");
	include("$d/language.php");
	include("$d/lib.php");
	@include("$d/hooks/clients.php");
	include("$d/clients_dml.php");
	// mm: can the current member access this page?
	$perm=getTablePermissions('clients');
	if(!$perm[0]){
		echo StyleSheet();
		echo "<div class=\"error\">".$Translation['tableAccessDenied']."</div>";
		echo '<script language="javaScript">setInterval("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "clients";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(
		"`clients`.`id`" => "ID",
		"`clients`.`name`" => "Name",
		"`clients`.`contact`" => "Contact",
		"`clients`.`address`" => "Address",
		"`clients`.`phone`" => "Phone",
		"`clients`.`email`" => "Email",
		"`clients`.`website`" => "Website",
		"`clients`.`comments`" => "Comments"
	);
	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(
		"`clients`.`id`" => "ID",
		"`clients`.`name`" => "Name",
		"`clients`.`contact`" => "Contact",
		"`clients`.`address`" => "Address",
		"`clients`.`phone`" => "Phone",
		"`clients`.`email`" => "Email",
		"`clients`.`website`" => "Website",
		"`clients`.`comments`" => "Comments"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(
		"`clients`.`id`" => "ID",
		"`clients`.`name`" => "Name",
		"`clients`.`contact`" => "Contact",
		"`clients`.`address`" => "Address",
		"`clients`.`phone`" => "Phone",
		"`clients`.`email`" => "Email",
		"`clients`.`website`" => "Website",
		"`clients`.`comments`" => "Comments"
	);

	$x->QueryFrom="`clients` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

	$x->DataHeight = 150;
	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 1;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 3;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "clients_view.php";
	$x->RedirectAfterInsert = "clients_view.php?SelectedID=#ID#";
	$x->TableTitle = "Clients";
	$x->PrimaryKey = "`clients`.`id`";

	$x->ColWidth   = array(250, 200, 150, 50, 50);
	$x->ColCaption = array("Name", "Contact", "Phone", "Email", "Website");
	$x->ColNumber  = array(2, 3, 5, 6, 7);

	$x->Template = 'templates/clients_templateTV.html';
	$x->SelectedTemplate = 'templates/clients_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	if($perm[2]==1){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `clients`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='clients' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `clients`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='clients' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`clients`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}

	// handle date sorting correctly
	// end of date sorting handler

	// hook: clients_init
	$render=TRUE;
	if(function_exists('clients_init')){
		$args=array();
		$render=clients_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: clients_header
	$headerCode='';
	if(function_exists('clients_header')){
		$args=array();
		$headerCode=clients_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include("$d/header.php"); 
	}else{
		ob_start(); include("$d/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: clients_footer
	$footerCode='';
	if(function_exists('clients_footer')){
		$args=array();
		$footerCode=clients_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include("$d/footer.php"); 
	}else{
		ob_start(); include("$d/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>