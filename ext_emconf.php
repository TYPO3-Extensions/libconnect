<?php

########################################################################
# Extension Manager/Repository config file for ext "libconnect".
#
# Auto generated 03-05-2012 09:53
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'libconnect',
	'description' => 'Diese Extension ist von Avonis im Auftrag der Staats- und Universitaetsbibliothek Hamburg entwickelt worden. Mit ihr lassen sich Ergebnisse aus den Informationssystemen EZB und DBIS der Universitaet Regensburg direkt in das TYPO3-System einbinden.',
	'category' => 'plugin',
	'author' => 'Avonis New Media / SUB Hamburg',
	'author_email' => 'finck@sub.uni-hamburg.de',
	'author_company' => '',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '3.1.2',
	'dependencies' => 'cms,extbase,fluid',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:86:{s:9:"ChangeLog";s:4:"932e";s:21:"ExtensionBuilder.json";s:4:"87b1";s:12:"ext_icon.gif";s:4:"b57c";s:17:"ext_localconf.php";s:4:"3872";s:14:"ext_tables.php";s:4:"1845";s:14:"ext_tables.sql";s:4:"81d1";s:25:"ext_tables_static+adt.sql";s:4:"cb8b";s:12:"wiz_icon.gif";s:4:"38d5";s:37:"Classes/Controller/DbisController.php";s:4:"9cab";s:36:"Classes/Controller/EzbController.php";s:4:"6c04";s:40:"Classes/Controller/SubjectController.php";s:4:"7fcc";s:32:"Classes/Domain/Model/Subject.php";s:4:"0a4b";s:44:"Classes/Domain/Repository/DbisRepository.php";s:4:"71f1";s:43:"Classes/Domain/Repository/EzbRepository.php";s:4:"df2e";s:47:"Classes/Domain/Repository/SubjectRepository.php";s:4:"e847";s:41:"Classes/ViewHelpers/CompareViewHelper.php";s:4:"70bf";s:40:"Classes/ViewHelpers/IntvalViewHelper.php";s:4:"4748";s:41:"Classes/ViewHelpers/IsArrayViewHelper.php";s:4:"4109";s:40:"Classes/ViewHelpers/StrlenViewHelper.php";s:4:"af0b";s:46:"Classes/ViewHelpers/TrimedstrlenViewHelper.php";s:4:"219e";s:42:"Classes/ViewHelpers/TruncateViewHelper.php";s:4:"bdb2";s:43:"Classes/ViewHelpers/UrldecodeViewHelper.php";s:4:"049f";s:44:"Configuration/ExtensionBuilder/settings.yaml";s:4:"aa96";s:41:"Configuration/FlexForms/dbis_flexform.xml";s:4:"da47";s:40:"Configuration/FlexForms/ezb_flexform.xml";s:4:"b431";s:26:"Configuration/TCA/Dbis.php";s:4:"09d3";s:25:"Configuration/TCA/Ezb.php";s:4:"76f5";s:29:"Configuration/TCA/Subject.php";s:4:"a45b";s:38:"Configuration/TypoScript/constants.txt";s:4:"0880";s:34:"Configuration/TypoScript/setup.txt";s:4:"2ea9";s:58:"Configuration/Wizicon/class.tx_libconnect_dbis_wizicon.php";s:4:"943c";s:57:"Configuration/Wizicon/class.tx_libconnect_ezb_wizicon.php";s:4:"1d49";s:40:"Resources/Private/Language/locallang.xml";s:4:"0281";s:76:"Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_dbis.xml";s:4:"ebeb";s:75:"Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_ezb.xml";s:4:"db02";s:79:"Resources/Private/Language/locallang_csh_tx_libconnect_domain_model_subject.xml";s:4:"b005";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"ea05";s:38:"Resources/Private/Layouts/Default.html";s:4:"5e54";s:36:"Resources/Private/Lib/class_DBIS.php";s:4:"271b";s:35:"Resources/Private/Lib/class_EZB.php";s:4:"153f";s:47:"Resources/Private/Partials/Dbis/Properties.html";s:4:"9d7b";s:46:"Resources/Private/Partials/Ezb/Properties.html";s:4:"9d7b";s:51:"Resources/Private/Templates/Dbis/DisplayDetail.html";s:4:"89aa";s:50:"Resources/Private/Templates/Dbis/DisplayError.html";s:4:"d41d";s:49:"Resources/Private/Templates/Dbis/DisplayForm.html";s:4:"78b2";s:49:"Resources/Private/Templates/Dbis/DisplayList.html";s:4:"6b1d";s:53:"Resources/Private/Templates/Dbis/DisplayMiniForm.html";s:4:"5c48";s:53:"Resources/Private/Templates/Dbis/DisplayOverview.html";s:4:"842d";s:51:"Resources/Private/Templates/Dbis/DisplaySearch.html";s:4:"fe39";s:48:"Resources/Private/Templates/Dbis/DisplayTop.html";s:4:"3f96";s:50:"Resources/Private/Templates/Ezb/DisplayDetail.html";s:4:"fd2a";s:48:"Resources/Private/Templates/Ezb/DisplayForm.html";s:4:"e86c";s:48:"Resources/Private/Templates/Ezb/DisplayList.html";s:4:"a5e2";s:52:"Resources/Private/Templates/Ezb/DisplayMiniForm.html";s:4:"ee16";s:52:"Resources/Private/Templates/Ezb/DisplayOverview.html";s:4:"4bca";s:50:"Resources/Private/Templates/Ezb/DisplaySearch.html";s:4:"41fe";s:54:"Resources/Private/Templates/Subject/DisplayDetail.html";s:4:"33cb";s:45:"Resources/Private/Templates/Subject/Show.html";s:4:"498f";s:35:"Resources/Public/Icons/relation.gif";s:4:"e615";s:58:"Resources/Public/Icons/tx_libconnect_domain_model_dbis.gif";s:4:"1103";s:57:"Resources/Public/Icons/tx_libconnect_domain_model_ezb.gif";s:4:"1103";s:61:"Resources/Public/Icons/tx_libconnect_domain_model_subject.gif";s:4:"1103";s:30:"Resources/Public/Img/Thumbs.db";s:4:"b4ac";s:36:"Resources/Public/Img/dbis-list_0.png";s:4:"4ca5";s:36:"Resources/Public/Img/dbis-list_1.png";s:4:"fffe";s:36:"Resources/Public/Img/dbis-list_2.png";s:4:"efff";s:36:"Resources/Public/Img/dbis-list_4.png";s:4:"5b3e";s:36:"Resources/Public/Img/dbis-list_5.png";s:4:"1a6a";s:36:"Resources/Public/Img/dbis-list_6.png";s:4:"8ff8";s:36:"Resources/Public/Img/dbis-list_7.png";s:4:"2000";s:29:"Resources/Public/Img/euro.png";s:4:"76f7";s:35:"Resources/Public/Img/ezb-list_1.png";s:4:"4ca5";s:35:"Resources/Public/Img/ezb-list_2.png";s:4:"fffe";s:35:"Resources/Public/Img/ezb-list_4.png";s:4:"5b3e";s:35:"Resources/Public/Img/ezb-list_6.png";s:4:"9401";s:35:"Resources/Public/Img/ezb-list_7.png";s:4:"5b3e";s:32:"Resources/Public/Img/germany.png";s:4:"5f7e";s:32:"Resources/Public/Styles/dbis.css";s:4:"0c3b";s:31:"Resources/Public/Styles/ezb.css";s:4:"87e9";s:44:"Tests/Unit/Controller/DbisControllerTest.php";s:4:"bcb2";s:43:"Tests/Unit/Controller/EzbControllerTest.php";s:4:"01b4";s:47:"Tests/Unit/Controller/SubjectControllerTest.php";s:4:"67ba";s:36:"Tests/Unit/Domain/Model/DbisTest.php";s:4:"81eb";s:35:"Tests/Unit/Domain/Model/EzbTest.php";s:4:"1ee5";s:39:"Tests/Unit/Domain/Model/SubjectTest.php";s:4:"d561";s:14:"doc/manual.sxw";s:4:"daee";}',
	'suggests' => array(
	),
	'conflicts' => '',
);

?>