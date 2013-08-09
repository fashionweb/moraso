<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_Tags implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$metas = Moraso_Db::simpleFetch('all', '_art_meta', array('idartlang' => Aitsu_Registry::get()->env->idartlang));

		unset($metas['idartlang']);

		$metaTags = array();
		$metaTags[] = "\n\t\t<!-- Meta Tags :: Start -->\n";
		foreach ($metas as $name => $content) {
			if (!empty($content)) {
				$metaTags[] = "\t\t" . '<meta name="' . $name . '" content="' . $content . '" />' . "\n\t";
			}
		}
		$metaTags[] = "\t<!-- Meta Tags :: End -->\n";

		$event->bootstrap->pageContent = str_replace("<head>", "<head>\t" . implode('', $metaTags), $event->bootstrap->pageContent);
	}
}