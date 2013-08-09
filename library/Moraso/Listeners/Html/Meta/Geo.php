<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_Geo implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$geoTags = (object) array();
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				$geoTags = (object) array_merge((array) $geoTags, (array) $json_file_content->geo);
			}
		}

		$geo = array();

		if (count((array) $geoTags) > 0 {		
			$geo[] = "\n\t\t<!-- Meta Geo Tags :: Start -->\n\t";
			foreach ($geoTags as $key => $value) {
				$geo[$key] = "\t" . '<meta name="' . $key . '" content="' . $value . '" />' . "\n\t";
			}
			$geo[] = "\t<!-- Meta Geo Tags :: End -->\n";

			$event->bootstrap->pageContent = str_replace("</head>", implode('', $geo) . "\t</head>", $event->bootstrap->pageContent);
		}	
	}
}