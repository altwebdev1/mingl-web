<?php

/**
 * Helper functions for RedBean
 * @author eventurers
 */

namespace Helpers;

/**
 * using some much needed objects
 */
use RedBean_OODBBean;

class RedBeanHelper {
    /**
     * Returns an array of exported beans
     * @param array $beans
     * @param array $keysToExclude
     * @return array
     */
    public static function exportAll($beans, $keysToExclude = null) {
        $answer = [];
        foreach($beans as $a){
            $item = RedBeanHelper::export($a, $keysToExclude);
            $answer[] = $item;
        }
        return $answer;
    }

    /**
     * Returns an array representation of a bean
     * @param RedBean_OODBBean $bean
     * @param array $keysToExclude
     * @return array
     */
    public static function export($bean, $keysToExclude = null){
        $result = $bean->export();
        if($keysToExclude)
            $result = ArrayHelper::array_cleanup($result, $keysToExclude);
        //$result = array_intersect_key($result, array_flip($keys));
        return $result;
    }
}