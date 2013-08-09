<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Skin_Css implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$css_collection = (object) array();
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				$css_collection = (object) array_merge((array) $css_collection, (array) $json_file_content->css);
			}
		}

		$css = array();

		if (!empty($css_collection)) {		
			$css[] = "\n\t\t<!-- CSS :: Start -->\n\t";
			foreach ($css_collection as $key => $value) {
				$css[$key] = "\t" . '<link rel="stylesheet" href="/skin/' . $value . '" />' . "\n\t";
			}
			$css[] = "\t<!-- CSS :: End -->\n";

			$event->bootstrap->pageContent = str_replace("</head>", implode('', $css) . "\t</head>", $event->bootstrap->pageContent);
		}
	}
}