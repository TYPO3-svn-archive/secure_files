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
 * The hook to get the content after it is rendered.
 */
class tx_SecureFiles_Hooks_ParseContent {

	/**
	 * This function generates the user/public file index.
	 *
	 * @param array $parameters
	 * @param tslib_fe $tsfe
	 */
	public function parse(&$parameters, $tsfe) {
		$parser = t3lib_div::makeInstance('tx_SecureFiles_Utility_Parser');
		
		$parser->parse($tsfe->content);
	}
}
?>
