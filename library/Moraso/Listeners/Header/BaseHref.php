<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header_BaseHref implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		Aitsu_Registry::get()->header->base_href = (object) array(
			"name" => "BaseHref",
			"tag" => "<base href=\"" . Moraso_Config::get('sys.webpath') . "\" />"
			);
	}
}