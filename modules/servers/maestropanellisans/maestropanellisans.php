<?php
// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
// ############################################################	//

require_once("libs/MaestroPanelLisans.php");

function maestropanellisans_ConfigOptions() {

    $configarray = array(
            "Lisans Tipi" => array("Type" => "dropdown", "Options" => "Ücretsiz - 10 Domain,Ücretsiz - 30 Domain,Aylık,Yıllık"),        
	);

	return $configarray;

}

function maestropanellisans_CreateAccount($params) {
//var_dump($params);die();

    $sql = "SELECT * FROM tblproducts WHERE servertype='maestropanellisans' AND configoption1 LIKE 'Free%'";
    $query=mysql_query( $sql );
    $freeIds = array();
    while( $row = mysql_fetch_assoc( $query ) ){
        $freeIds[] = $row['id'];
    }


    $sql = "SELECT count(th.id) total FROM tblhosting th   WHERE domainstatus='Active' AND packageid IN (" . implode(',', $freeIds)  . ")" ;
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    if( $result['total'] ){
        return "Her müşteri yalnızca 1 adet ücretsiz MaestroPanel lisansı alabilmektedir.";
    }

    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='apikey' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $apikey=$result['value'];
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='testmode' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $testMode=$result['value'];
    $mp = new MaestroPanelLisans( $apikey, $testMode );

 	$query = "select id,relid  from tblcustomfields where fieldname='Lisans Adı' and relid=" . $params[pid];
 	$result = mysql_fetch_array( mysql_query( $query ) );
 	$query= "select value from tblcustomfieldsvalues where fieldid=".$result[id] ." and relid=".$params['serviceid'];
	$result = mysql_fetch_array( mysql_query( $query ) );
	$licenseName = $result[value];
	
//    $period = strtolower($params['configoption1']);

   if ($params['configoption1'] = 'Ücretsiz - 10 Domain') {
		$period = 'free10';
	} elseif ($params['configoption1'] = 'Ücretsiz - 30 Domain') {
		$period = 'free30';
	} elseif ($params['configoption1'] = 'Aylık') {
		$period = 'monthly';
	}  elseif ($params['configoption1'] = 'Yıllık') {
		$period = 'yearly';
	} 

    $request = $mp->create(  $period, $licenseName );

    if( !$request->errorCode ){
        $successful=true;
		$lc = $request->license->licenseCode;
        $query = "update tblhosting set domain='" . $lc . "' where id=" . $params[serviceid];
        mysql_query($query);

        $query = "select id,relid  from tblcustomfields where fieldname='Lisans Adı' and relid=" . $params[pid];
        $result = mysql_fetch_array( mysql_query( $query ) );
        $query= "update tblcustomfieldsvalues set value='" . $request->license->licenseName . "' where fieldid=".$result[id] ." and relid=".$params['serviceid'];
        
        $result = mysql_query( $query );
    }
    else{
        $successful=false;
    }


	if ($successful){
		$result = "success";
	} else {
		$result = $request->Message;
	}
	return $result;

}

function maestropanellisans_TerminateAccount($params) {

	return "Not supported";
}

function maestropanellisans_SuspendAccount($params) {

	return "Not supported";

}

function maestropanellisans_UnsuspendAccount($params) {
    return "Not supported";

}



function maestropanellisans_ClientArea($params) {
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='apikey' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $apikey=$result['value'];
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='testmode' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $testMode=$result['value'];
    $mp = new MaestroPanelLisans( $apikey, $testMode );

    $lc = $params['domain'];

    $request = $mp->getLicense( $lc );

    $values=array();
    $values['vars']=array();

    if( $request->errorCode ){
        $values['vars']['lerror'] = $request->Message;
    }else{
        $values['vars']['lerror'] = '';
        $values['vars']['lc'] = $request->license->licenseCode;
        $values['vars']['ln'] = $request->license->licenseName;
        $values['vars']['expiration'] = date("d.m.Y", strtotime( $request->license->Expiration ) );
        $values['vars']['ip'] = $request->license->AssignedIp;
		$statusen = $request->license->Status;
		if ($statusen = 'Active') {
			 $values['vars']['lstatus'] = 'Aktif';
		}
		elseif ($statusen = 'Passive') {
			 $values['vars']['lstatus'] = 'Pasif';
		}
		elseif ($statusen = 'Pending') {
			 $values['vars']['lstatus'] = 'Beklemede';
		}
		elseif ($statusen = 'Cancelled') {
			 $values['vars']['lstatus'] = 'İptal Edildi';
		}
		elseif ($statusen = 'Expired') {
			 $values['vars']['lstatus'] = 'Süresi Doldu';
		}
		elseif ($statusen = 'RenewPending') {
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
            'Boşa Çıkart' => 'reissue'
	 
	);
	return $buttonarray;
}

function maestropanellisans_reissue( $params ){
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='apikey' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $apikey=$result['value'];
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='testmode' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $testMode=$result['value'];
    $mp = new MaestroPanelLisans( $apikey, $testMode );

    $lc = $params['domain'];

    $request = $mp->reissue( $lc );

    if( !$request->errorCode ){
        logModuleCall('maestropanellisans','reissue','','success','','');
        $successful=true;
    }
    else{
        $successful=false;
    }

     if ($successful){
                $result = "success";
        } else {
                logModuleCall('maestropanellisans','reissue','',$request->Message,'','');
                $result = $request->Message;
        }
        return $result;

}

function maestropanellisans_AdminServicesTabFields( $params ) {
    
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='apikey' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $apikey=$result['value'];
    $sql="select * from  tbladdonmodules where module='maestropanellisans' and setting='testmode' ORDER BY value DESC LIMIT 1";
    $query=mysql_query( $sql );
    $result=mysql_fetch_assoc( $query );
    $testMode=$result['value'];
    $mp = new MaestroPanelLisans( $apikey, $testMode );

    $lc = $params['domain'];

    $request = $mp->getLicense( $lc );
	$adminstatusen = $request->license->Status;
		if ($adminstatusen = 'Active') {
			 $adminstatustr = 'Aktif';
		}
		elseif ($adminstatusen = 'Passive') {
			 $adminstatustr = 'Pasif';
		}
		elseif ($adminstatusen = 'Pending') {
			 $adminstatustr = 'Beklemede';
		}
		elseif ($adminstatusen = 'Cancelled') {
			 $adminstatustr = 'İptal Edildi';
		}
		elseif ($adminstatusen = 'Expired') {
			 $adminstatustr = 'Süresi Doldu';
		}
		elseif ($adminstatusen = 'RenewPending') {
			 $adminstatustr = 'Yenileme Bekliyor';
		}

    if( $request->errorCode )
        return array();
    $fieldsarray = array(
        'Lisans Kodu' => $request->license->licenseCode,
        'Lisans Adı' => $request->license->licenseName,
        'Sonlanma Tarihi' => date("d.m.Y", strtotime( $request->license->Expiration ) ),
        'IP' => $request->license->AssignedIp,
        'Durum' => $adminstatustr,
    );
    return $fieldsarray;

}
