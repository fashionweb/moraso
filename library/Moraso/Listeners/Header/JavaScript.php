<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_JavaScript implements Aitsu_Event_Listener_Interface
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
		if (!empty($skin_js->top)) {
			foreach ($skin_js->top as $key => $value) {
				if (strpos($value, '/') !== 0) {
					$topJS[$key] = '<script src="/skin/' . $value . '"></script>';
				} else {
					$topJS[$key] = '<script src="' . $value . '"></script>';
				}
			}

			Aitsu_Registry::get()->header->javascript_top = (object) array(
				"name" => "JavaScript (Top)",
				"tags" => $topJS
				);
		}

		$bottomJS = array();
		if (!empty($skin_js->bottom)) {
			foreach ($skin_js->bottom as $key => $value) {
				if (strpos($value, '/') !== 0) {
					$bottomJS[$key] = '<script src="/skin/' . $value . '"></script>';
				} else {
					$bottomJS[$key] = '<script src="' . $value . '"></script>';
				}
			}

			Aitsu_Registry::get()->header->javascript_bottom = (object) array(
				"name" => "JavaScript (Bottom)",
				"tags" => $bottomJS,
				"position" => "bottom"
				);
		}
	}
}