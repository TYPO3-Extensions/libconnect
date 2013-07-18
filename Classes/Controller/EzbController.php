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
class Tx_Libconnect_Controller_EzbController extends Tx_Extbase_MVC_Controller_ActionController {
	
	 /**
	 * Zeigt eine Liste von Datenbanken (für allgemein, Suche und das gewählte Fach)
	 */
	public function displayListAction() {	
		$params = t3lib_div::_GET('libconnect');
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');
		
		if (!empty($params['subject'])) {//Gewaehltes Fach nach Einstiegspunkt
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			$options['index'] = $params['index'];
			$options['sc'] = $params['sc'];
			$options['lc'] = $params['lc'];
			
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
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');

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
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
    	$form = $this->ezbRepository->loadMiniForm($params['search']);
		
		//Variablen Template übergeben	
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('searchUrl', $cObject->getTypolink_URL($this->settings['flexform']['searchPid']));//Link zur Suchseite
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
	    $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID der Listendarstellung
	}
	
	/**
	 * zeigt die Suche
	 */
	public function displayFormAction() {
		$params = t3lib_div::_GET('libconnect');
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$form =  $this->ezbRepository->loadForm();
		
		//Variablen Template übergeben
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
		$this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID der Listendarstellung
	}
}
?>