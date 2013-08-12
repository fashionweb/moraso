<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Status
{
    public static function version()
    {
        $version = '$/major/1/minor/34/revision/0/build/138$';

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

    public static function latestVersion()
    {
        $applicationStatus = file_get_contents('https://bitbucket.org/webtischlerei/moraso-cms/raw/master/library/Moraso/Status.php');

        $matches = array();
        preg_match_all('@\'\\$/major/(\\d+)/minor/(\\d+)/revision/(\\d+)/build/(\\d+)\\$\'@', $applicationStatus, $matches);

        return $matches[1][0] . '.' . $matches[2][0] . '.' . $matches[3][0] . '-' . $matches[4][0];
    }
}