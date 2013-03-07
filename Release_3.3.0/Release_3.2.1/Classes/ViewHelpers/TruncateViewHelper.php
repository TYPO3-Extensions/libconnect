<?php

/**
 * This class is a demo view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Libconnect_ViewHelpers_TruncateViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns truncated string
     *
     * @param string $string
     * @param length $int
     * @return string
     */
    public function render($string, $length) {
        return mb_substr($string, 0, $length);
    }
}

?>