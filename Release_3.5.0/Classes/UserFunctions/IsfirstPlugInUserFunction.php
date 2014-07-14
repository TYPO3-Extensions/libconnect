<?php

/**
 * Class 'IsfirstPlugInUserFunction' for the 'libconnect' extension.
 *
 * @author	Torsten Witt
 * @package	TYPO3
 * @subpackage	tx_libconnect
 */

/**
 * Prüft ob das aufrufende PlugIn das erste auf der Seite ist
 * 
 * @param string	$type: Name des gewählten PlugIns
 * @param integer	$uid: Uid des PlugIns aus Tabelle tt_content
 * 
 * @return boolean		
 */
function IsfirstPlugInUserFunction($type, $uid) {
    $pid = $GLOBALS['TSFE']->id;

    $list_type = 'libconnect_'.$type;
    
    $select = 'uid, pid, list_type, sorting';
    $from = 'tt_content';
    $where = 'pid = "'.$pid.'" AND list_type = "'.$list_type.'" AND deleted = "0"';
    $groupBy = '';
    $orderBy = 'sorting asc';
    $limit = '0,1';
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

	//Wenn die aktuelle UID nicht ganz oben auf de Seite ist, soll diese kein CSS einbinden
	while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
		if($row['uid'] != $uid){
			return FALSE;
		}
	}
    
    return TRUE;
}
?>