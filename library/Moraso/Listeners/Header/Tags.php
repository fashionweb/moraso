<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_Tags implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$metas = Moraso_Db::simpleFetch('all', '_art_meta', array('idartlang' => Aitsu_Registry::get()->env->idartlang));

		unset($metas['idartlang']);

		if (!empty($metas)) {
			$metaTags = array();
			foreach ($metas as $name => $content) {
				if (!empty($content)) {
					$metaTags[] = '<meta name="' . $name . '" content="' . $content . '" />';
				}
			}

			Aitsu_Registry::get()->header->meta_tags = (object) array(
				"name" => "Meta Tags",
				"tags" => $metaTags
				);
		}
	}
}