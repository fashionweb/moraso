<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$t = Zend_Registry :: get('Zend_Translate');
		try {
			$nav = array (
				array (
					'label' => $t->translate('Dashboard'),
					'id' => 'dashboard',
					'controller' => 'index',
					'action' => 'index',
					'route' => 'default',
					'icon' => 'tm-dashboard'
				),
				array (
					'label' => 'Content',
					'id' => 'page',
					'controller' => 'data',
					'action' => 'index',
					'route' => 'default',
					'ac' => array (
						'area' => 'article'
					),
					'icon' => 'tm-page'
				),
				array (
					'label' => $t->translate('Management'),
					'id' => 'management',
					'controller' => 'acl',
					'action' => 'profil',
					'route' => 'default',
					'ac' => array (
						'area' => 'management'
					),
					'icon' => 'tm-management',
					'pages' => array (
						array (
							'label' => $t->translate('User profile'),
							'id' => 'management',
							'controller' => 'acl',
							'action' => 'profil',
							'route' => 'default',
							'ac' => array (
								'area' => 'userprofile'
							),
							'icon' => 'tm-user-profile'
						),
						array (
							'label' => $t->translate('User, Roles and Privileges'),
							'id' => 'acl',
							'controller' => 'acl',
							'action' => 'index',
							'route' => 'default',
							'ac' => array (
								'area' => 'usermanagement',
								'action' => 'crud'
							),
							'icon' => 'tm-usermanagement'
						),
						array (
							'label' => $t->translate('Clients'),
							'id' => 'client',
							'controller' => 'client',
							'action' => 'index',
							'route' => 'default',
							'ac' => array (
								'area' => 'client'
							),
							'icon' => 'tm-client'
						),
						array (
							'label' => $t->translate('Mappings'),
							'id' => 'mapping',
							'controller' => 'mapping',
							'action' => 'index',
							'route' => 'default',
							'ac' => array (
								'area' => 'mapping'
							),
							'icon' => 'tm-mapping'
						),
						array (
							'label' => $t->translate('Configurations'),
							'id' => 'configs',
							'controller' => 'config',
							'action' => 'index',
							'route' => 'default',
							'ac' => array (
								'area' => 'config'
							),
							'icon' => 'tm-configuration'
						),
						array (
							'label' => $t->translate('Translations'),
							'id' => 'translation',
							'controller' => 'translation',
							'action' => 'index',
							'route' => 'default',
							'ac' => array (
								'area' => 'translation'
							),
							'icon' => 'tm-translation'
						),
						array (
							'label' => $t->translate('Scripts'),
							'id' => 'scripts',
							'controller' => 'script',
							'action' => 'index',
							'route' => 'default',
							'ac' => array (
								'area' => 'script'
							),
							'icon' => 'tm-script'
						)
					)
				),
				array (
					'label' => 'System',
					'id' => uniqid(),
					'controller' => 'plugins',
					'action' => 'index',
					'route' => 'default',
					'pages' => $this->_getPluginNav(),
					'ac' => array (
						'area' => 'plugins'
					),
					'icon' => 'tm-plugin'
				),
				array (
					'label' => $t->translate('Logout'),
					'id' => 'logout',
					'controller' => 'acl',
					'action' => 'logout',
					'route' => 'default'
				)
			);
			$nav = new Zend_Navigation($nav);
			Zend_Registry :: set('nav', $nav);
		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}

	}

	protected function _getPluginNav() {

		$user = Aitsu_Adm_User :: getInstance();

		$dir = APPLICATION_PATH . '/plugins/generic';

		$files = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/generic', '.description.txt');
		sort($files);
		$baseLength = strlen(APPLICATION_PATH . '/plugins/generic/');
		$plugins = array ();
		foreach ($files as $file) {
			$content = explode("\n", file_get_contents($file));
			$file = substr($file, $baseLength);
			$file = explode('/', $file);
			if (count($file) == 2) {
				if ($user != null && $user->isAllowed(array (
						'area' => 'plugin.' . $file[0]
					))) {
					$plugins[] = array (
						'label' => trim($content[0]),
						'id' => uniqid(),
						'controller' => 'plugins',
						'action' => 'index',
						'params' => array (
							'area' => $file[0]
						),
						'route' => 'plugins',
						'pages' => array (),
						'icon' => isset($content[2]) ? $content[2] : ''
					);
				}
			}
			elseif (count($file) == 3) {
				if ($user != null && $user->isAllowed(array (
						'area' => 'plugin.' . $file[0] . '.' . $file[1]
					))) {
					$plugins[count($plugins) - 1]['pages'][] = array (
						'label' => trim($content[0]),
						'id' => uniqid(),
						'controller' => 'plugin',
						'action' => 'index',
						'params' => array (
							'area' => $file[0],
							'plugin' => $file[1],
							'paction' => 'index'
						),
						'route' => 'plugin',
						'icon' => isset($content[2]) ? $content[2] : ''
					);
				}
			}
		}

		return $plugins;
	}
}