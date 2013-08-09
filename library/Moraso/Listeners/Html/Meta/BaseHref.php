<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_BaseHref implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$baseHref = "\n\t\t<!-- BaseHref :: Start -->\n\t\t<base href=\"" . Moraso_Config::get('sys.webpath') . "\" />\n\t\t<!-- BaseHref :: End -->\n";
		
		$event->bootstrap->pageContent = str_replace("<head>", "<head>\t" . $baseHref, $event->bootstrap->pageContent);
	}
}