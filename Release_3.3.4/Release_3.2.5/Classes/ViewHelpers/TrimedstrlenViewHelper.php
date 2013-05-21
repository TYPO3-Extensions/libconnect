<?php

/**
 * This class is a demo view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Libconnect_ViewHelpers_TrimedstrlenViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns trimed string
     *
     * @param string $string
     * @return string
     */
    public function render($string) {
        return strlen(trim($string));
    }
}

?>