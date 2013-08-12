<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Navigation_Frontend
{
    public static function getTree($idcat = null, $level = 1)
    {
        $idlang = Aitsu_Registry::get()->env->idlang;
        $user = Aitsu_Adm_User::getInstance();
        $currentCat = Aitsu_Registry::get()->env->idcat;

        return self::_getCategorieChilds($idcat, $level, $idlang, $user, $currentCat);
    }

    private static function _getCategorieChilds($idcat, $level, $idlang, $user, $currentCat)
    {
        $categories = Moraso_Db::fetchAll('' .
                        'SELECT ' .
                        '   o.idcat, ' .
                        '   catlng.name, ' .
                        '   catlng.claim, ' .
                        '   catlng.public AS isPublic, ' .
                        '   IF (o.lft + 1 = o.rgt, false, true) AS hasChildren, ' .
                        '   IF (catlng.public, true, false) AS isAccessible, ' .
                        '   IF (child.idcat = o.idcat, true, false) AS isCurrent, ' .
                        '   IF (child.idcat IS NULL, false, IF(child.idcat = o.idcat, false, true)) AS isParent, ' .
                        '   COUNT(p.idcat)-1 AS level ' .
                        'FROM ' .
                        '   _cat AS n, ' .
                        '   _cat AS p, ' .
                        '   _cat AS o ' .
                        'LEFT JOIN ' .
                        '   _cat_lang AS catlng ON (catlng.idcat = o.idcat AND catlng.idlang =:idlang) ' .
                        'LEFT JOIN ' .
                        '   _art_lang AS artlng ON artlng.idartlang = catlng.startidartlang ' .
                        'LEFT JOIN ' .
                        '   _cat AS child ON (child.idcat =:currentCat AND child.lft BETWEEN o.lft AND o.rgt) ' .
                        'WHERE ' .
                        '   o.lft BETWEEN p.lft AND p.rgt ' .
                        'AND ' .
                        '   o.lft BETWEEN n.lft AND n.rgt ' .
                        'AND ' .
                        '   n.idcat =:id ' .
                        'AND ' .
                        '   artlng.online =:online ' .
                        'AND ' .
                        '   catlng.visible =:visible ' .
                        'GROUP BY ' .
                        '   o.lft ' .
                        'HAVING ' .
                        '   level =:level ' .
                        'ORDER BY ' .
                        '   o.lft ASC', array(
                    ':id' => $idcat,
                    ':level' => $level,
                    ':idlang' => $idlang,
                    ':online' => 1,
                    ':visible' => 1,
                    ':currentCat' => $currentCat
        ));

        foreach ($categories as &$category) {
            $category['isPublic'] = (bool) $category['isPublic'];
            $category['hasChildren'] = (bool) $category['hasChildren'];
            $category['isAccessible'] = (bool) $category['isAccessible'];
            $category['isCurrent'] = (bool) $category['isCurrent'];
            $category['isParent'] = (bool) $category['isParent'];

            if (!$category['isPublic'] && $user != null) {
                $category['isAccessible'] = $user->isAllowed(array(
                    'language' => $idlang,
                    'resource' => array(
                        'type' => 'cat',
                        'id' => $category['idcat']
                    )
                ));
            }

            if (!$category['isAccessible']) {
                unset($category);
                continue;
            }
            
            if ($category['hasChildren']) {
                $category['children'] = self::_getCategorieChilds($category['idcat'], $category['level'] + 1, $idlang, $user, $currentCat);

                if (empty($category['children'])) {
                    $category['hasChildren'] = false;
                } 
            }

            unset($category['isPublic']);
            unset($category['isAccessible']);
            unset($category['level']);
        }

        return $categories;
    }
}