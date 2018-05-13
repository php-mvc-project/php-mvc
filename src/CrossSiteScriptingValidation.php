<?php
namespace PhpMvc;

/**
 * Detection of unsafe strings from the client.
 */
final class CrossSiteScriptingValidation {

    /**
     * Checks whether the specified line is safe or not.
     * 
     * @param string $value The value to check.
     * 
     * @return bool
     */
    public static function IsDangerousString($value) {
        return preg_match('/(\x3c([A-Za-z\x21\x2f\x3f]+))|(\x26\x23)/', $value) === 1;
    }

}