<?php
/***************************************************************
* Copyright notice
*
* (c) 2009 by Avonis - New Media Agency
*
* All rights reserved
*
* This script is part of the EZB/DBIS-Extention project. The EZB/DBIS-Extention project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
* Project sponsored by:
*  Avonis - New Media Agency - http://www.avonis.com/
***************************************************************/

/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
 
require_once(t3lib_extMgm::extPath('libconnect') . 'Resources/Private/Lib/class_EZB.php');
require_once(t3lib_extMgm::extPath('libconnect') . 'Resources/Private/Lib/class_ZDB.php');

Class Tx_Libconnect_Domain_Repository_EzbRepository extends Tx_Extbase_Persistence_Repository {
	private $ezb_to_t3_subjects = array();
	private $t3_to_ezb_subjects = array();
	
	private $longAccessInfos = array();
	
	public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
		$this->subjectRepository = $subjectRepository;
	}
	
	public function loadOverview() {
		$this->loadSubjects();
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		

		$ezb = new EZB();
		$fb = $ezb->getFachbereiche();

		foreach($fb as $el) {
			$subject = $this->ezb_to_t3_subjects[$el['id']];
			$el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
				'libconnect[subject]' => $subject['uid']
			));
			$list[$el['id']] = $el;
		}
		
		return $list;
	}
	
	private function loadSubjects() {
		$res = $this->subjectRepository->findAll();
		
		foreach($res as $row){			
			$this->ezb_to_t3_subjects[$row->getEzbnotation()]['ezbnotation'] = $row->getEzbnotation();
			$this->ezb_to_t3_subjects[$row->getEzbnotation()]['title'] = $row->getTitle();
			$this->ezb_to_t3_subjects[$row->getEzbnotation()]['uid'] = $row->getUid();
			
			$this->t3_to_ezb_subjects[$row->getUid()]['uid'] = $row->getUid();
			$this->t3_to_ezb_subjects[$row->getUid()]['ezbnotation'] = $row->getEzbnotation();
			$this->t3_to_ezb_subjects[$row->getUid()]['title'] = $row->getTitle();
		}
	}
	
	public function loadList($subject_id, $options =array('index' =>0, 'sc' => 'A', 'lc' => ''), $config) {
		$index = $options['index'];
		$sc = $options['sc'];
		$lc = $options['lc'];
		//$index=0, $sc='A', $lc =''
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$this->loadSubjects();
		
		//Notation für Fach holen
		$subject = $this->t3_to_ezb_subjects[$subject_id];

		$ezb = new EZB();
		if($options['notation'] == 'All'){
			$subject['ezbnotation'] = 'All';
		}
		//$ezb->notation = $options['notation'];
		$journals = $ezb->getFachbereichJournals($subject['ezbnotation'], $index, $sc, $lc);
		
		//Zugriffsinformationen holen
		$journals['selected_colors'] = $this->getAccessInfos();
		
		
		/**
		 * Links bauen
		 */
		//Navigation
		foreach(array_keys($journals['navlist']['pages']) as $page) {
			if (is_array($journals['navlist']['pages'][$page])) {
				$journals['navlist']['pages'][$page]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
					'libconnect[subject]' => $subject['uid'],
					'libconnect[index]' => 0,
				    'libconnect[sc]' => $journals['navlist']['pages'][$page]['sc']? $journals['navlist']['pages'][$page]['sc'] : 'A',
					'libconnect[lc]' => $journals['navlist']['pages'][$page]['lc'],
					'libconnect[notation]' => $subject['ezbnotation']
				));
			}
		}
		
		//Ergenisse
		if(isset($journals['alphabetical_order']['first_fifty'])){
			foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
				$journals['alphabetical_order']['first_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
						'libconnect[subject]' => $subject['uid'],
						'libconnect[index]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
					    'libconnect[sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc']? $journals['alphabetical_order']['first_fifty'][$section]['sc'] : 'A',
						'libconnect[lc]' => $journals['alphabetical_order']['first_fifty'][$section]['lc'],
						'libconnect[notation]' => $subject['ezbnotation']
				));
			}
		}
		if(isset($journals['alphabetical_order']['journals'])){
            foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
                $journals['alphabetical_order']['journals'][$journal]['detail_link'] = $cObject->getTypolink_URL(
                        intval($config['detailPid']),
                        array(
                            'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid']
                        )
                );
            }		    
		}
		if(isset($journals['alphabetical_order']['next_fifty'])){
			foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
				$journals['alphabetical_order']['next_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
						'libconnect[subject]' => $subject['uid'],
						'libconnect[index]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
					    'libconnect[sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc']? $journals['alphabetical_order']['next_fifty'][$section]['sc'] : 'A',
						'libconnect[lc]' => $journals['alphabetical_order']['next_fifty'][$section]['lc'],
						'libconnect[notation]' => $subject['ezbnotation']
				));
			}
		}

		return $journals;
	}
	
	public function loadDetail($journal_id) {
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$ezb = new EZB();
		$journal = $ezb->getJournalDetail($journal_id);

		if (! $journal ){
			return false;
		}
		
		/*BEGIN Zugriffsinformationen holen*/
		
		//Standardtexte holen
		$LongAccessInfos = $ezb->getLongAccessInfos();
		
		$colortext = array();
		if((!empty($LongAccessInfos['longAccessInfos'])) && ($LongAccessInfos['longAccessInfos']!= false)){
			foreach($LongAccessInfos as $key =>$text){
				 $colortext[$key] = $text;
			}
		}
		
		//Texte aus dem Web holen
		$form = $ezb->detailSearchFormFields();
		$journal['selected_colors'] = $form['selected_colors'];

		$color = $journal['color_code'];//Farbangabe
		unset($journal['color_code']);
		$journal['color_code'] = array();

		if((!isset($journal['selected_colors'][$color])) or (empty($journal['selected_colors'][$color])) or ($LongAccessInfos['force'] == 'true')){
			$journal['color_code']['text'] = $colortext['longAccessInfos'][$color];
		}else{
			$journal['color_code']['text'] = $journal['selected_colors'][$color];
		}
		$journal['color_code']['color'] = $color;
		/*END Zugriffsinformationen holen*/
		
		return $journal; 
	}
	
	/**
	 * Suche vorbereiten
	 * 
	 * @param array $searchVars
	 * @param array $config
	 * @return array $journals
	 */
	public function loadSearch($searchVars, $config) {
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$this->loadSubjects();

		//Suche von Sidebar
		if (strlen($searchVars['sword'])) {
			$searchVars['jq_type1'] = 'KT';
			$searchVars['jq_term1'] = $searchVars['sword'];
		}
		unset($searchVars['sword']);//in weiterer Verarbeitung nicht sinnvoll
		

		$linkParams = array();
		foreach ($searchVars as $key => $value) {
			$linkParams["libconnect[search][$key]"] = $value;
		}
		
		
		$ezb = new EZB();
		$journals = $ezb->search($term, $searchVars);
		
		if (! $journals){
			return false;
		}
		
		$journals['searchDescription'] = $this->getSearchDescription($searchVars);
		
		/**
		 * Links bauen
		 */
		//Navigation
		if (is_array($journals['navlist']['pages'])) {

			foreach(array_keys($journals['navlist']['pages']) as $page) {
				if (is_array($journals['navlist']['pages'][$page])) {
					$journals['navlist']['pages'][$page]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id,
						array_merge($linkParams, array(
							'libconnect[search][sc]' => $journals['navlist']['pages'][$page]['id']
						)));
				}
			}
		}
		
		//Ergenisse
		if (is_array($journals['alphabetical_order']['first_fifty'])) {

			foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
				$journals['alphabetical_order']['first_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id,
					array_merge($linkParams, array(
						'libconnect[search][sindex]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
					    'libconnect[search][sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc'],
					)));
			}
		}

		if (is_array($journals['alphabetical_order']['journals'])) {

			foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
				$journals['alphabetical_order']['journals'][$journal]['detail_link'] = $cObject->getTypolink_URL(
						intval($config['detailPid']),
						array(
							'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid'],
						)
				);
			}
		}

		if (is_array($journals['alphabetical_order']['next_fifty'])) {

			foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
				$journals['alphabetical_order']['next_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id,
					array_merge($linkParams, array(
						'libconnect[search][sindex]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
					    'libconnect[search][sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc'],
					)));
			}
		}
		
		//Zugriffsinformationen holen
		$journals['selected_colors'] = $this->getAccessInfos();
		
		
		
		return $journals;
	}

	public function loadMiniForm() {

		$ezb = new EZB();
		$form = $ezb->detailSearchFormFields();
		
		return $form;
	}

	public function loadForm() {
		$ezb = new EZB();
		
		/*BEGIN Zugriffsinformationen holen*/
		
		//Standardtexte holen
		$LongAccessInfos = $ezb->getLongAccessInfos();
		
		$colortext = array();
		if((!empty($LongAccessInfos['longAccessInfos'])) && ($LongAccessInfos['longAccessInfos']!= false)){
			foreach($LongAccessInfos as $key =>$text){
				 $colortext[$key] = $text;
			}
		}
		
		//Texte aus dem Web holen
		$form = $ezb->detailSearchFormFields();
		$journal['selected_colors'] = $form['selected_colors'];

		if((!isset($journal['selected_colors'])) or (empty($journal['selected_colors'])) or ($LongAccessInfos['force'] == 'true')){
			$form['selected_colors'] = $colortext['longAccessInfos'];
		}else{
			$form['selected_colors'] = $journal['selected_colors'];
		}
		
		//falls eine k�rzere Form erw�nscht ist
		$ShortAccessInfos = $ezb->getShortAccessInfos();
		if((!empty($ShortAccessInfos)) && ($ShortAccessInfos!= false)){
			foreach($ShortAccessInfos['shortAccessInfos'] as $key =>$text){
				 $form['selected_colors'][$key] = $text;
			}
		}
		
		unset($form['selected_colors'][6]);
		/*END Zugriffsinformationen holen*/
	
		return $form;
	}
	
	/**
	 * holt BibID
	 * 
	 * @return string
	 */
	public function getBibid(){
		return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
	}
	
//BOF ZDB LocationData
	/**
	 * Standortinformationen für Druckausgaben von Zeitschriften
	 */
	public function loadLocationData($journal) {
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$zdb = new ZDB();

		if(!empty($journal['ZDB_number']))
		    $locationData = $zdb->getJournalLocationDetails( NULL, $journal['ZDB_number']);
		else {
		    if(count($journal['pissns']))
		        $locationData = $zdb->getJournalLocationDetails( "issn=" . reset($journal['pissns']), NULL );
		    
		    elseif(count($journal['eissns']))
		        $locationData = $zdb->getJournalLocationDetails( "eissn=" . reset($journal['eissns']), NULL );
		}

		if (! $locationData ){
			return false;
		}
	
		return $locationData; 
	}
//EOF ZDB LocationData


	public function setLongAccessInfos($longAccessInfos) {
		$this->longAccessInfos = $longAccessInfos;
    }
	
	public function getLongAccessInfos(){
		return $this->longAccessInfos;
	}
	
	public function getAccessInfos(){
		$ezb = new EZB();
		
		//Standardtexte holen
		$LongAccessInfos = $ezb->getLongAccessInfos();
		
		$colortext = array();
		if((!empty($LongAccessInfos['longAccessInfos'])) && ($LongAccessInfos['longAccessInfos']!= false)){
			foreach($LongAccessInfos as $key =>$text){
				 $colortext[$key] = $text;
			}
		}
		
		//Texte aus dem Web holen
		$form = $ezb->detailSearchFormFields();
		$journal['selected_colors'] = $form['selected_colors'];

		if((!isset($journal['selected_colors'])) or (empty($journal['selected_colors'])) or ($LongAccessInfos['force'] == 'true')){
			$journals['selected_colors'] = $colortext['longAccessInfos'];
		}else{
			$journals['selected_colors'] = $journal['selected_colors'];
			//Falls Lizenzinformationen fehlen
			foreach($colortext['longAccessInfos'] as $key =>$text){
				 if(!isset($journals['selected_colors'][$key])){
					$journals['selected_colors'][$key] = $text;
				 }
			} 
		}
		
		return $journals['selected_colors'];
	}
	
	private function getSearchDescription($searchVars){
		$list = array();
		$ezb = new EZB();
		
		//Sucbbegriffe und deren Kategorien
		$jq = "";
		
		for($i=1;$i<=4;$i++){
			if((!empty($searchVars['jq_type'.$i])) && (!empty($searchVars['jq_term'.$i]))){
					
				$jq.=$ezb->jq_type[$searchVars['jq_type'.$i]]. ' "'. $searchVars['jq_term'.$i].'" ';
				
				if(!empty($searchVars['jq_type2'])){
					$jq.= ' '.$searchVars['jq_bool'.$i].' ';
				}
			}
		}
		if(!empty($jq)){
			$list = array(1 =>$jq);
		}

		//Fächer
		if(!empty($searchVars['Notations'])){
			foreach($searchVars['Notations'] as $notation){
				if((!empty($this->ezb_to_t3_subjects[$notation])) && ($notation != '-')){
					$list[] = $this->ezb_to_t3_subjects[$notation]['title'];
				}
			}	
			
		}
	    
	    //Lizenzen
	    if(!empty($searchVars['selected_colors'])){
		    $accessInfos = $this-> getAccessInfos();
			foreach($searchVars['selected_colors'] as $key=>$color){
				if($accessInfos[$color]){
					$list[]=$accessInfos[$color];
				}
			}
		}
		
		return $list;
	}
}
?>