<?php
/**
 * This class is a view helper that returns an value of an sub array
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class Tx_Libconnect_ViewHelpers_ArrayViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
    * Get the array value from given key
    *
    * @param array $inputArray
    * @param string $key
    * @return string
    */
    public function render($inputArray = array(), $key = '0') {
        if (is_array($inputArray)) {
            foreach ($inputArray as $value) {
                if($value == $key){
                    return $value;
                }
            }
        }
    }
}
?>