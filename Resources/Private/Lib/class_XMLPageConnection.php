<?php

/**
 * Class 'XMLPageConnection' for the 'libconnect' extension.
 *
 * @author	Björn Heinermann <hein@zhaw.ch>
 * @package	TYPO3
 * @subpackage	tx_libconnect
 */

class XMLPageConnection {
    
    private $extPiVars;
    private $proxy;
    private $proxyPort;
    
    public function __construct() {
	
		$this->setExtPiVars();
		$this->setProxy();
		$this->setProxyPort();
    }
    
    /**
     * Läd die im typoscript gesetzten Variablen.
     */
    private function setExtPiVars() {
		$this->extPiVars = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.'];
    }
    
    /**
     * Setzt den Proxy Server 
     */
    private function setProxy() {
		$this->proxy = $this->extPiVars['proxy'];
    }
    
    /**
     * Setzt den Proxy Port 
     */
    private function setProxyPort() {
		$this->proxyPort = $this->extPiVars['proxy_port'];
    }
    
    /**
     * Holt XML Daten von einer Internetseite und gibt diese als array zurück
     *
     * @return SimpleXMLElement object
     */
    public function getDataFromXMLPage($url) {

		$xmlObj = FALSE;
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_PORT, 80);
		if ($this->proxy && $this->proxyPort) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		
		$result = curl_exec($ch);
		$xmlObj = simplexml_load_string($result);
		
		
		//Pruefung ob Abfrage fehlerfrei erfolgte
		$http_code = curl_getinfo($ch);
		curl_close($ch);//Session schliessen
		
		if($http_code['http_code']!=200){
			$xmlObj = FALSE;
			
			return $xmlObj;
		}
		
		
		/* HINWEIS FEHLERPRUEFUNG: 
		 * Die Funktion curl_error() wie auch curl_getinfo() haben keine brauchbaren Rückgabewerte
		 * zurückgegeben. Deswegen die Pruefung ob ein Object erzeugt wurde und es ein
		 * SimpleXMLElement ist. 
		 */
		if (!is_object($xmlObj) && get_class($xmlObj) == 'SimpleXMLElement') {
			$xmlObj = FALSE;
		}

		return $xmlObj;
    }
}
?>