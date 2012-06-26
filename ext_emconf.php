<?php

########################################################################
# Extension Manager/Repository config file for ext "secure_files".
#
# Auto generated 08-03-2012 11:20
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Secured files using mod_rewrite',
	'description' => 'This is an extension, that helps secure any files in the TYPO3-System.
The idea behind it is that:
- file paths stay the same
- configuration is easy
- folders can be marked public',
	'category' => 'misc',
	'author' => 'Nils Blattner',
	'author_email' => 'nb@cabag.ch',
	'author_company' => 'cab services ag',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '',
	'createDirs' => 'typo3temp/tx_securefiles/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.0.8',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:28:{s:9:"ChangeLog";s:4:"1020";s:21:"ExtensionBuilder.json";s:4:"fc41";s:10:"Readme.txt";s:4:"d1d9";s:16:"ext_autoload.php";s:4:"de6d";s:21:"ext_conf_template.txt";s:4:"5d86";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"3898";s:14:"ext_tables.php";s:4:"7890";s:14:"ext_tables.sql";s:4:"4272";s:31:"Classes/Domain/Model/Public.php";s:4:"0749";s:46:"Classes/Domain/Repository/PublicRepository.php";s:4:"f513";s:23:"Classes/Hooks/Login.php";s:4:"50d7";s:30:"Classes/Hooks/ParseContent.php";s:4:"baec";s:46:"Classes/Scheduler/AdditionalFieldsProvider.php";s:4:"438c";s:53:"Classes/Scheduler/CleanupAdditionalFieldsProvider.php";s:4:"eca1";s:33:"Classes/Scheduler/CleanupTask.php";s:4:"197f";s:33:"Classes/Utility/Configuration.php";s:4:"117d";s:26:"Classes/Utility/Parser.php";s:4:"8869";s:44:"Configuration/ExtensionBuilder/settings.yaml";s:4:"073f";s:28:"Configuration/TCA/Public.php";s:4:"cf5d";s:40:"Resources/Private/Language/locallang.xml";s:4:"0ac6";s:79:"Resources/Private/Language/locallang_csh_tx_securefiles_domain_model_public.xml";s:4:"e8db";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"1317";s:35:"Resources/Public/Icons/relation.gif";s:4:"e615";s:61:"Resources/Public/Icons/tx_securefiles_domain_model_public.gif";s:4:"905a";s:46:"Tests/Unit/Controller/PublicControllerTest.php";s:4:"346a";s:38:"Tests/Unit/Domain/Model/PublicTest.php";s:4:"7e37";s:14:"doc/manual.sxw";s:4:"8d2d";}',
);

?>