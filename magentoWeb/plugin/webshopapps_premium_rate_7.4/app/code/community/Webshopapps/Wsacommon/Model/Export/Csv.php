<?php
/**
 * Magento Webshopapps Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Webshopapps
 * @package    Webshopapps_Wsacommon
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
 */
class Webshopapps_Wsacommon_Model_Export_Csv extends Mage_Core_Model_Abstract
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
    protected $_fileDates = array();
    protected $_dateFileArray = array();
    protected $_theDataArray = array();

    /**
     * Get latest CSV file name and content from var/export
     *
     * @param $website Id of the current store config scope
     * @return Array The name of the CSV and data of the CSV file to be returned
     */
    public function createCSV($website, $carrierCode='nk')
    {
        if (is_dir(Mage::getBaseDir('var') . DS . 'export' . DS)) {
            $directory = Mage::getBaseDir('var') . DS . 'export' . DS;
            $csvFiles = glob($directory . 'WSA*.csv');

            if (!$this->_findCarrierSpecificCsvFiles($directory,$website, $carrierCode)) {
                if (!$this->_findOldFormatCsvFiles($csvFiles,$website)) {

                } else {
                    $this->_findNoWebsiteCsvFiles($csvFiles);
                }
            }

            // If $this->_fileDates is empty return a blank CSV
            if (!isset($this->_fileDates) || empty($this->_fileDates)) {
                return $this->_getNoCsvFilePresentArr();
            }

            $this->_findMostRecentCSV();

        } else {
            // If var/export is not a directory return a blank CSV
            return $this->_getNoCsvFilePresentArr();
        }
        return $this->_theDataArray;
    }


    protected function _findCarrierSpecificCsvFiles($directory,$website, $carrierCode) {
        $carrierSpecificCsvFiles = glob($directory . 'WSA_'.$carrierCode.'_*.csv');
        if (empty($carrierSpecificCsvFiles)) {
            return false;
        }

        return $this->_findCsvFile($carrierSpecificCsvFiles,$website);

    }

    protected function _findOldFormatCsvFiles($csvFiles,$website) {
        if (empty($csvFiles)) { // If no WSA*.csv files found, return blank CSV
            return false;
        }
        return $this->_findCsvFile($csvFiles,$website);
    }


    protected function _findCsvFile($csvFiles,$website) {

        $found = false;
        foreach ($csvFiles as $file) {
            $file = basename($file);
            $posOfId = strpos($file, 'Id=');
            $websiteId = substr($file, $posOfId+3, 1);

            // Get files for the current website config scope
            if ($website == $websiteId) {
                $this->timeSortSetup($file);
                $found = true;
            }

        }

        return $found;
    }


    /**
     * Loops through csv files and sets up $_dateFileArray & $_theDataArray
     *
     * @param $csvFiles Array of csv files in var/export
     */
    protected function _findNoWebsiteCsvFiles($csvFiles)
    {
        foreach ($csvFiles as $file) {
            $file = basename($file);
            $this->timeSortSetup($file);
        }
    }

    /**
     * Get most recent csv, read data and assign to $this->$theData
     *
     * @param $csvFiles Array of csv files in var/export.
     */
    protected function _findMostRecentCSV()
    {
        // Get file with the most recent timestamp
        array_multisort($this->_fileDates, SORT_DESC);
        $mostRecent = $this->_fileDates[0];
        $mostRecentCSV = $this->_dateFileArray[$mostRecent];
        $fullFileName = Mage::getBaseDir('var') . DS . 'export' . DS . $mostRecentCSV;

        if (is_file($fullFileName)) {
            $theData = array(
                'type'  => 'filename',
                'value' => $fullFileName,
                'rm'    => false // can delete file after use
            );
        } else {
            $theData = $this->_noCSVPresent($fullFileName);
            return $theData;
        }

        $this->_theDataArray = array($mostRecentCSV, $theData);
    }

    /**
     * Sets up $this->_fileDates & $this->_dateFileArray to be sorted
     *
     * @param $csvFiles Array of csv files in var/export.
     */
    protected function timeSortSetup($file)
    {
        $currentModified = filectime(Mage::getBaseDir('var') . DS . 'export' . DS. $file);
        $this->_fileDates[] = $currentModified;
        $this->_dateFileArray[$currentModified] = $file;
    }

    /**
     * Assigns blank CSV file to $this->_theDataArray and posts a log
     *
     * @param $dir Location of var/export.
     */
    protected function _getNoCsvFilePresentArr()
    {
        $dir = Mage::getBaseDir('var') . DS . 'export' . DS;
        Mage::helper('wsacommon/log')->postMajor('WSA Helper','No file found in var/export with the name:', $dir);
        return array('', 'blank');
    }

}
?>
