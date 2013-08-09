<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Skin_GoogleFonts implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$google_fonts = (object) array();
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				$google_fonts = (object) array_merge((array) $google_fonts, (array) $json_file_content->google->fonts);
			}
		}

		$fonts = array();
		$fonts[] = "\n\t\t<!-- GoogleFonts :: Start -->\n";
		foreach ($google_fonts as $font => $width) {
			if (!empty($width)) {
				$fonts[] = "\t\t" . "<link href='http://fonts.googleapis.com/css?family=" . $font . ":" . $width . "' rel='stylesheet' type='text/css'>" . "\n\t";
			}
		}
		$fonts[] = "\t<!-- GoogleFonts :: End -->\n";

		$event->bootstrap->pageContent = str_replace("</head>", implode('', $fonts) . "\t</head>" , $event->bootstrap->pageContent);
	}
}