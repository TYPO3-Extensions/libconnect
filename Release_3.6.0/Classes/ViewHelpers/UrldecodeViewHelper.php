<?php
/**
 * This class is a view helper that returns a urldecoded string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class Tx_Libconnect_ViewHelpers_UrldecodeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns urldecoded string
     *
     * @param string $url
     * @return string
     */
    public function render($url) {
        return urldecode($url);
    }
}
?>