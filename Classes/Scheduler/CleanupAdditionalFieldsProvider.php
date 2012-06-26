<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nils Blattner <nb@cabag.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * The additional fields provider for the cleanup task.
 */

class tx_SecureFiles_Scheduler_CleanupAdditionalFieldsProvider extends tx_SecureFiles_Scheduler_AdditionalFieldsProvider {
	public function __construct() {
		$this->additionalParameters = array(
			'root_folder' => array(
				'type' => 'string',
				'default' => 'typo3temp/tx_securefiles/',
				'label' => 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:tx_securefiles_scheduler_cleanup.root_folder',
			),
			'max_lifetime' => array(
				'type' => 'int',
				'default' => '172800',
				'label' => 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:tx_securefiles_scheduler_cleanup.max_lifetime',
			)
		);
		
		parent::__construct();
	}
}

?>
