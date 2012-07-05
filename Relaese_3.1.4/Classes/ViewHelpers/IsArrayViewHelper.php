<?php

/**
 * This class is a demo view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Libconnect_ViewHelpers_IsArrayViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns true if $value is array, false otherwise.
     *
     * @param mixed $value
     * @return boolean
     */
    public function render($value) {
        return is_array($value);
    }
}

?>