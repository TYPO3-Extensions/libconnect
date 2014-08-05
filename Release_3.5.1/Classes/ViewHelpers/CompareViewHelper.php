<?php
/**
 * This class is a view helper that compare variables.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class Tx_Libconnect_ViewHelpers_CompareViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns true if $a and $b are type-equal, false otherwise.
     *
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function render($a, $b) {
        return $a === $b;
    }
}
?>