<?php
Class Tx_Libconnect_Domain_Repository_SubjectRepository extends Tx_Extbase_Persistence_Repository {

	public function findRandom() {
        $rows = $this->createQuery()->execute()->count();
        $row_number = mt_rand(0, max(0, ($rows - 1)));
        return $this->createQuery()->setOffset($row_number)->setLimit(1)->execute();
    }
	
	public function findAll() {
 		//$extbaseFrameworkConfiguration = Tx_Extbase_Dispatcher::getExtbaseFrameworkConfiguration();
		//$pidList = implode(', ', t3lib_div::intExplode(',', $extbaseFrameworkConfiguration['persistence']['storagePid']));
		$query = $this->createQuery();
		$query->statement('SELECT * from tx_libconnect_subject');
		
		return $query->execute();
 	}
}
?>