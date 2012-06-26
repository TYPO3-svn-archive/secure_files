<?php
// DO NOT CHANGE THIS FILE!

$extensionPath = t3lib_extMgm::extPath('secure_files');
return array(
	'tx_securefiles_scheduler_cleanuptask' => $extensionPath . 'Classes/Scheduler/CleanupTask.php',
	'tx_securefiles_scheduler_additionalfieldsprovider' => $extensionPath . 'Classes/Scheduler/AdditionalFieldsProvider.php',
	'tx_securefiles_scheduler_cleanupadditionalfieldsprovider' => $extensionPath . 'Classes/Scheduler/CleanupAdditionalFieldsProvider.php',
	'tx_securefiles_hooks_parsecontent' => $extensionPath . 'Classes/Hooks/ParseContent.php',
	'tx_securefiles_utility_parser' => $extensionPath . 'Classes/Utility/Parser.php',
	'tx_securefiles_utility_configuration' => $extensionPath . 'Classes/Utility/Configuration.php',
);
?>
