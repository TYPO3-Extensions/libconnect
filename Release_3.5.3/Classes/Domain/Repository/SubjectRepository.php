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

Class Tx_Libconnect_Domain_Repository_SubjectRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Fächer zufällig aus Datenbank laden
	 * 
	 * @return object
	 */
	public function findRandom() {
        $rows = $this->createQuery()->execute()->count();
        $row_number = mt_rand(0, max(0, ($rows - 1)));
		
        return $this->createQuery()->setOffset($row_number)->setLimit(1)->execute();
    }
	
	/**
	 * Fächer aus Datenbank laden
	 * 
	 * @return object
	 */
	public function findAll() {
 		//$extbaseFrameworkConfiguration = Tx_Extbase_Dispatcher::getExtbaseFrameworkConfiguration();
		//$pidList = implode(', ', t3lib_div::intExplode(',', $extbaseFrameworkConfiguration['persistence']['storagePid']));
		$query = $this->createQuery();
		$query->statement('SELECT * from tx_libconnect_domain_model_subject');
		
		return $query->execute();
 	}
}
?>