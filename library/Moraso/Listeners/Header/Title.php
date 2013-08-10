<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_Title implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$heredity = Moraso_Skin_Heredity::build();

		$prefix = '';
		$suffix = '';
		foreach (array_reverse($heredity) as $skin) {
			$json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

			if (is_readable($json_file_dest)) {
				$json_file_content = json_decode(file_get_contents($json_file_dest));

				if (isset($json_file_content->pagetitle->prefix) && !empty($json_file_content->pagetitle->prefix)) {
					$prefix = $json_file_content->pagetitle->prefix;
				}

				if (isset($json_file_content->pagetitle->suffix) && !empty($json_file_content->pagetitle->suffix)) {
					$suffix = $json_file_content->pagetitle->suffix;
				}
			}
		}

		if (!empty($prefix) || !empty($suffix)) {
			$pageTitle = Aitsu_Core_Article::factory()->pagetitle;

			Aitsu_Registry::get()->header->title = (object) array(
				"name" => "Title",
				"tag" => '<title>' . $prefix . $pageTitle . $suffix . '</title>'
				);
		}
	}
}