<?php

/**
 * Class 'user_libconnect_hasSelectedPluginForCSSInclude' for the 'libconnect' extension.
 *
 * @author	Björn Heinermann <hein@zhaw.ch>
 * @package	TYPO3
 * @subpackage	tx_libconnect
 */

function user_libconnect_hasSelectedPluginForCSSInclude($type) {
    
	    $pid = $GLOBALS['TSFE']->id;
	    $list_type = 'libconnect_'.$type;
	    
	    $select = 'uid';
	    $from = 'tt_content';
	    $where = 'pid = "'.$pid.'" AND list_type = "'.$list_type.'" AND deleted = "0"';
	    $groupBy = '';
	    $orderBy = '';
	    $limit = '';
	    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
	    
	    if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
		return TRUE;
    }
    
    return FALSE;
}
?>
