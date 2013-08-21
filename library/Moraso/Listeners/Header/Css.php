<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_Css implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$css_collection = new stdClass();
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->css) && !empty($json_file_content->css)) {
					$css_collection = (object) array_merge((array) $css_collection, (array) $json_file_content->css);
				}
			}
		}

		$css = array();
		if (count((array) $css_collection) > 0) {		
			foreach ($css_collection as $key => $value) {
				if (strpos($value, '//') !== 0 && strpos($value, '<') !== 0 && strpos($value, 'http') !== 0) {
					$css[$key] = '<link rel="stylesheet" href="/skin/' . $value . '" />';
				} else {
					if (strpos($value, 'http') === 0 || strpos($value, '//') === 0) {
						$css[$key] = '<link rel="stylesheet" href="' . $value . '" />';
					} else {
						$css[$key] = $value;
					}
				}
			}

			if (!empty($css)) {
				Aitsu_Registry::get()->header->css = (object) array(
					"name" => "CSS",
					"tags" => $css
					);
			}
		}
	}
}