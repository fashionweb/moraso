<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_Viewport implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$viewport = '';

		$heredity = Moraso_Skin_Heredity::build();

		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->viewport) && !empty($json_file_content->viewport)) {
					$viewport = $json_file_content->viewport;
				}
			}
		}
		
		$event->bootstrap->pageContent = str_replace("<head>", "<head>\n\t\t" . '<!-- Viewport :: Start -->' . "\n\t\t" . '<meta name="viewport" content="' . $viewport . '" />' . "\n\t\t" . '<!-- Viewport :: End -->' . "\n", $event->bootstrap->pageContent);
	}
}