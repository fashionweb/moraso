<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_GoogleFonts implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$google_fonts = new stdClass();
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->google->fonts) && !empty($json_file_content->google->fonts)) {
					$google_fonts = (object) array_merge((array) $google_fonts, (array) $json_file_content->google->fonts);
				}
			}
		}

		if (count((array) $google_fonts) > 0) {		
			$fonts = array();
			foreach ($google_fonts as $font => $width) {
				if (!empty($width)) {
					$fonts[] = "<link href='http://fonts.googleapis.com/css?family=" . $font . ":" . $width . "' rel='stylesheet' type='text/css'>";
				}
			}

			if (!empty($fonts)) {
				Aitsu_Registry::get()->header->google_fonts = (object) array(
					"name" => "GoogleFonts",
					"tags" => $fonts
					);
			}
		}
	}
}