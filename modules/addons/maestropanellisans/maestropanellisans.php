<?php
// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
// ############################################################	//


if ( !defined( "WHMCS" ) )
    die( "Bu dosyaya doğrudan erişim izni bulunmamaktadır." );

function maestropanellisans_config() {
    $configarray = array(
        "name" => "MaestroPanelLisans",
        "description" => "MaestroPanel - WHMCS Lisans Otomasyonu",
        "version" => "1.0",
        "author" => "bilrom.com",
        "language" => "turkish",
        "fields" => array(
            "apikey" => array ( "FriendlyName" => "API Key", "Type" => "password", "Size" => "50" ),
            "testmode" => array ( "FriendlyName" => "Test Modu", "Type" => "yesno", "Size" => "25" ),
        ) );
    return $configarray;
}

function maestropanellisans_activate() {

}

function maestropanellisans_deactivate() {
}

function maestropanellisans_upgrade( $vars ) {
}

function maestropanellisans_output( $vars ) {
}
