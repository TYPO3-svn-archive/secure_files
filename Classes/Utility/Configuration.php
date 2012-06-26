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
 * Extension configuration wrapper that sets various defaults in case the extensino configuration is not properly set.
 */
class Tx_SecureFiles_Utility_Configuration implements t3lib_Singleton {
	
	/**
	 * @var array The extension configuration.
	 */
	protected $extensionConfiguration = array();
	
	/**
	 * @var string The regex to search the ext_conf_template.txt
	 */
	protected $configurationRegex = '/^\s*([^#\s][^\s]*)\s*=(.*)$/m';
	
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['secure_files']);
		
		$this->injectDefaults();
	}
	
	/**
	 * Injects the default values into the extension config if they are missing (are not set).
	 *
	 * @return void
	 */
	protected function injectDefaults() {
		$defaults = $this->parseExtConfTemplate();
		
		foreach ($defaults as $key => $value) {
			if (!isset($this->extensionConfiguration[$key])) {
				$this->extensionConfiguration[$key] = $value;
			}
		}
	}
	
	/**
	 * Parses the ext_conf_template.txt and returns an array with the values.
	 *
	 * @return array The parsed values.
	 */
	protected function parseExtConfTemplate() {
		$path = t3lib_extMgm::extPath('secure_files') . 'ext_conf_template.txt';
		$configurationContent = file_get_contents($path);
		
		$defaultConfiguration = array();
		
		if ($configurationContent !== false && preg_match_all($this->configurationRegex, $configurationContent, $matches)) {
			foreach ($matches[1] as $key => $identifier) {
				$defaultConfiguration[$identifier] = trim($matches[2][$key]);
			}
		}
		
		return $defaultConfiguration;
	}
	
	/**
	 * Returns the extension configuration with injected defaults.
	 *
	 * @return array The extension configuration.
	 */
	public function getExtensionConfiguration() {
		return $this->extensionConfiguration;
	}
	
	/**
	 * Returns the extension configuration with injected defaults.
	 * Static version of the above function, makes sure there is an object available.
	 * Because this class is a singleton this does not cause any problems.
	 *
	 * @return array The extension configuration.
	 */
	public static function getExtensionConfigurationStatic() {
		$instance = t3lib_div::makeInstance('Tx_SecureFiles_Utility_Configuration');
		return $instance->getExtensionConfiguration();
	}
}
?>
