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

require_once(t3lib_extMgm::extPath('cachecleaner', 'class.tx_cachecleaner.php'));

/** 
 * Base class wrapper for the lowlevel cleaner module
 *
 * @author		Francois Suter <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_cachecleaner
 *
 *  $Id$
 */
class tx_cachecleaner_lowlevel extends tx_lowlevel_cleaner_core {
	protected $extKey = 'cachecleaner';	// The extension key
	protected $extConf = array(); // The extension configuration
	/**
	 * Instance of the main cache cleaner class
	 * 
	 * @var tx_cachecleaner
	 */
	protected $cleaner;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::tx_lowlevel_cleaner_core();

			// Instantiate the cleaner itself
		$this->cleaner = t3lib_div::makeInstance('tx_cachecleaner');

			// Load the language file and set base messages for the lowlevel interface
		$GLOBALS['LANG']->includeLLFile('EXT:' . $this->extKey . '/locallang.xml');
		$this->cli_help['name'] = $GLOBALS['LANG']->getLL('name');
		$this->cli_help['description'] = trim($GLOBALS['LANG']->getLL('description'));
		$this->cli_help['author'] = 'Francois Suter, (c) 2009';
		$this->cli_options[] = array('--optimize', $GLOBALS['LANG']->getLL('options.optimize'));
	}

	public function main() {
			// Initialize result array
		$resultArray = array(
			'message' => $this->cli_help['name'] . chr(10) . chr(10) . $this->cli_help['description'],
			'headers' => array(
				'CACHE_TO_CLEAN' => array(
					$GLOBALS['LANG']->getLL('cleantest.header'), $GLOBALS['LANG']->getLL('cleantest.description'), 1
				)
			),
			'CACHE_TO_CLEAN' => array()
		);
		$result = $this->cleaner->analyzeTables();
		$resultArray['CACHE_TO_CLEAN'] = $result;
		return $resultArray;
	}
}
?>