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
 * The abstract additional fields provider.
 */

class tx_SecureFiles_Scheduler_AdditionalFieldsProvider implements tx_scheduler_AdditionalFieldProvider {
	
	protected $additionalParameters = array();
	
	public function __construct() {
		$this->additionalParameters = array_merge(
			$this->additionalParameters,
			array(
				'debug' => array(
					'type' => 'boolean',
					'default' => '0',
					'label' => 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:provider.debug',
				),
				'dryRun' => array(
					'type' => 'boolean',
					'default' => '0',
					'label' => 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:provider.dryRun',
				)
			)
		);
	}

	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param	array					Values of the fields from the add/edit task form
	 * @param	tx_scheduler_Task		The task object being eddited. Null when adding a task!
	 * @param	tx_scheduler_Module		Reference to the scheduler backend module
	 * @return	array					A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $schedulerModule) {
		$return = array();
		
		if ($task != null) {
			$settings = $task->getSettings();
		} else {
			$settings = array();
		}
		$class = get_class($this);
		foreach ($this->additionalParameters as $key => $def) {
			// every class needs their own variables
			$setKey = $key;
			$key = $class . '_' . $key;
			
			// Initialize field value
			if (empty($taskInfo['$key'])) {
				if ($schedulerModule->CMD == 'add') {
						// In case of new task and if field is empty, set default sleep time
					$taskInfo[$key] = $def['default'];
				} else if ($schedulerModule->CMD == 'edit') {
						// In case of edit, set to internal value if no data was submitted already
					$taskInfo[$key] = $settings[$setKey];
				} else {
						// Otherwise set an empty value, as it will not be used anyway
					$taskInfo[$key] = '';
				}
			}
			
			$entry = array();
			
			switch ($def['type']) {
				case 'int' :
					$entry = array(
						'code'     => '<input type="text" name="tx_scheduler[' . $key . ']" id="' . $key . '" value="' . $taskInfo[$key] . '" />',
						'label'    => $def['label'],
						'cshKey'   => '_MOD_tools_txschedulerM1',
						'cshLabel' => $key
					);
					break;
				case 'string' :
					$entry = array(
						'code'     => '<input type="text" name="tx_scheduler[' . $key . ']" id="' . $key . '" value="' . $taskInfo[$key] . '" />',
						'label'    => $def['label'],
						'cshKey'   => '_MOD_tools_txschedulerM1',
						'cshLabel' => $key
					);
					break;
				case 'boolean' :
					$checked = '';
					if (!empty($taskInfo[$key])) {
						$checked = ' checked="checked"';
					}
					$entry = array(
						'code'     => '<input type="checkbox" name="tx_scheduler[' . $key . ']" id="' . $key . '" value="1"' . $checked . ' />',
						'label'    => $def['label'],
						'cshKey'   => '_MOD_tools_txschedulerM1',
						'cshLabel' => $key
					);
					break;
			}
			
			$return[$key] = $entry;
		}
		
		return $return;
	}

	/**
	 * Validates the additional fields' values
	 *
	 * @param	array					An array containing the data submitted by the add/edit task form
	 * @param	tx_scheduler_Module		Reference to the scheduler backend module
	 * @return	boolean					True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $schedulerModule) {
		
		$return = true;
		
		$class = get_class($this);
		
		foreach ($this->additionalParameters as $key => $def) {
			$key = $class . '_' . $key;
			
			switch ($def['type']) {
				case 'int' :
					$submittedData[$key] = intval($submittedData[$key]);
					if ($submittedData[$key] <= 0) {
						$return = false;
						$schedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:cabag_steps/Resources/Private/Language/Resources/Private/Language/locallang_db.xml:tx_cabagsteps_scheduler_milestone.error') . $key, t3lib_FlashMessage::ERROR);
					}
					break;
				case 'string' :
					$submittedData[$key] = empty($submittedData[$key]) ? '' : $submittedData[$key];
					// TODO: add some regex validation
					break;
				case 'boolean' :
					// we accept anything as a boolean
					break;
			}
			
			//$return[$key] = $entry;
		}
		return $return;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param	array					An array containing the data submitted by the add/edit task form
	 * @param	tx_scheduler_Module		Reference to the scheduler backend module
	 * @return	void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$settings = array();
		
		$class = get_class($this);
		foreach ($this->additionalParameters as $key => $def) {
			$setKey = $key;
			$key = $class . '_' . $key;
			
			switch ($def['type']) {
				case 'int' :
					$settings[$setKey] = intval($submittedData[$key]);
					break;
				case 'boolean' :
					$submittedData[$key] = intval($submittedData[$key]);
					if ($submittedData[$key]) {
						$settings[$setKey] = true;
					} else {
						$settings[$setKey] = false;
					}
					break;
					
				default :
					$settings[$setKey] = $submittedData[$key];
					
			}
		}
		
		$task->setSettings($settings);
	}
}

?>
