<?php
// This script and data application were generated by AppGini 4.50
// Download AppGini for free from http://www.bigprof.com/appgini/download/

	$d=dirname(__FILE__);
	include("$d/defaultLang.php");
	include("$d/language.php");
	include("$d/lib.php");
	@include("$d/hooks/invoices.php");
	include("$d/invoices_dml.php");
	// mm: can the current member access this page?
	$perm=getTablePermissions('invoices');
	if(!$perm[0]){
		echo StyleSheet();
		echo "<div class=\"error\">".$Translation['tableAccessDenied']."</div>";
		echo '<script language="javaScript">setInterval("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "invoices";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(
		"`invoices`.`id`" => "ID",
		"`invoices`.`code`" => "Code",
		"`invoices`.`status`" => "Status",
		"if(`invoices`.`date_due`,date_format(`invoices`.`date_due`,'%d/%m/%Y'),'')" => "Date due",
		"`clients1`.`name` /* Client */" => "Client",
		"`clients1`.`contact` /* Client contact */" => "Client contact",
		"`clients1`.`address` /* Client address */" => "Client address",
		"`clients1`.`phone` /* Client phone */" => "Client phone",
		"`clients1`.`email` /* Client email */" => "Client email",
		"`clients1`.`website` /* Client website */" => "Client website",
		"`clients1`.`comments` /* Client comments */" => "Client comments",
		"FORMAT(`invoices`.`subtotal`, 2)" => "Subtotal",
		"`invoices`.`discount`" => "Discount %",
		"FORMAT(`invoices`.`tax`, 2)" => "Tax",
		"FORMAT(`invoices`.`total`, 2)" => "Total",
		"`invoices`.`comments`" => "Comments"
	);
	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(
		"`invoices`.`id`" => "ID",
		"`invoices`.`code`" => "Code",
		"`invoices`.`status`" => "Status",
		"if(`invoices`.`date_due`,date_format(`invoices`.`date_due`,'%d/%m/%Y'),'')" => "Date due",
		"`clients1`.`name` /* Client */" => "Client",
		"`clients1`.`contact` /* Client contact */" => "Client contact",
		"`clients1`.`address` /* Client address */" => "Client address",
		"`clients1`.`phone` /* Client phone */" => "Client phone",
		"`clients1`.`email` /* Client email */" => "Client email",
		"`clients1`.`website` /* Client website */" => "Client website",
		"`clients1`.`comments` /* Client comments */" => "Client comments",
		"FORMAT(`invoices`.`subtotal`, 2)" => "Subtotal",
		"`invoices`.`discount`" => "Discount %",
		"FORMAT(`invoices`.`tax`, 2)" => "Tax",
		"FORMAT(`invoices`.`total`, 2)" => "Total",
		"`invoices`.`comments`" => "Comments"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(
		"`invoices`.`id`" => "ID",
		"`invoices`.`code`" => "Code",
		"`invoices`.`status`" => "Status",
		"`invoices`.`date_due`" => "Date due",
		"`clients1`.`name` /* Client */" => "Client",
		"`clients1`.`contact` /* Client contact */" => "Client contact",
		"`clients1`.`address` /* Client address */" => "Client address",
		"`clients1`.`phone` /* Client phone */" => "Client phone",
		"`clients1`.`email` /* Client email */" => "Client email",
		"`clients1`.`website` /* Client website */" => "Client website",
		"`clients1`.`comments` /* Client comments */" => "Client comments",
		"`invoices`.`subtotal`" => "Subtotal",
		"`invoices`.`discount`" => "Discount %",
		"`invoices`.`tax`" => "Tax",
		"`invoices`.`total`" => "Total",
		"`invoices`.`comments`" => "Comments"
	);

	$x->QueryFrom="`invoices` LEFT JOIN `clients` as clients1 ON `invoices`.`client`=clients1.`id` ";
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
	$x->ScriptFileName = "invoices_view.php";
	$x->RedirectAfterInsert = "invoices_view.php?SelectedID=#ID#";
	$x->TableTitle = "Invoices";
	$x->PrimaryKey = "`invoices`.`id`";

	$x->ColWidth   = array(60, 70, 100, 250, 200, 100, 70);
	$x->ColCaption = array("Code", "Status", "Date due", "Client", "Client contact", "Client phone", "Total");
	$x->ColNumber  = array(2, 3, 4, 5, 6, 8, 15);

	$x->Template = 'templates/invoices_templateTV.html';
	$x->SelectedTemplate = 'templates/invoices_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	if($perm[2]==1){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `invoices`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='invoices' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `invoices`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='invoices' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`invoices`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}

	// handle date sorting correctly
	if($_POST['SortField']=='4' || $_POST['SortField']=='`invoices`.`date_due`' || $_POST['SortField']=='invoices.date_due'){
		$_POST['SortField']='`invoices`.`date_due`';
		$SortFieldNumeric=4;
	}
	if($_GET['SortField']=='4' || $_GET['SortField']=='`invoices`.`date_due`' || $_GET['SortField']=='invoices.date_due'){
		$_GET['SortField']='`invoices`.`date_due`';
		$SortFieldNumeric=4;
	}
	// end of date sorting handler

	// hook: invoices_init
	$render=TRUE;
	if(function_exists('invoices_init')){
		$args=array();
		$render=invoices_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// column sums
	if(strpos($x->HTML, '<!-- tv data below -->')){
		$sumQuery="select FORMAT(sum(`invoices`.`total`), 2) from ".$x->QueryFrom.' '.$x->QueryWhere;
		$sumStyle='color: green; text-align: right; font-weight: normal;';
		$res=sql($sumQuery);
		if($row=mysql_fetch_row($res)){
			$sumRow ="<tr>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\"><b>&sum;</b></td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;</td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;</td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;</td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;</td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;</td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;</td>";
			$sumRow.="<td class=\"TableHeader\" style=\"$sumStyle\">&nbsp;$row[0]</td>";
			$sumRow.="</tr>";

			$x->HTML=str_replace("<!-- tv data below -->", '', $x->HTML);
			$x->HTML=str_replace("<!-- tv data above -->", $sumRow, $x->HTML);
		}
	}

	// hook: invoices_header
	$headerCode='';
	if(function_exists('invoices_header')){
		$args=array();
		$headerCode=invoices_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include("$d/header.php"); 
	}else{
		ob_start(); include("$d/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: invoices_footer
	$footerCode='';
	if(function_exists('invoices_footer')){
		$args=array();
		$footerCode=invoices_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include("$d/footer.php"); 
	}else{
		ob_start(); include("$d/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>