<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_Charset implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$charset = '';
		foreach ($heredity as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->charset) && !empty($json_file_content->charset)) {
					$charset = $json_file_content->charset;
					break;
				}
			}
		}
		
		if (!empty($charset)) {
			Aitsu_Registry::get()->header->charset = (object) array(
				"name" => "Charset",
				"tag" => "<meta charset=\"" . $charset . "\" />"
				);
		}
	}
}