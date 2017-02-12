<?php

/**
*
*/
function dump($mixed = null) {
    echo '<pre>';
    var_dump($mixed);
    echo '</pre>';
}


/**
*
*/
function sanitize($mixed = null) {

    if(!is_array($mixed)) {
        return convertHtmlEntities($mixed);
    }

    function array_map_recursive($callback, $array) {
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
        };

        return array_map($func, $array);
    }

    return array_map_recursive('convertHtmlEntities', $mixed);

}


function convertHtmlEntities($mixed) {
    return htmlentities($mixed, ENT_QUOTES, "UTF-8");
}
