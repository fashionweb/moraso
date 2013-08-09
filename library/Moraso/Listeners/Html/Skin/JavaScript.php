<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Skin_JavaScript implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$skin_js = (object) array();
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				$skin_js->top = (object) array_merge((array) $skin_js->top, (array) $json_file_content->js->top);
				$skin_js->bottom = (object) array_merge((array) $skin_js->bottom, (array) $json_file_content->js->bottom);
			}
		}

		$topJS = array();
		$bottomJS = array();

		$topJS[] = "\n\t\t<!-- JavaScript (Top) :: Start -->\n\t";
		$bottomJS[] = "\n\t\t<!-- JavaScript (Bottom) :: Start -->\n\t";

		foreach ($skin_js->top as $key => $value) {
			if (strpos($value, '/') !== 0) {
				$topJS[$key] = "\t" . '<script src="/skin/' . $value . '"></script>' . "\n\t";
			} else {
				$topJS[$key] = "\t" . '<script src="' . $value . '"></script>' . "\n\t";
			}
		}
		
		foreach ($skin_js->bottom as $key => $value) {
			if (strpos($value, '/') !== 0) {
				$bottomJS[$key] = "\t" . '<script src="/skin/' . $value . '"></script>' . "\n\t";
			} else {
				$bottomJS[$key] = "\t" . '<script src="' . $value . '"></script>' . "\n\t";
			}
		}

		$topJS[] = "\t<!-- JavaScript (Top) :: End -->\n";

		$bottomJS[] = "\t<!-- JavaScript (Bottom) :: End -->\n";

		$event->bootstrap->pageContent = str_replace("</head>", implode('', $topJS) . "\t</head>", $event->bootstrap->pageContent);
		$event->bootstrap->pageContent = str_replace("</body>", implode('', $bottomJS) . "\t</body>", $event->bootstrap->pageContent);
	}
}