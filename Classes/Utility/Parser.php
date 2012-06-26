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
 * The html parser that extracts the references.
 * Some extracts taken from naw_securedl.
 */
class tx_SecureFiles_Utility_Parser implements t3lib_Singleton {
	/**
	 * @var array The extension configuration.
	 */
	protected $extensionConfiguration = array();
	
	/**
	 * @var string The current working directory (eg. PATH_site . 'typo3temp/tx_securefiles/fe/)
	 */
	protected $workingDirectory = '';
	
	/**
	 * @var boolean Whether or not the current user is logged in.
	 */
	protected $userIsLoggedIn = false;
	
	/**
	 * @var string The cookie domain, empty string if it should be skipped.
	 */
	protected $baseUrl = '';
	
	/**
	 * @var string The regex to extract the base url.
	 */
	protected $baseUrlRegex = '#<base[^"/]+href=([\'"])([^\\1]+)\\1[^"/]*/>#siU';
	
	/**
	 * @var string The regex to use in order to get the resources.
	 */
	protected $resourceRegex = '/(<source|<a|<img|<link)[^>]*(href|src)=([\"\']??)([^\\3 >]*)\\3[^>]*>/siU';
	
	/**
	 * @var boolean Whether or not the cookie is set already.
	 */
	protected $cookieIsSet = false;
	
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->extensionConfiguration = Tx_SecureFiles_Utility_Configuration::getExtensionConfigurationStatic();
		
		$this->workingDirectory = preg_replace('#/+$#', '', $this->extensionConfiguration['workingDirectory']) . '/';
		
		if (!preg_match('#^/#', $this->workingDirectory)) {
			$this->workingDirectory = PATH_site . $this->workingDirectory;
		}
		
		if (!is_dir($this->workingDirectory)) {
			$this->mkdir($this->workingDirectory);
		}
		
		if (!is_file($this->workingDirectory . '.htaccess')) {
			@file_put_contents($this->workingDirectory . '.htaccess', 'Deny from all');
		}
		
		if (intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0) {
			$this->userIsLoggedIn = true;
		}
	}

	/**
	 * This function parses all the html content for a/img/etc. references.
	 *
	 * @param string $htmlContent The HTML content to parse for references.
	 */
	public function parse(&$htmlContent) {
		$this->extractBaseUrl($htmlContent);
		
		if (preg_match_all($this->resourceRegex, $htmlContent, $matches)) {
			foreach ($matches[4] as &$resource) {
				$file = $this->cleanResource($resource);
				
				if ($file !== false) {
					$this->allowDownload($file);
				}
			}
		}
	}
	
	/**
	 * Extracts the base url if present or sets the requested domain as base url.
	 *
	 * @return void
	 */
	protected function extractBaseUrl(&$htmlContent) {
		if (preg_match($this->baseUrlRegex, $htmlContent, $match)) {
			$parts = parse_url($match[2]);
			$this->baseUrl = $parts['host'];
		} else {
			$this->baseUrl = empty($this->extensionConfiguration['feCookieDomain']) ? t3lib_div::getIndpEnv('TYPO3_HOST_ONLY') : $this->extensionConfiguration['feCookieDomain'];
		}
	}
	
	/**
	 * Wrapper for the mkdir function. Always creates recursively and with TYPO3 UMASK.
	 *
	 * @param string The folder to create. Must be absolute!
	 * @return boolean True if the mkdir was successfull, false otherwise.
	 */
	protected static function mkdir($folder) {
		return @mkdir($folder, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']), true) or false;
	}
	
	/**
	 * Cleans the resource of any extra stuff. Returns false in case its external or mailto: etc.
	 *
	 * @param string $resource The resource.
	 * @return mixed The cleaned relative path.
	 */
	protected function cleanResource($resource) {
		$result = false;
		if (preg_match('#^https?://([^/\?\#]+)/([^\?\#]+)(\?.*)?(\#.*)?$#', $resource, $match)) {
			if ($this->baseUrl == $match[1]) {
				$result = $match[2];
			}
		} else if (preg_match('#^(javascript|mailto):#', $resource)) {
			// ignore
		} else if (preg_match('#^/?([^\?\#]+)(\?.*)?(\#.*)?$#', $resource, $match)) {
			$result = $match[1];
		}
		
		$result = preg_replace('#/+#', '/', urldecode($result));
		
		if ($result && !is_file(PATH_site . $result)) {
			$result = false;
		}
		
		return $result;
	}
	
	/**
	 * Allow the current user to download the given file (either via public of user specific rights).
	 *
	 * @var string $file Relative path to the file.
	 */
	protected function allowDownload($file) {
		$path = $this->getAllowPath($file);
		$this->mkdir(dirname($path));
		@touch($path);
	}
	
	/**
	 * Returns the path under where the permission file is stored.
	 *
	 * @var string $file The relative file to get the permission file path.
	 * @return string The permission file path.
	 */
	protected function getAllowPath($file) {
		$hash = '';
		$section = 'pub/';
		
		if ($this->userIsLoggedIn) {
			$hash = '/' . $this->getUserHash();
			$section = 'fe/';
		}
		
		return $this->workingDirectory . $section . $file . $hash;
	}
	
	/**
	 * Returns the users hash. If user doesn't have one it is generated and saved in the session.
	 * This function must not be called if the user is not logged in!
	 *
	 * @return string The users hash.
	 */
	protected function getUserHash() {
		$hash = $_COOKIE['tx_securefiles_fe'];
		
		if (empty($hash)) {
			// random string of length 16-32 and md5 of that
			$hash = md5(t3lib_div::generateRandomBytes(mt_rand(16, 32)));
		}
		
		if (!$this->cookieIsSet) {
			setcookie(
				'tx_securefiles_fe',
				$hash,
				time() + intval($this->extensionConfiguration['feCookieLifetime']),
				$this->extensionConfiguration['feCookiePath'],
				$this->cookieDomain,
				$this->extensionConfiguration['feCookieSecure'],
				$this->extensionConfiguration['feCookieHTTPOnly']
			);
			$this->cookieIsSet = true;
		}
		
		return $hash;
	}
}
?>
