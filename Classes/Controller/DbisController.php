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

class Tx_Libconnect_Controller_DbisController extends Tx_Extbase_MVC_Controller_ActionController {
	
	/**
	 * zeigt die Top-Datenbanken an
	 */
	public function displayTopAction() {
		//CSS includieren
		$this->decideIncludeCSS();
		
		$config['subject'] = $this->settings['flexform']['subject'];
		$config['detailPid'] = $this->settings['flexform']['detailPid'];

		$top =  $this->dbisRepository->loadTop($config);
		
		//Variable Template übergeben
		$this->view->assign('top', $top);
	}
	
	/**
	 * Zeigt eine Liste von Datenbanken (für allgemein, Suche und das gewählte Fach)
	 */
	public function displayListAction() {
		$params = t3lib_div::_GET('libconnect');
		
		//Test wegen Kompatibilität
		/*$version = t3lib_div::int_from_ver(TYPO3_version);
		if($version < 40060000){//Älter als Version 4.6?
			$this->view->setTemplateRootPath(t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Private/Oldtemplates/');	
		}*/
		
		//CSS includieren
		$this->decideIncludeCSS();
		
		if (!empty($params['subject'])) {//Gewaehltes Fach nach Einstiegspunkt
			$config['sort'] = $this->settings['flexform']['sortParameter'];
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			//Sortierung der Liste durch User
			if(isset($params['sort']) && !empty($params['sort'])) {
				$config['sort'] = 	$params['sort'];
			}
			
			$liste =  $this->dbisRepository->loadList($params['subject'], $config);
			
			//Variablen Template übergeben
			$this->view->assign('subject', $liste['subject']);
			$this->view->assign('list', $liste['list']);

		} else if (!empty($params['search'])) {//Suchergebnisse
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			$liste =  $this->dbisRepository->loadSearch($params['search'], $config);
			
			//andere View verwenden
			$controllerContext = $this->buildControllerContext();
			$controllerContext->getRequest()->setControllerActionName('displaySearch');
			$this->view->setControllerContext($controllerContext);
			
			//Variable Template übergeben
			$this->view->assign('list', $liste);

		} else {//Einstiegspunkt
			$liste =  $this->dbisRepository->loadOverview();
			
			//andere View verwenden
			$controllerContext = $this->buildControllerContext();
			$controllerContext->getRequest()->setControllerActionName('displayOverview');
			$this->view->setControllerContext($controllerContext);
			
			//Variable Template übergeben
			$this->view->assign('list', $liste);
		}
    }
	
	public function injectDbisRepository(Tx_Libconnect_Domain_Repository_DbisRepository $dbisRepository){
		$this->dbisRepository = $dbisRepository;
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
		
		if (!($params['titleid'])){
			//Variable Template übergeben
			$this->view->assign('error', 'Error');
			
			return;
		}
		$liste =  $this->dbisRepository->loadDetail($params['titleid']);
		
		if(!$liste){
			//Variable Template übergeben
			$this->view->assign('error', 'Error');
			
		}else{
			//BG> Hide start research link for internal access only items
			if($liste['access_id']!='access_4'){
				$liste['access_workaround']=$liste['access_id'];
			}
			//Variable Template übergeben
			$this->view->assign('db', $liste);
		}		
    }
	
	/**
	 * zeigt die Sidebar
	 */
	public function displayMiniFormAction() {
		$params = t3lib_div::_GET('libconnect');
		
		//CSS includieren
		$this->decideIncludeCSS();
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
    	$form = $this->dbisRepository->loadMiniForm();
		
		//Variablen Template übergeben
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('searchUrl', $cObject->getTypolink_URL($this->settings['flexform']['searchPid']));//Link zur Suchseite
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
		$this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID der Listendarstellung
		
		//Möglichkeit zur Sortierung der Einträge des Fachgebietes erst nach Wahl des Fachgebietes
		if (!empty($params['subject'])) {
			$this->view->assign('listingsWrapper', true);
			
			//Wenn New aktiviert soll hier auch das Neu im Fach aktiviert werden
			if(!empty($this->settings['flexform']['newPid'])){
				$cObject = t3lib_div::makeInstance('tslib_cObj');
				
				$this->view->assign('newUrlSub', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
					array('libconnect' => array('subject' => $params['subject'] )) ) );//URL der New-Darstellung
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
	
    	$form = $this->dbisRepository->loadForm($params['search']);
		
		//Variablen Template übergeben
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
		$this->view->assign('listPid', $this->settings['flexform']['listPid']);//Link zur Listendarstellung
    }

	/**
	 * zeigt die neuesten Einträge
	 */
	public function displayNewAction() {
		$params = t3lib_div::_GET('libconnect');
		$params['jq_type1'] = 'LD';
		$params['sc'] = $params['search']['sc'];
		if(!empty($params['subject'])){
			$subject = $this->dbisRepository->getSubject($params['subject']);
			$params['gebiete']=array($subject['dbisid']);
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
		$date = date("d.m.Y",$today-($numDays * $oneDay));
		$params['jq_term1'] = $date;//Datum bis wann Eintrag neu
		
		$config['detailPid'] = $this->settings['flexform']['detailPid'];
		
		//Liste abfragen
		$liste =  $this->dbisRepository->loadSearch($params, $config);

		//Variable Template übergeben
		$this->view->assign('list', $liste);
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
		if(IsfirstPlugInUserFunction('dbis', $uid)){
			$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');	
		}
	}
}
?>