<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Dbis',
	array(
		'Dbis' => 'displayDetail, displayList, displayMiniForm, displayForm'
	),
	// non-cacheable actions
	array(
		'Subject' => '',
		'Dbis' => 'displayDetail, displayList, displayMiniForm, displayForm',
		'Ezb' => ''
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Ezb',
	array(
		'Ezb' => 'displayList, displayDetail, displayMiniForm, displayForm'
	),
	// non-cacheable actions
	array(
		'Subject' => '',
		'Dbis' => '',
		'Ezb' => 'displayDetail, displayList, displayMiniForm, displayForm'
		
	)
);

?>