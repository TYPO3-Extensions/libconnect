<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Torsten Witt <witt@sub.uni-hamburg.de>, Stabi Hamburg
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Libconnect_Controller_DbisController extends Tx_Extbase_MVC_Controller_ActionController {
	
	/**
	 * zeigt die Top-Datenbanken an
	 */
	public function displayTopAction() {
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
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');
		
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
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');
		
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
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');		
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		
    	$form = $this->dbisRepository->loadMiniForm($params['search']);
		
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
		}
		
    }
	
	/**
	 * zeigt die Suche
	 */
	public function displayFormAction() {
		$params = t3lib_div::_GET('libconnect');
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');
				
		$cObject = t3lib_div::makeInstance('tslib_cObj');
	
    	$form = $this->dbisRepository->loadForm($params['search']);
		
		//Variablen Template übergeben
		$this->view->assign('vars', $params['search']);
		$this->view->assign('form', $form);
		$this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
		$this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
		$this->view->assign('listPid', $this->settings['flexform']['listPid']);//Link zur Listendarstellung
    }
}
?>