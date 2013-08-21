<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_SortcutIcon implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$shortcut_icon = '';
		foreach ($heredity as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->shortcut_icon) && !empty($json_file_content->shortcut_icon)) {
					$shortcut_icon = $json_file_content->shortcut_icon;
					break;
				}
			}
		}

		if (!empty($shortcut_icon)) {
			Aitsu_Registry::get()->header->shortcut_icon = (object) array(
				"name" => "ShortcutIcon",
				"tag" => "<link rel=\"shortcut icon\" href=\"" . $shortcut_icon . "\" />"
				);
		}
	}
}