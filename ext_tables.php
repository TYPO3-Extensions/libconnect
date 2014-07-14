<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Dbis',
	'libconnect: dbis'
);


Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Ezb',
	'libconnect: ezb'
);

// Add flexform for DBIS
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_dbis'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_dbis', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/dbis_flexform.xml');

// Add flexform for EZB
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_ezb'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_ezb', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/ezb_flexform.xml');


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/', 'libconnect');


t3lib_extMgm::addLLrefForTCAdescr('tx_libconnect_domain_model_subject', 'EXT:libconnect/Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_subject.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_libconnect_domain_model_subject');
$TCA['tx_libconnect_domain_model_subject'] = array(
	'ctrl' => array(
		'title'	=> 'Fachgebiet',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		//'dividers2tabs' => TRUE,
		//'versioningWS' => 2,
		//'versioning_followPages' => TRUE,
		//'origUid' => 't3_origuid',
		//'languageField' => 'sys_language_uid',
		//'transOrigPointerField' => 'l10n_parent',
		//'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			//'starttime' => 'starttime',
			//'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Subject.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_libconnect_domain_model_subject.gif'
	),
);
/*
t3lib_extMgm::addLLrefForTCAdescr('tx_libconnect_domain_model_dbis', 'EXT:libconnect/Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_dbis.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_libconnect_domain_model_dbis');
$TCA['tx_libconnect_domain_model_dbis'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_dbis',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Dbis.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_libconnect_domain_model_dbis.gif'
	),
);
/*
t3lib_extMgm::addLLrefForTCAdescr('tx_libconnect_domain_model_ezb', 'EXT:libconnect/Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_ezb.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_libconnect_domain_model_ezb');
$TCA['tx_libconnect_domain_model_ezb'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:libconnect/Resources/Private/Language/locallang_db.xml:tx_libconnect_domain_model_ezb',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Ezb.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_libconnect_domain_model_ezb.gif'
	),
);*/

if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_libconnect_dbis_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Wizicon/class.tx_libconnect_dbis_wizicon.php';
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_libconnect_ezb_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/Wizicon/class.tx_libconnect_ezb_wizicon.php';
}

?>