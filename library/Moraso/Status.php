<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Status
{
    public static function version()
    {
        $version = '$/major/1/minor/27/revision/4/build/77$';

        return str_replace(array(
            '/major/',
            '/minor/',
            '/revision/',
            '/build/',
            '$'
                ), array(
            '',
            '.',
            '.',
            '-',
            ''
                ), $version);
    }

}