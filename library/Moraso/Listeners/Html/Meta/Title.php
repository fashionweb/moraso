<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_Title implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$prefix = '';
		$suffix = '';

		$heredity = Moraso_Skin_Heredity::build();

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

		$pageTitle = Aitsu_Core_Article::factory()->pagetitle;
		
		$event->bootstrap->pageContent = str_replace("<head>", "<head>\n\t\t" . '<!-- Title :: Start -->' . "\n\t\t" . '<title>' . $prefix . $pageTitle . $suffix . '</title>' . "\n\t\t" . '<!-- Title :: End -->' . "\n", $event->bootstrap->pageContent);
	}
}