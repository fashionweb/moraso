<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_Generator implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        if (!isset ($event->bootstrap->pageContent)) {
            return;
        }

        $matches = array();
        preg_match_all('@\'\\$/major/(\\d+)/minor/(\\d+)/revision/(\\d+)/build/(\\d+)\\$\'@', file_get_contents(LIBRARY_PATH . '/Moraso/Status.php'), $matches);

        $version = array(
            'major' => $matches[1][0],
            'minor' => $matches[2][0],
            'revision' => $matches[3][0],
            'build' => $matches[4][0]
            );

        Aitsu_Registry::get()->header->generator = (object) array(
            "name" => "Generator",
            "tag" => "<meta name=\"generator\" content=\"moraso cms - v" . $version['major'] . "." . $version['minor'] . "." . $version['revision'] . "-" . $version['build'] . "\" />"
            );
    }
}