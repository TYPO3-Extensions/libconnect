<?php
/**
 * Doku: http://www.zeitschriftendatenbank.de/services/journals-online-print/
 * Doku: http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Spezifikation_XML-Dienst.pdf
 * Doku: http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Dokumentation_XML-Dienst.pdf
 * @author André Lahmann
 *
 */

/**
 * Typoscript Settings
 *      settings.enableLocationData = 1
 *      settings.validStatesList = 2,3
 *      settings.EZBSearchResultsPageId = pageID
 *      settings.DBISSearchResultsPageId = pageID
 *      settings.useIconService = 1/0
 *      zdbsid = id:phrase
 *      zdbbibid = BIBID
 *      zdbsigel = SIGEL
 *      zdbisil = ISIL
 *      zdbbik = BIK
 *
 */
 
 
class ZDB {

   /**
    * Source-Identifier (sid – Vendor-ID:Database-ID) needs to be arranged with
    * the ZDB (contact: Mr. Rolschewski, mailto: johann.rolschewski@sbb.spk-berlin.de)
    *
    */	    
    private $sid = NULL;
    
   /**
    * library authentication parameters to display correct availability
    *
    */
    private $bibid = NULL;
    private $sigel = NULL;
    private $isil = NULL;
    private $bik = NULL;
    
   /**
    * non-open-url conform pid arguments-string
    *
    */ 
    private $pid = '';
    private $onlyPrintFlag = true;    
    
   /**
    * request URLs
    *
    */    
    //private $briefformat_request_url = "http://services.d-nb.de/fize-service/gvr/brief.xml?";
    private $fullformat_request_url = "http://services.d-nb.de/fize-service/gvr/full.xml?";    
    

    /**
	 * Class Constructor
	 *
	 */    
    function __construct() {
        $this->sid = $this->getSid();
	    if(!$this->sid) {
	        //todo: Fehlermeldung ausgeben
	        //error_log('typo3 extension libconnect - missing ZDB source-identifier: refer to documentation - chapter configuration.');
	        return false;
	    }
        
        //get the bibid
        if(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbibid'])) $this->bibid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbibid'];
        //if no explicit bibid for zdb is set, try to find the bibid which needs to setup for libconnect without zdb-support
        elseif(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'])) $this->bibid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
        
        //get the library sigel
        if(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbsigel'])) $this->sigel = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbsigel'];
        //get the isil
        if(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbisil'])) $this->isil = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbisil'];
        //get the bik
        if(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbik'])) $this->bik = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbik'];

        $this->pid = urlencode((!empty($this->bibid) ? 'bibid=' . $this->bibid .'&' : '') .
                               (!empty($this->sigel) ? 'sigel=' . $this->sigel .'&' : '') .
                               (!empty($this->isil) ? 'isil=' . $this->isil .'&' : '') .
                               (!empty($this->bik) ? 'bik=' . $this->bik : ''));
                     
        //remove last &(urlencode: %26) if existant (only if bik is empty but any other info above is given)
        if(strlen($this->pid)-3 == strrpos($this->pid, '%26')) 
            $this->pid = substr($this->pid,0,strlen($this->pid)-3);
        
        //only print location data are requested (default is off, so online and print will be delivered)
        if($onlyPrintFlag)
            $this->pid .= (strlen($this->pid) > 0 ? urlencode("&print=1") : urlencode("print=1"));
        
    }
  
    
    
    /**
	 * Standortdetails zu einem Journal laden
	 *
	 * @param JournalIdentifier string
	 * @param ZDBID string
	 *
	 * @return string
	 */
	public function getJournalLocationDetails($JournalIdentifier, $ZDBID, $genre = 'journal'){
	   /**
	    * to identify a journal either the ISSN or the eISSN is mandatory as primary key
	    * alternatively the ZDB-ID is allowed but does not conform with Open-URL-Standard
	    * - JournalIdentifier is a complete Get-Parameter-String
	    *    e.g. "issn=1234567-4"
	    * - ZDBID is a string of a ZDB-ID
	    *
	    */
	    if(empty($JournalIdentifier) && empty($ZDBID))
	        return false;
	    else {
	        if(!empty($ZDBID)) {
	            $this->pid .= (strlen($this->pid) > 0 ? urlencode("&zdbid={$ZDBID}") : urlencode("zdbid={$ZDBID}"));  
	        } 
	        if(!empty($JournalIdentifier)) {
	            $JournalIdentifier = "&{$JournalIdentifier}";
	        }
	    }

		$url = "{$this->fullformat_request_url}sid={$this->sid}" . (!empty($this->pid) ? "&pid={$this->pid}" : "" ) . $JournalIdentifier . "&genre={$genre}";

		$xml_request = simplexml_load_file( $url );
		
		$locationDetail = array();

		// root-element = OpenURLResponseXML->Full/Brief
		// only Full-objects got all the info we want
		if (! is_object($xml_request->Full))
		   /**
		    * possible Error-Codes:
		    *     Code        Meaning
		    *    -----------------------------------------------
		    *     m-issn      ISSN fehlt!
		    *     genre       Genre nicht journal oder article!
		    *     f-issn      ISSN mit falschen Format!
		    *     unknown     Unbekannter Fehler
		    *
		    */
			return false;

			
		$locationDetail['library'] = (string) $xml_request->Full->PrintData->Library;			
			
		
	   /**
	    * two branches: <ElectronicData> : electronic availability
	    *               <PrintData> : print 
	    *
	    * as only the location of the printed journal is of any concern, only the
	    * branch PrintData is regarded
	    *
	    * states
	    * ------
	    * -1, 10 = Error: ZDB-ID, ISSN, Sigel or ISBN unknown or not unique
	    * 2 = available
	    * 3 = limited availability (moving wall, etc.)
	    * 4 = journal not available
	    *
	    * check for any valid (for display) state - default is 2 and 3 (custom
	    * configuration in typoscript):
	    *     tx_libconnect.validStatesList = 1,2,3,4,5,6
	    */
	    $validStatesArray = (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['validStatesList']) && !empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['validStatesList']) ?
	                         explode(',', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['validStatesList']) :
	                         array(2,3)
	                        );
	    array_walk($validStatesArray, create_function('&$val', '$val = trim($val);')); //remove all whitespaces from states 
	    $tmpStates = $xml_request->Full->PrintData->ResultList->children();
	    $validStateFlag = false;
	    if(count($tmpStates)) {
	        foreach($tmpStates as $tmpState) {
	            if(in_array($tmpState->attributes()->state, $validStatesArray))
	                $validStateFlag = true;
	        }
	    }
	    //no valid state found -> exit
	    if(!$validStateFlag)
	        return false;	    


       /**
		* Result
		* --------
		*     Attributes:     (integer)    state
		*     Children:       (string)     Title
		*                     (string)     Location
		*                     (string)     Signature
		*                     (string)     Period
		*
		* subelement Additionals is not considered as it's not used for PrintData yet
		*
		*/
		$tmpResultList = $xml_request->Full->PrintData->ResultList->children();
		$locationDetail['resultList'] = array();
		if(count($tmpResultList)) {
		    foreach($tmpResultList as $tmpResult) {
		        if(in_array($tmpResult->attributes()->state, $validStatesArray)) {
                    array_push($locationDetail['resultList'],
                               array('state'     => (int) $tmpResult->attributes()->state,
                                     'Title'     => (string) $tmpResult->Title,
                                     'Location'  => (string) $tmpResult->Location,
                                     'Signature' => (string) $tmpResult->Signature,
                                     'Period'    => (string) $tmpResult->Period)
                               );
                }
		    }
		}
		
		
	   /**
		* Reference
		* ---------
		*     Children:       (string)     URL
		*                     (string)     Label
		*/
		$tmpReferences = $xml_request->Full->PrintData->References->children();
		$locationDetail['references'] = array();
		if(count($tmpReferences)) {
		    foreach($tmpReferences as $tmpReference) {
		        array_push($locationDetail['references'],
		                   array('URL'   => (string) $tmpReference->URL,
		                         'Label' => (string) $tmpReference->Label)
		                   );
		    }
		}
		
	   /**
		* Icon-Service
		* ------------
		*     
		*/
		$locationDetail['iconRequest'] = $this->buildIconRequest($JournalIdentifier, $genre);
		$locationDetail['iconInfoUrl'] = $this->buildIconInfoUrl($JournalIdentifier, $genre);
		
		
		return $locationDetail;
	    
	}
    
	
    /**
	 * helper function build ICON-service request
	 * (http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Dokumentation_Icon-Dienst.pdf)
	 *
	 * @return string
	 */
	private function buildIconRequest($journalIdentifier, $genre){
		
		return "http://services.d-nb.de/fize-service/gvr/icon?sid={$this->getSid()}" . (!empty($this->pid) ? "&pid={$this->pid}" : "" ) . $JournalIdentifier . "&genre={$genre}";
	}
	

    /**
	 * helper function build ICON-info url
	 * (http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Dokumentation_Icon-Dienst.pdf)
	 *
	 * @return string
	 */
	private function buildIconInfoUrl($journalIdentifier, $genre){
		
		return "http://services.d-nb.de/fize-service/gvr/html-service.htm?sid={$this->getSid()}" . (!empty($this->pid) ? "&pid={$this->pid}" : "" ) . $JournalIdentifier . "&genre={$genre}";
	}
	
	
	/**
	 * helper function get SID
	 *
	 * @return string
	 */
	private function getSid(){
		$sid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbsid'];

		if(is_null($sid) or !$sid or empty($sid)){	
			return false;
		}
		
		return $sid;
	}
	
}

?>
