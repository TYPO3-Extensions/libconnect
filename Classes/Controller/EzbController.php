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
 
require_once(t3lib_extMgm::extPath('libconnect') . 'Classes/UserFunctions/IsfirstPlugInUserFunction.php');

class Tx_Libconnect_Controller_EzbController extends Tx_Extbase_MVC_Controller_ActionController {

	 /**
	 * Zeigt eine Liste von Datenbanken (für allgemein, Suche und das gewählte Fach)
	 */
	public function displayListAction() {	
		$params = t3lib_div::_GET('libconnect');
		
		//CSS includieren
		$this->decideIncludeCSS();
		
		if ((!empty($params['subject'])) || (!empty($params['notation']))) {//Gewaehltes Fach nach Einstiegspunkt
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			$options['index'] = $params['index'];
			$options['sc'] = $params['sc'];
			$options['lc'] = $params['lc'];
			$options['notation'] = $params['notation'];
			
			//damit nicht NULL in der Abfrage steht oder sonst es zu Problemen kommt
			if(!isset($params['subject'])){
				$params['subject'] = "";
			}
			$liste =  $this->ezbRepository->loadList(
				$params['subject'], 
				$options,
				$config
			);

			//Variable Template übergeben
			$this->view->assign('journals', $liste);
				
		} else if (!empty($params['search'])) {//Suchergebnisse
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			$journals =  $this->ezbRepository->loadSearch($params['search'], $config);
			
			//andere View verwenden
			$controllerContext = $this->buildControllerContext();
			$controllerContext->getRequest()->setControllerActionName('displaySearch');
			$this->view->setControllerContext($controllerContext);
			
			//Variable Template übergeben
			$this->view->assign('journals', $journals);
		} else {//Einstiegspunkt
			$liste =  $this->ezbRepository->loadOverview();
			
			//andere View verwenden
			$controllerContext = $this->buildControllerContext();
			$controllerContext->getRequest()->setControllerActionName('displayOverview');
			$this->view->setControllerContext($controllerContext);
			
			//Variable Template übergeben
			$this->view->assign('list', $liste);
		}
	}
	
	public function injectEzbRepository(Tx_Libconnect_Domain_Repository_EzbRepository $ezbRepository){
		$this->ezbRepository = $ezbRepository;
	}
	
	public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
		$this->subjectRepository = $subjectRepository;
	}
	
	/**
	 * Zeigt die Detailansicht
	 */
	public function displayDetailAction() {
		$params = t3lib_div::_GET('libconnect');
		
		//CSS includieren
		$this->decideIncludeCSS();

		//$this->set('bibid', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid']);
		if (!($params['jourid'])){
			$this->view->assign('error', 'Error');
			//return "<strong>Fehler: Es wurde keine Zeitschrift mit der angegeben URL gefunden.</strong>";
			return;
		}
		
		//$this->ezbRepository->setLongAccessInfos($this->ezblongaccessinfos->de);

		$journal =  $this->ezbRepository->loadDetail($params['jourid']);
		
    //BOF ZDB LocationData
		//check if locationData is enabled
		if($this->settings['enableLocationData'] == 1) {
            $locationData = $this->ezbRepository->loadLocationData($journal);
            
            if($locationData) {
                $journal['locationData'] = $locationData;
			}

		}
    //EOF ZDB LocationData
		
		//Variablen Template übergeben
		$this->view->assign('journal', $journal);
		$this->view->assign('bibid', $this->ezbRepository->getBibid());
	}
	
	/**
	 * zeigt die Sidebar
	 */
	public function displayMiniFormAction() {
		$params = t3lib_div::_GET('libconnect');
		
		//CSS includieren
		$this->decideIncludeCSS();
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
    	$form = $this->ezbRepository->loadMiniForm();
		
		//Variablen Template übergeben	
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('searchUrl', $cObject->getTypolink_URL($this->settings['flexform']['searchPid']));//Link zur Suchseite
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
	    $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID der Listendarstellung
	    
    	//Wenn Fach gewählt, soll Link zur Fachübersicht dargestellt werden
		if ((!empty($params['subject'])) || (!empty($params['notation']))) {
			$this->view->assign('showSubjectLink', true);
			
			//Wenn New aktiviert soll hier auch das Neu im Fach aktiviert werden
			if(!empty($this->settings['flexform']['newPid'])){
					
				$cObject = t3lib_div::makeInstance('tslib_cObj');
				
				if(!empty($params['subject'])){
					$this->view->assign('newUrlSub', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
						array('libconnect' => array('subject' => $params['subject'] )) ) );//URL der New-Darstellung
				}
			}
				
		}elseif(!empty($this->settings['flexform']['newPid'])){
			$this->view->assign('newUrl', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
		}
	}
	
	/**
	 * zeigt die Suche
	 */
	public function displayFormAction() {
		$params = t3lib_div::_GET('libconnect');
		
		//CSS includieren
		$this->decideIncludeCSS();
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$form =  $this->ezbRepository->loadForm();
		
		//Variablen Template übergeben
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
		$this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID der Listendarstellung
	}
	
	/**
	 * zeigt die neuesten Einträge
	 */
	public function displayNewAction() {
		$params = t3lib_div::_GET('libconnect');
		$params['jq_type1'] = 'ID';
		$params['sc'] = $params['search']['sc'];
		if(!empty($params['subject'])){
			$subject = $this->ezbRepository->getSubject($params['subject']);
			$params['Notations']=array($subject['ezbnotation']);
		}
		unset($params['subject']);
		unset($params['search']);
		
		//CSS einbinden
		$this->decideIncludeCSS();
		
		date_default_timezone_set('GMT+1');//@todo aus dem System auslesen
		
		$oneDay = 86400;//Sekunden
		$numDays = 7; //Standard sind 7 Tage
		$today = strtotime('now');
  
		if(!empty($this->settings['flexform']['countDays'])){
			$numDays = $this->settings['flexform']['countDays'];
		}
		
		//Datum berechnen
		$date = date("d-m-Y",$today-($numDays * $oneDay));
		$params['jq_term1'] = $date;//Datum bis wann Eintrag neu
		
		$config['detailPid'] = $this->settings['flexform']['detailPid'];
		
		//Liste abfragen
		$journals =  $this->ezbRepository->loadSearch($params, $config);
		
		//Variable Template übergeben
		$this->view->assign('journals', $journals);
		$this->view->assign('new_date', date("d.m.Y",$today-($numDays * $oneDay)));
		$this->view->assign('subject', $subject['title']);
	}

	/**
	 * prüft ob eine CSS-Datei eingebunden werden muss und macht es dann
	 */
	private function decideIncludeCSS(){
		$params = t3lib_div::_GET('libconnect');
		//UID des PlugIns ermitteln
		$this->contentObj = $this->configurationManager->getContentObject();
		$uid = $this->contentObj->data['uid'];
		unset($this->contentObj);
		
		//Nur das erste PlugIn auf der Seite soll die CSS-Datei einbinden
		if(IsfirstPlugInUserFunction('ezb', $uid)){
			$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');	
		}
	}
}
?>