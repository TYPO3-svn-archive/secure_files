<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nils Blattner <nb@cabag.ch>, cab services ag
 *  			
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
 * Test case for class Tx_SecureFiles_Domain_Model_Public.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Secured files using mod_rewrite
 *
 * @author Nils Blattner <nb@cabag.ch>
 */
class Tx_SecureFiles_Domain_Model_PublicTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_SecureFiles_Domain_Model_Public
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_SecureFiles_Domain_Model_Public();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getFolderReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setFolderForStringSetsFolder() { 
		$this->fixture->setFolder('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getFolder()
		);
	}
	
}
?>