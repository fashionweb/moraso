<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Status {

    public static function version() {

        $version = '$version/1.13.0/revision/8$';

        return str_replace(array(
            'version/',
            '/revision/',
            '$'
                ), array(
            '',
            '-',
            ''
                ), $version);
    }

}