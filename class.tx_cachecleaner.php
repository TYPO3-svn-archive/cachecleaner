<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2009 Francois Suter (typo3@cobweb.ch)
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
 * Base class which performs the actual cache cleaning
 *
 * @author	Francois Suter <typo3@cobweb.ch>
 *
 *  $Id$
 */
class tx_cachecleaner {
	protected $extKey = 'cachecleaner';	// The extension key
	protected $extConf = array(); // The extension configuration
	protected $cleanerConfiguration = array(); // The cache cleaning configuration

	/**
	 * Constructor
	 * The constructor just reads the extension configuration and stores it in a member variable
	 */
	public function __construct() {
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

			// If no cleaning configuration exists, load the default one
			// TODO: remove this when finished testing
		if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['tables'])) {
			require_once(t3lib_extMgm::extPath($this->extKey, 'configuration_default.php'));
		}
		$this->cleanerConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['tables'];
	}

	/**
	 * This method counts the number of records that could be deleted, given the configuration
	 * and returns information about this
	 *
	 * @return	array	List of informational messages, one per table
	 */
	public function analyzeTables() {
		$results = array();
			// Loop on all configured tables
		foreach ($this->cleanerConfiguration as $table => $tableConfiguration) {
				// Handle tables that have an explicit expiry field
			if (isset($tableConfiguration['expireField'])) {
				$field = $tableConfiguration['expireField'];
				$dateLimit = $GLOBALS['EXEC_TIME'];

					// Handle tables with a date field and a lifetime
			} elseif (isset($tableConfiguration['dateField'])) {
				$field = $tableConfiguration['dateField'];
				$dateLimit = $GLOBALS['EXEC_TIME'] - (7 * 86400);
			}
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*) AS total', $table, $field . " <= '" . $dateLimit . "'");
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
				$results[] = sprintf($GLOBALS['LANG']->getLL('recordsToDelete'), $table, $row[0]);
		}
		return $results;
	}

	/**
	 * This is the main method that clears expired records from the database
	 */
	public function cleanTables() {
	}
}
?>