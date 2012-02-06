<?php

/**
 * This class is a demo view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Libconnect_ViewHelpers_DbisViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

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