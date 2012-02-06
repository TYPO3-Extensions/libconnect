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
class Tx_Libconnect_Controller_SubjectController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * action show
	 *
	 * @param $subject
	 * @return void
	 */
	public function showAction(Tx_Libconnect_Domain_Model_Subject $subject) {
//	public function showAction() {
		$this->view->assign('subject', $subject);
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$subjects = $this->subjectRepository->findAll();
		$this->view->assign('subjects', $subjects);
	}
	/*
	public function detailAction() {
		$this->view->assign('subject', $subject);
	}
	
	public function displayDetailAction() {
		$this->view->assign('subject', $subject);
	}*/

}
?>