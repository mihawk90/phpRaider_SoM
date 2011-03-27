<?php

/**
 * File:        validate_transform.strip_tags.php
 * Author:      Espen Carlsen
 *
*/

 /**
 * transform fuction, strip html tags from a value
 *
 * @param string $value the value being stripped
 * @param array  $params the parameters passed to the transform function
 * @param array  $formvars the form variables
 */

function smarty_validate_transform_strip_tags($value, $params, &$formvars) {
    return strip_tags($value);
}

?>
