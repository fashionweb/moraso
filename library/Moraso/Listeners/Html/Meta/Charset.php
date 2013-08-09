<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_Charset implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$charset = '';

		$heredity = Moraso_Skin_Heredity::build();

		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->charset) && !empty($json_file_content->charset)) {
					$charset = $json_file_content->charset;
				}
			}
		}
		
		if (!empty($charset)) {
			$event->bootstrap->pageContent = str_replace("<head>", "<head>\n\t\t" . '<!-- Charset :: Start -->' . "\n\t\t" . '<meta charset="' . $charset . '" />' . "\n\t\t" . '<!-- Charset :: End -->' . "\n", $event->bootstrap->pageContent);
		}
	}
}