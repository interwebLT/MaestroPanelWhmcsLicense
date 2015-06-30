<?php
// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
//				Son Düzenleme: 26.06.2014						//
// ############################################################	//

require_once("libs/MaestroPanelLisans.php");

if ( !defined( "WHMCS" ) )
    die( "Bu dosyaya doğrudan erişim izni bulunmamaktadır." );

function maestropanellisans_config() {
    $configarray = array(
        "name" => "MaestroPanel Lisans",
        "description" => "MaestroPanel - WHMCS Lisans Otomasyonu",
        "version" => "1.0",
        "author" => "bilrom.com",
        "language" => "turkish",
        "fields" => array(
            "apikey" => array ( "FriendlyName" => "API Key", "Type" => "password", "Size" => "50" ),
			"externalapikey" => array ( "FriendlyName" => "External API Key", "Type" => "password", "Size" => "50" ),
			"licensenameprefix" => array ( "FriendlyName" => "License Name Prefix", "Type" => "text", "Size" => "50", "Description" => "The prefix for license names. ie. 'BLRM-'"),
            "testmode" => array ( "FriendlyName" => "Test Modu", "Type" => "yesno", "Size" => "25" )
        ) );
    return $configarray;
}

function maestropanellisans_activate() {
}

function maestropanellisans_deactivate() {
}

function maestropanellisans_upgrade( $vars ) {
}

function maestropanellisans_createlicense($license_name, $license_type, $license_ip) {
	$actionresult=array();
		$result=mysql_fetch_assoc( mysql_query( "select * from  tbladdonmodules where module='maestropanellisans' and setting='licensenameprefix' ORDER BY value DESC LIMIT 1" ) );
		$licensename=$result["value"] . $license_name;
			if ($licensename == "" or $license_type == ""){
			$actionresult["result"]="ERR";
			$actionresult["reason"]="Create new license ERRed. Reason: <b>Parameters cannot be empty!</b>";
		}elseif (!empty($_POST["licenseip"])){
			$checkip=explode(".", $license_ip);
			if ((!is_array($checkip)) or (count($checkip) != 4)){
				$actionresult["result"]="ERR";
				$actionresult["reason"]="Create new license ERRed. Reason: <b>IP is not valid!</b>";
			}
		}elseif (strlen($licensename) > 140){
			$actionresult["result"]="ERR";
			$actionresult["reason"]="Create new license ERRed. Reason: <b>License name prefix + license name cannot excess 140 chars!</b>";
		}elseif (!ctype_alnum($license_name)){
			$actionresult["result"]="ERR";
			$actionresult["reason"]="Create new license ERRed. Reason: <b>License name has to be alphanumeric!</b>";
		}
		
		if ($actionresult["result"] != "ERR"){
			$mp = new MaestroPanelLisans();
			$createnewlicense = $mp->create( $license_type, $licensename, $license_ip );
			if( !$createnewlicense->errorCode ){
				$actionresult["result"]="OK";
				$actionresult["reason"]="New license created OKfully. <br>License Code: <b>" . $createnewlicense->license->licenseCode . "</b><br>License Name: <b>" . $createnewlicense->license->licenseName . "</b>";
				logModuleCall('maestropanellisans','AdminCreate','Admin ID: ' . $_SESSION['adminid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $createnewlicense->license->licenseCode . PHP_EOL . 'License Type: ' . $license_type,'OK','','');
			}else{
				$actionresult["result"]="ERR";
				$actionresult["reason"]="Create new license ERRed. Reason: <b>" . $createnewlicense->Message . "</b>";
			}
		}
	return $actionresult;
}

function maestropanellisans_cancellicense($license_code){
	$actionresult=array();
	$mp = new MaestroPanelLisans();
	$cancellicense = $mp->CancelLicense( $license_code );
		
		if( !$cancellicense->errorCode ){
				$actionresult["result"]="OK";
				$actionresult["reason"]="License cancelled OKfully. <br>License Code: <b>" . $license_code . "</b>";
			}else{
				$actionresult["result"]="ERR";
				$actionresult["reason"]="Create new license ERRed. Reason: <b>" . $cancellicense->Message . "</b>";
		}
	return $actionresult;
}

function maestropanellisans_toggleautorenew($license_code, $auto_renew){
	$actionresult=array();
	$mp = new MaestroPanelLisans();
	$toggleautorenew = $mp->ToggleAutoRenew(  $license_code, $auto_renew );
		$actionresult=array();
		if( !$toggleautorenew->errorCode ){
			$actionresult["result"]="OK";
			$actionresult["reason"]="Auto renew has been <b>" . strtolower($auto_renew) . "d</b>.<br>License Code: <b>" . $_POST["licensecode"] . "</b>";
			logModuleCall('maestropanellisans','Admin'.$auto_renew.'AutoRenew','Admin ID: ' . $_SESSION['adminid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $license_code,'OK','','');
		}
		else{
			$actionresult["result"]="ERR";
			$actionresult["reason"]="Auto renew " . strtolower($auto_renew) . " ERRed. Reason: <b>" . $toggleautorenew->Message . "</b>";
		}
	return $actionresult;
}

function maestropanellisans_reissuelicense($license_code){
	$actionresult=array();
	$mp = new MaestroPanelLisans();
	$reissuelicense = $mp->reissue($license_code);
		$actionresult=array();
		if( !$reissuelicense->errorCode ){
			$actionresult["result"]="OK";
			$actionresult["reason"]="License has been reissued. <br>License Code: <b>" . $_POST["licensecode"] . "</b>";
			logModuleCall('maestropanellisans','AdminReissue','Admin ID: ' . $_SESSION['adminid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $license_code,'OK','','');
		}
		else{
			$actionresult["result"]="ERR";
			$actionresult["reason"]="Reissue ERRed. Reason: " . $reissuelicense->Message;
		}
	return $actionresult;
}

function maestropanellisans_output( $vars ) {
	$actionresult=array();
    $mp = new MaestroPanelLisans();
	if ($_POST["action"] == "ReissueLicense"){
		$actionresult=maestropanellisans_reissuelicense($_POST["licensecode"]);
	}elseif ($_POST["action"] == "ToggleAutoRenew"){
		$actionresult=maestropanellisans_toggleautorenew($_POST["licensecode"], $_POST["autorenew"]);
	}elseif ($_POST["action"] == "NewLicense"){
		echo '<br><div align="center"><form action="addonmodules.php?module=maestropanellisans" method="post">
<table width="50%" border="0">
  <tr>
    <td>License Name</td>
    <td><input name="licensename" type="text" size="30" autocomplete="off" /></td>
  </tr>
  <tr>
    <td>License IP</td>
    <td><input name="licenseip" type="text" size="30" autocomplete="off" /></td>
  </tr>
  <tr>
    <td>License Type</td>
    <td><select name="licensetype">
    <option value="free10">Free 10 Domains</option>
    <option value="free30">Free 30 Domains</option>
    <option value="monthly">Monthly - Unlimited Domains</option>
    <option value="yearly">Yearly - Unlimited Domains</option>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="" type="submit" value="Create New License" /></td>
  </tr>
</table>
<input name="action" id="action" type="hidden" value="CreateNewLicense" />
</form></div></br>';
	}elseif ($_POST["action"] == "CreateNewLicense"){
		$actionresult=maestropanellisans_createlicense($_POST["licensename"], $_POST["licensetype"], $_POST["licenseip"]);
	}elseif ($_POST["action"] == "CancelLicense"){
		$actionresult=maestropanellisans_cancellicense($_POST["licensecode"]);
	}
	
	if (is_array($actionresult)){
		if ($actionresult["result"] == "OK"){
			echo '<div class="OKbox"><strong><span class="title">API Command OK</span></strong><br>' . $actionresult["reason"] . '</div>';
			$actionresult="";
		}elseif ($actionresult["result"] == "ERR"){
			echo '<div class="errorbox"><strong><span class="title">API Command Error</span></strong><br>' . $actionresult["reason"] . '</div>';
			$actionresult="";
		}
	}
	
	$request = $mp->ListLicenses( );
	$licenselist = $request->license;
	echo '
	<script type="text/javascript">
	function FormSubmit(action, licensecode, autorenew) {
		document.sendaction.action.value = action;
		document.sendaction.licensecode.value = licensecode;
		document.sendaction.autorenew.value = autorenew;
		document.sendaction.submit();
		}
	</script>
	<table width="100%" border="0">
	<tr>
    <td align="center"><table width="30%" border="0">
  <tr>
	<td><form action="addonmodules.php?module=maestropanellisans" method="post" name="showlicenses">
	Show License(s):&nbsp;&nbsp;&nbsp;
	<select name="licensestatus" onchange="this.form.submit();">
	<option value="" ' . ($_POST["licensestatus"] == "" ? 'selected="selected"' : '') . '>ALL</option>
	<option value="Active" ' . ($_POST["licensestatus"] == "Active" ? 'selected="selected"' : '') . '>Active</option>
	<option value="Passive" ' . ($_POST["licensestatus"] == "Passive" ? 'selected="selected"' : '') . '>Passive</option>
	<option value="Cancelled" ' . ($_POST["licensestatus"] == "Cancelled" ? 'selected="selected"' : '') . '>Cancelled</option>
	<option value="Pending" ' . ($_POST["licensestatus"] == "Pending" ? 'selected="selected"' : '') . '>Pending</option>
	<option value="Expired" ' . ($_POST["licensestatus"] == "Expired" ? 'selected="selected"' : '') . '>Expired</option>
	<option value="RenewPending" ' . ($_POST["licensestatus"] == "RenewPending" ? 'selected="selected"' : '') . '>RenewPending</option>
    </select></form></td>
    <td><input name="" value="New License" type="button" onclick="FormSubmit(\'NewLicense\');" /></td>
  </tr>
</table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0">
  <tr>
    <td><b>LicenseCode</b></td>
    <td><b>LicenseName</b></td>
    <td><b>IP</b></td>
    <td><b>Expiration</b></td>
    <td><b>Status</b></td>
	<td><b>AutoRenew</b></td>
    <td><b>Addons</b></td>
	<td><b>Process</b></td>
  </tr>';
  $countbg=0;
  $countresults=0;
foreach ($licenselist as $licensedet){
	if ($_POST["licensestatus"] == ""){
    echo '<tr ' . ($countbg == 1 ? 'style="background-color: #CCC;" ondblclick="this.style.backgroundColor=\'#CCC\'"' : 'ondblclick="this.style.backgroundColor=\'\'"') . ' onclick="this.style.backgroundColor=\'#D9DAFF\';">
    <td>' . $licensedet->licenseCode . '</td>
    <td>' . $licensedet->licenseName . '</td>
    <td>' . $licensedet->AssignedIp . '</td>
	<td>' . date("d.m.Y", strtotime( $licensedet->Expiration ) ) . '</td>
    <td>' . $licensedet->Status . '</td>
    <td>' . $licensedet->AutoRenew . '</td>
	<td>';
	$addonlist="";
	if (count($licensedet->Addons) > 0){
		foreach($licensedet->Addons as $addon){
			$addonlist.= $addon->Name . "=" . $addon->Value . ",";
		}
	$addonlist=rtrim($addonlist, ",");
	}else {
		$addonlist="None";
	}
	echo $addonlist . '</td>
	<td>';
	if ($licensedet->Status == 'Active'){
	echo '<select name="lidislem" id="lidislem" onChange="if(confirm(\'!!!!! Attention !!!!! \r\nAction: \' + this.options[this.selectedIndex].text + \'\nLicense Code: ' . $licensedet->licenseCode . '\r\nAre you sure you want to continue?\'))
	{
		FormSubmit(this.options[this.selectedIndex].value, \'' . $licensedet->licenseCode . '\', \'' . ($licensedet->AutoRenew == "True" ? "Disable" : "Enable") . '\');
	};
	">
    <option value="0">&nbsp;</option>
    <option value="ReissueLicense">Reissue</option>
	<option value="ToggleAutoRenew">' . ($licensedet->AutoRenew == "True" ? "Disable" : "Enable") . ' Auto Renew</option>
	<option value="CancelLicense">Cancel License</option>
    </select>';
	}else{
		echo '';
	}
	echo '
	</td>
  </tr>';
  if ($countbg == 1) {
		$countbg=0;
	}else {
		$countbg=1;
	}
	$countresults++;
 }elseif ($licensedet->Status == $_POST["licensestatus"]){
	 echo '<tr ' . ($countbg == 1 ? 'style="background-color: #CCC;" ondblclick="this.style.backgroundColor=\'#CCC\'"' : 'ondblclick="this.style.backgroundColor=\'\'"') . ' onclick="this.style.backgroundColor=\'#D9DAFF\';">
    <td>' . $licensedet->licenseCode . '</td>
    <td>' . $licensedet->licenseName . '</td>
    <td>' . $licensedet->AssignedIp . '</td>
	<td>' . date("d.m.Y", strtotime( $licensedet->Expiration ) ) . '</td>
    <td>' . $licensedet->Status . '</td>
    <td>' . $licensedet->AutoRenew . '</td>
	<td>';
	$addonlist="";
	if (count($licensedet->Addons) > 0){
		foreach($licensedet->Addons as $addon){
			$addonlist.= $addon->Name . "=" . $addon->Value . ",";
		}
	$addonlist=rtrim($addonlist, ",");
	}else {
		$addonlist="None";
	}
	echo $addonlist . '</td>
	<td>';
	if ($licensedet->Status == 'Active'){
	echo '<select name="lidislem" id="lidislem" onChange="if(confirm(\'!!!!! Attention !!!!! \r\nAction: \' + this.options[this.selectedIndex].text + \'\nLicense Code: ' . $licensedet->licenseCode . '\r\nAre you sure you want to continue?\'))
	{
		FormSubmit(this.options[this.selectedIndex].value, \'' . $licensedet->licenseCode . '\', \'' . ($licensedet->AutoRenew == "True" ? "Disable" : "Enable") . '\');
	};
	">
    <option value="0">&nbsp;</option>
    <option value="ReissueLicense">Reissue</option>
	<option value="ToggleAutoRenew">' . ($licensedet->AutoRenew == "True" ? "Disable" : "Enable") . ' Auto Renew</option>
	<option value="CancelLicense">Cancel License</option>
    </select>';
	}else{
		echo 'N/A';
	}
	echo '
	</td>
  </tr>';
  if ($countbg == 1) {
		$countbg=0;
	}else {
		$countbg=1;
	}
$countresults++;
 }
}
 
  echo '
</table>
</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<form action="addonmodules.php?module=maestropanellisans" method="post" name="sendaction">
<input name="action" id="action" type="hidden" value="" />
<input name="licensecode" id="licensecode" type="hidden" value="" />
<input name="autorenew" id="autorenew" type="hidden" value="" />
<input name="licensestatus" id="licensestatus" type="hidden" value="' . $_POST["licensestatus"] . '" />
</form><br><br><div align="center">';
if ($countresults == 0){
	echo "No license found with this criteria.";
}elseif ($countresults == 1){
	echo "1 license found with this criteria.";
}else {
	echo $countresults . " licenses found with this criteria.";
}
echo '</div>';
}
