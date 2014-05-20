<?php
// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
// ############################################################	//

class MaestroPanelLisans {

    private $host;
    private $apiKey;

    public function __construct( $apikey, $testmode = false ) {
        $this->apiKey = $apikey;
        if ( $testmode )
            $this->host = "http://sandbox.maestropanel.net/Api/v1/License/";
        else
            $this->host = "https://secure.maestropanel.com/Api/v1/License/";
    }
    private function parseJson( $json ) {
        $data = json_decode( $json );
        return $data;
    }

    public function create( $period, $ln ) {
        $command = $this->host . "New?key=" . $this->apiKey  . "&period=" . $period . "&licenseName=" . urlencode( $ln );
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
