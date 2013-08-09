<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Meta_CanonicalTag implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$art = Aitsu_Persistence_Article::factory(Aitsu_Registry::get()->env->idart, Aitsu_Registry::get()->env->idlang)->load();

		$base = substr(Aitsu_Config::get('sys.webpath'), 0, -1);
		$canonicalPath = Aitsu_Config::get('sys.canonicalpath');
		if ($canonicalPath != null) {
			$base = substr(Aitsu_Config::get('sys.canonicalpath'), 0, -1);
		}

		if ($art->idcat == Aitsu_Config::get('sys.startcat')) {
			if (Aitsu_Config::get('rewrite.uselang')) {
				$language = Aitsu_Persistence_Language::factory(Aitsu_Registry::get()->env->idlang)->name;
				$href = $base . '/' . $language . '/';
			} else {
				$href = $base . '/';
			}
		} elseif ($art->startidartlang == $art->idartlang) {
			$href = '{ref:idcat-' . $art->idcat . '}';
		} else {
			$href = '{ref:idart-' . $art->idart . '}';
		}

		$canonicalTag = "\n\t\t<!-- CanonicalTag :: Start -->\n\t\t<link rel=\"canonical\" href=\"" . $href . "\" />\n\t\t<!-- CanonicalTag :: End -->\n";
		
		$event->bootstrap->pageContent = str_replace("<head>", "<head>\t" . $canonicalTag, $event->bootstrap->pageContent);
	}
}