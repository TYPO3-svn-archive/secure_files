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
 * The Task cleaning up old files.
 */

class Tx_SecureFiles_Scheduler_CleanupTask extends tx_scheduler_Task {
	/**
	 * @var array Settings provided by the scheduler.
	 */
	public $settings = array();
	
	/**
	 * @var array Log.
	 */
	protected $log = array();
	
	/**
	 * @var array Errors.
	 */
	protected $errors = array();
	
	/**
	 * The main function beeing called from the scheduler.
	 *
	 * @return void
	 */
	public function execute() {
		$folder = $this->settings['root_folder'];
		$folder = substr($folder, 0, 1) === '/' ? $folder : PATH_site . $folder;
		
		$lifetime = intval($this->settings['max_lifetime']);
		$deleteBefore = $lifetime > 0 ? time() - $lifetime : 0;
		
		$this->log('Parameters resolved to (root folder: ' . $folder . ', lifetime: ' . $lifetime . ')');
		
		if (is_dir($folder)) {
			$this->log('Starting recursion...');
			$this->walkRecursive($folder, $deleteBefore);
		}
		
		if (count($this->errors) > 0) {
			throw new Exception("Finished with errors:\n" . implode("\n", array_merge($this->errors, $this->log)));
		}
		
		$this->log('Finished successfully!');
		
		if ($this->settings['debug']) {
			throw new Exception("Success!:\n" . implode("\n", $this->log));
		}
		
		return true;
	}
	
	/**
	 * Walk the folders/pages recursively.
	 * 
	 * @param string The folder to use as root for the current recursion.
	 * @param int The date before which all files should be deleted.
	 * @param int The current recursion depth.
	 * @return int The amount of objects still in this folder.
	 */
	protected function walkRecursive($folder, $deleteBefore = 0, $depth = 0) {
		$folder = preg_replace('#/$#', '', $folder) . '/';
		
		$physical = $this->resolvePhysical($folder);
		$folders = &$physical['folders'];
		$files = &$physical['files'];
		$objects = 0;
		
		// for those folders there is no page record
		foreach ($folders as &$folderToDelete) {
			$remaining = $this->walkRecursive($folder . $folderToDelete, $deleteBefore, $depth + 1);
			
			if ($remaining > 0) {
				$objects++;
			} else {
				@rmdir($folder . $folderToDelete) or $this->error('Error deleting folder: ' . $folder . $folderToDelete);
			}
		}
		
		$objects += $this->checkAndDeleteFiles($folder, $files, $deleteBefore);
		
		return $objects;
	}
	
	/**
	 * Checks the file age and deletes files too old.
	 * Also makes sure there are only physical/db file together.
	 * 
	 * @param string The folder to check in.
	 * @param array Array of strings, the file names.
	 * @param int Files older than this date will be deleted.
	 * @return int The amount of files left in the directory.
	 */
	protected function checkAndDeleteFiles($folder, $files, $deleteBefore) {
		$this->log('Checking files:' . PHP_EOL . print_r(array('folder' => $folder, 'files' => $files, 'fileRecords' => $fileRecords), true));
		foreach ($files as &$file) {
			if (filectime($folder . $file) < $deleteBefore) {
				$this->log('Deleting file because it is too old: ' . $folder . $file);
				if (!$this->settings['dryMode']) {
					@unlink($folder . $file) or $this->error('Error deleting file: ' . $folder . $file);
				}
				unset($files[$file]);
			}
		}
		
		return count($files);
	}
	
	/**
	 * Log a message.
	 *
	 * @param string The message.
	 */
	protected function log($message) {
		if ($this->settings['debug']) {
			print_r($message);
			echo PHP_EOL;
			
			$this->log[] = $message;
		}
	}
	
	/**
	 * Log an error.
	 *
	 * @param string The error message.
	 */
	protected function error($message) {
		print_r($message);
		echo PHP_EOL;
		
		$this->errors[] = $message;
	}
	
	/**
	 * Check for and log sql errors.
	 */
	protected function logSQLError() {
		$this->log('SQL-query: ' . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		
		$error = $GLOBALS['TYPO3_DB']->sql_error();
		if (!empty($error)) {
			$this->error('SQL-error: ' . $error);
		}
	}
	
	/**
	 * Set the settings.
	 *
	 * @param array $settings The settings.
	 * @return void
	 */
	public function setSettings(array $settings = null) {
		$this->settings = $settings;
	}
	
	/**
	 * Get the settings.
	 *
	 * @return array The settings.
	 */
	public function getSettings() {
		return $this->settings;
	}
	
	/**
	 * Returns all folders/files in a given folder (not recursive).
	 *
	 * @param string The folder.
	 * @return array Associative array with keys 'folders', holding an array of contained folders, and 'files', holding an array of contained files.
	 */
	protected function resolvePhysical($folder) {
		$files = array();
		$folders = array();
		if (is_dir($folder)) {
			$folder = preg_replace('#/$#', '', $folder) . '/';
			
			$dh  = opendir($folder);
			while (false !== ($filename = readdir($dh))) {
				if ($filename == '.' || $filename == '..') {
					// ignore
				} else if (is_dir($folder . $filename)) {
					$folders[$filename] = $filename;
				} else {
					$files[$filename] = $filename;
				}
			}
		}
		
		return array('folders' => $folders, 'files' => $files);
	}

	/**
	 * This method adds the root pid/page to the title.
	 *
	 * @return	string	Information to display
	 */
	public function getAdditionalInformation() {
		return $this->settings['root_folder'];
	}
}

?>
