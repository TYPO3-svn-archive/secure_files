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
 * The hook gets called right after a user is logged in
 */
class tx_SecureFiles_Hooks_Login {
	/**
	 * @var array The extension configuration.
	 */
	protected $extensionConfiguration = array();
	
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->extensionConfiguration = Tx_SecureFiles_Utility_Configuration::getExtensionConfigurationStatic();
	}

	/**
	 * This function generates is called on login.
	 *
	 * @param array $parameters
	 * @param t3lib_userauth $userObject
	 */
	public function loggedIn(&$parameters, $userObject) {
		$key = 'tx_securefiles_' . strtolower($userObject->loginType);
		$hash = $_COOKIE[$key];
		
		// at this point we can't use the base url yet
		$domain = empty($this->extensionConfiguration[strtolower($userObject->loginType) . 'CookieDomain']) ? t3lib_div::getIndpEnv('TYPO3_HOST_ONLY') : $this->extensionConfiguration[strtolower($userObject->loginType) . 'CookieDomain'];
		
		if (empty($hash)) {
			// random string of length 16-32 and md5 of that
			$hash = md5(t3lib_div::generateRandomBytes(mt_rand(16, 32)));
		}
		
		setcookie(
			$key,
			$hash,
			time() + intval($this->extensionConfiguration[strtolower($userObject->loginType) . 'CookieLifetime']),
			$this->extensionConfiguration[strtolower($userObject->loginType) . 'CookiePath'],
			$this->cookieDomain,
			$this->extensionConfiguration[strtolower($userObject->loginType) . 'CookieSecure'],
			$this->extensionConfiguration[strtolower($userObject->loginType) . 'CookieHTTPOnly']
		);
		
		if (strtolower($userObject->loginType) === 'be') {
			$this->allowBeUser($hash);
		}
	}
	
	/**
	 * Allow the current be user to access any files.
	 *
	 * @param string $hash The current users hash.
	 */
	protected function allowBeUser($hash) {
		$workingDirectory = preg_replace('#/+$#', '', $this->extensionConfiguration['workingDirectory']) . '/be/';
		
		if (!preg_match('#^/#', $workingDirectory)) {
			$workingDirectory = PATH_site . $workingDirectory;
		}
		
		if (!is_dir($workingDirectory)) {
			@mkdir($workingDirectory, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']), true);
		}
		
		touch($workingDirectory . $hash);
	}
}
?>
