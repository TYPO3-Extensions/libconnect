<?php
/**
 * This class is a view helper that convert a variable in Integer.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class Tx_Libconnect_ViewHelpers_IntvalViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns the parameter as integer
     *
     * @param mixed $value
     * @return int
     */
    public function render($wert) {
        return intval($wert);
    }
}
?>