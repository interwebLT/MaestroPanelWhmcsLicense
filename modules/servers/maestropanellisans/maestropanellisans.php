<?php
// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
//				Son Düzenleme: 26.06.2015						//
// ############################################################	//

require_once("libs/MaestroPanelLisans.php");

function maestropanellisans_ConfigOptions() {
    $configarray = array(
            "Lisans Tipi" => array("Type" => "dropdown", "Options" => "free10|Ücretsiz - 10 Domain,free30|Ücretsiz - 30 Domain,monthly|Aylık,yearly|Yıllık"), 
	);

	return $configarray;
}

function maestropanellisans_CreateAccount($params) {
    $query=mysql_query( "SELECT * FROM tblproducts WHERE servertype='maestropanellisans' AND configoption1 LIKE 'free%'" );
    $freeIds = array();
    while( $row = mysql_fetch_assoc( $query ) ){
        $freeIds[] = $row['id'];
    }

	$expconfig = explode("|", $params['configoption1']);
	$period = $expconfig[0];

    $result=mysql_fetch_assoc( mysql_query( "SELECT count(th.id) total FROM tblhosting th WHERE domainstatus='Active' AND userid=" . $_SESSION['uid'] . " AND packageid IN (" . implode(',', $freeIds)  . ")" ) );
    if( $result['total'] ){
		logModuleCall('maestropanellisans','create','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Type: ' . $period,'Failed / User has reached free license limit','','');
        return "Her müşteri yalnızca 1 adet ücretsiz MaestroPanel lisansı alabilmektedir.";
    }

    $mp = new MaestroPanelLisans();

 	$result=mysql_fetch_assoc( mysql_query( "select * from  tbladdonmodules where module='maestropanellisans' and setting='licensenameprefix' ORDER BY value DESC LIMIT 1" ) );
	$licenseName=$result["value"];
	$result = mysql_fetch_array( mysql_query( "select id,relid  from tblcustomfields where fieldname='Lisans Adı' and relid=" . $params["pid"] ) );
 	$result = mysql_fetch_array( mysql_query( "select value from tblcustomfieldsvalues where fieldid=".$result["id"] ." and relid=".$params["serviceid"] ) );
	$licenseName.=$result["value"];

    $request = $mp->create(  $period, $licenseName );

    if( !$request->errorCode ){
		$lc = $request->license->licenseCode;
        mysql_query("update tblhosting set domain='" . $lc . "' where id=" . $params[serviceid]);
        $result = mysql_fetch_array( mysql_query( "select id,relid  from tblcustomfields where fieldname='Lisans Adı' and relid=" . $params[pid] ) );
        $result = mysql_query( "update tblcustomfieldsvalues set value='" . $request->license->licenseName . "' where fieldid=".$result[id] ." and relid=".$params['serviceid'] );
		logModuleCall('maestropanellisans','create','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc . PHP_EOL . 'License Type: ' . $period,'success','','');
		$result = "success";
    }
    else{      
		logModuleCall('maestropanellisans','create','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Type: ' . $period . PHP_EOL . 'ERR MSG: ' . $result,'fail','','');
		$result = "API ERR: " . $request->Message;
    }

	return $result;

}

function maestropanellisans_TerminateAccount($params) {
    $mp = new MaestroPanelLisans();
    $lc = $params['domain'];
    $request = $mp->CancelLicense( $lc );
	
     if (!$request->errorCode){
				logModuleCall('maestropanellisans','CancelLicense','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc,'success','','');
				$result = "success";
    } else {
				logModuleCall('maestropanellisans','CancelLicense','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc . PHP_EOL . 'ERR MSG: ' . $result,'fail','','');
				$result = "API ERR: " . $request->Message;
    }
    return $result;
}

function maestropanellisans_SuspendAccount($params) {

	return "Not supported";

}

function maestropanellisans_UnsuspendAccount($params) {
    return "Not supported";

}



function maestropanellisans_ClientArea($params) {
    $mp = new MaestroPanelLisans();

    $lc = $params['domain'];

    $request = $mp->getLicense( $lc );

    $values=array();
    $values['vars']=array();

    if( $request->errorCode ){
        $values['vars']['lerror'] = $request->Message;
    }else{
        $values['vars']['lerror'] = '';
		$values['vars']['lserviceid'] = $params['serviceid'];
        $values['vars']['lc'] = $request->license->licenseCode;
        $values['vars']['ln'] = $request->license->licenseName;
        $values['vars']['expiration'] = date("d.m.Y", strtotime( $request->license->Expiration ) );
        $values['vars']['ip'] = $request->license->AssignedIp;
		if ( $request->license->AutoRenew == 'True' ){
			$values['vars']['autorenew'] = 'Aktif';
		}else {
			$values['vars']['autorenew'] = 'Pasif';
		}
		
		$statusen = $request->license->Status;
		if ($statusen == 'Active') {
			 $values['vars']['lstatus'] = 'Aktif';
		}
		elseif ($statusen == 'Passive') {
			 $values['vars']['lstatus'] = 'Pasif';
		}
		elseif ($statusen == 'Pending') {
			 $values['vars']['lstatus'] = 'Beklemede';
		}
		elseif ($statusen == 'Cancelled') {
			 $values['vars']['lstatus'] = 'İptal Edildi';
		}
		elseif ($statusen == 'Expired') {
			 $values['vars']['lstatus'] = 'Süresi Doldu';
		}
		elseif ($statusen == 'RenewPending') {
			 $values['vars']['lstatus'] = 'Yenileme Bekliyor';
		}

    }
    return $values;
}


function maestropanellisans_ClientAreaCustomButtonArray() {
    $buttonarray = array(
            'Boşa Çıkart' => 'reissue'
	);
	return $buttonarray;
}

function maestropanellisans_AdminCustomButtonArray() {
    $buttonarray = array(
            'Boşa Çıkart' => 'reissue',
			'Oto. Yenileme Aç/Kapat' => 'toggle_autorenew'
	);
	return $buttonarray;
}

function maestropanellisans_reissue( $params ){
    $mp = new MaestroPanelLisans();

    $lc = $params['domain'];

    $request = $mp->reissue( $lc );

    if( !$request->errorCode ){
        $successful=true;
    }
    else{
        $successful=false;
    }

     if ($successful){
                $result = "success";
				logModuleCall('maestropanellisans','reissue','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc,'success','','');
        } else {
                $result = "API ERR: " . $request->Message;
				logModuleCall('maestropanellisans','reissue','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc . PHP_EOL . 'ERR MSG: ' . $result,'fail','','');
        }
    return $result;
}

function maestropanellisans_toggle_autorenew( $params ){
    $mp = new MaestroPanelLisans();

    $lc = $params['domain'];
    $request = $mp->getLicense( $lc );
	
	if ( $request->license->AutoRenew == 'True' ){
		$autorenewaction="Disable";
	}else {
		$autorenewaction="Enable";
	}
	
	$request = $mp->ToggleAutoRenew( $lc , $autorenewaction);

	if (!$request->errorCode){
		logModuleCall('maestropanellisans',$autorenewaction.'AutoRenew','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc,'success','','');
		$result = "success";
	} else {
		logModuleCall('maestropanellisans',$autorenewaction.'AutoRenew','User ID: ' . $_SESSION['uid'] . PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'License Code: ' . $lc . PHP_EOL . 'ERR MSG: ' . $result,'fail','','');
		$result = "API ERR: " . $request->Message;
	}
    return $result;
}

function maestropanellisans_AdminServicesTabFields( $params ) {
    $mp = new MaestroPanelLisans();

    $lc = $params['domain'];
    $request = $mp->getLicense( $lc );
	
	if ( $request->license->AutoRenew == "True" ){
		$adminautorenewtr = "Aktif";
	}else {
		$adminautorenewtr = "Pasif";
	}
	
		if ($request->license->Status == "Active") {
			 $adminstatustr = "Aktif";
		}
		elseif ($request->license->Status == "Passive") {
			 $adminstatustr = "Pasif";
		}
		elseif ($request->license->Status == "Pending") {
			 $adminstatustr = "Beklemede";
		}
		elseif ($request->license->Status == "Cancelled") {
			 $adminstatustr = "İptal Edildi";
		}
		elseif ($request->license->Status == "Expired") {
			 $adminstatustr = "Süresi Doldu";
		}
		elseif ($request->license->Status == "RenewPending") {
			 $adminstatustr = "Yenileme Bekliyor";
		}

    if( $request->errorCode )
        return array();
    $fieldsarray = array(
        'Lisans Kodu' => $request->license->licenseCode,
        'Lisans Adı' => $request->license->licenseName,
        'Sonlanma Tarihi' => date("d.m.Y", strtotime( $request->license->Expiration ) ),
        'IP' => $request->license->AssignedIp,
		'Otomatik Yenileme' => $adminautorenewtr,
        'Durum' => $adminstatustr,
    );
    return $fieldsarray;

}
