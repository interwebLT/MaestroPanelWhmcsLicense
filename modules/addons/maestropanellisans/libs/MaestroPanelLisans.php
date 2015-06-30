<?php
// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
//				Son Düzenleme: 26.06.2014						//
// ############################################################	//

class MaestroPanelLisans {

    private $host;
    private $apiKey;
	
	public function __construct( ) {
		$result=mysql_fetch_assoc( mysql_query( "select * from  tbladdonmodules where module='maestropanellisans' and setting='apikey' ORDER BY value DESC LIMIT 1" ) );
        $this->apiKey = $result['value'];
		$result=mysql_fetch_assoc( mysql_query( "select * from  tbladdonmodules where module='maestropanellisans' and setting='testmode' ORDER BY value DESC LIMIT 1" ) );
		$testmode=$result['value'];
        if ( $testmode )
            $this->host = "http://sandbox.maestropanel.net/Api/v1/License/";
        else
            $this->host = "https://secure.maestropanel.com/Api/v1/License/";
    }
    private function parseJson( $json ) {
        $data = json_decode( $json );
        return $data;
    }

    public function create( $period, $ln, $licenseip ) {
        $command = $this->host . "New?key=" . $this->apiKey  . "&period=" . $period . "&licenseName=" . urlencode( $ln ) . "&ipaddr=" . $licenseip;
        return $this->sendRequest( $command );

    }

    public function reissue( $lc ) {
        $command = $this->host . "ReIssue?key=" . $this->apiKey  . "&licenseCode=" . $lc;
        return $this->sendRequest( $command );
    }

    public function getLicense( $lc ) {
        $command = $this->host . "Show?key=" . $this->apiKey  . "&licenseCode=" . $lc;
        return $this->sendRequest( $command, 'GET' );
    }
	
	public function ListLicenses( $query ) {
        $command = $this->host . "List?key=" . $this->apiKey  . "&query=" . $query;
        return $this->sendRequest( $command, 'GET' );
    }
	
	public function CancelLicense( $lc ) {
        $command = $this->host . "Cancel?key=" . $this->apiKey  . "&licenseCode=" . $lc;
        return $this->sendRequest( $command );
    }
	
	public function ToggleAutoRenew( $lc, $action ) {
        $command = $this->host . $action . "AutoRenew?key=" . $this->apiKey  . "&licenseCode=" . $lc;
        return $this->sendRequest( $command );
    }
	
    private function sendRequest( $command, $method = 'POST' ) {
        $command .= "&format=json";
        $ch = curl_init( $command );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        if ( $method == 'POST' )
            curl_setopt( $ch, CURLOPT_POSTFIELDS, 1 );
        $request = curl_exec( $ch );
        return $this->parseJson( $request );
    }
}
