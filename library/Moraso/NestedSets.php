<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_NestedSets
{
    /**
     * Description ...
     * 
     * @param type $node
     * @param type $parent
     * @param type $next
     * @param type $table
     * @return boolean 
     * 
     * @example Moraso_NestedSets::move(17, 13);
     * @example Moraso_NestedSets::move(17, 0, 14);
     * @example Moraso_NestedSets::move(17, 18, 31, '_example_table');
     * 
     * @example Moraso_NestedSets::move(array('lft' => 10, 'rgt' => 13), 13);
     */
    public static function move($node, $parent, $next = 0, $table = '_cat')
    {
        $primary = self::_getPrimaryKey($table);

        if (!is_array($node)) {
            $node = self::getNodeLftRgt($node, $table, $primary);
        }

        if (empty($parent) && empty($next)) {
            $target = Moraso_Db::fetchOne('select max(rgt) +1 from ' . $table . '');
        } elseif ((!empty($next))) {
            $target = Moraso_Db::fetchOne('select lft from ' . $table . ' where ' . $primary . ' =:id', array(':id' => $next));
        } else {
            $target = Moraso_Db::fetchOne('select rgt from ' . $table . ' where ' . $primary . ' =:id', array(':id' => $parent));
        }

        try {
            $query = '' .
                    'update ' . $table . ' ' .
                    '   set lft = lft + if (:target > :rgt, ' .
                    '       if (:rgt < lft and lft < :target, :lft - :rgt - 1, ' .
                    '           if (:lft <= lft and lft < :rgt, :target - :rgt - 1, 0) ' .
                    '       ), ' .
                    '       if (:target <= lft and lft < :lft, :rgt - :lft + 1, ' .
                    '           if (:lft <= lft and lft < :rgt, :target - :lft, 0) ' .
                    '       ) ' .
                    '   ), ' .
                    '   rgt = rgt + if (:target > :rgt, ' .
                    '       if (:rgt < rgt and rgt < :target, :lft - :rgt - 1, ' .
                    '           if (:lft < rgt and rgt <= :rgt, :target - :rgt - 1, 0) ' .
                    '       ), ' .
                    '       if (:target <= rgt and rgt < :lft, :rgt - :lft + 1, ' .
                    '           if (:lft < rgt and rgt <= :rgt, :target - :lft, 0) ' .
                    '       ) ' .
                    '   ) ' .
                    'where ' .
                    '   :rgt < :target ' .
                    'or ' .
                    '   :target < :lft';

            $search = array(':target', ':lft', ':rgt');
            $replace = array($target, $node['lft'], $node['rgt']);

            Moraso_Db::query(str_replace($search, $replace, $query));
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $table
     * @return boolean 
     * 
     * @example Moraso_NestedSets::delete(17);
     * @example Moraso_NestedSets::delete(17, '_example_table');
     */
    public static function delete($node, $table = '_cat')
    {
        if (!is_array($node)) {
            $node = self::getNodeLftRgt($node, $table);
        }

        try {
            Moraso_Db::query('' .
                    'delete from ' .
                    ' ' . $table . ' ' .
                    'where ' .
                    ' lft between ' . $node['lft'] . ' and ' . $node['rgt']);

            Moraso_Db::query('' .
                    'update ' .
                    ' ' . $table . ' ' .
                    'set ' .
                    ' lft = lft - round((' . $node['rgt'] . ' - ' . $node['lft'] . ' + 1)) ' .
                    'where ' .
                    ' lft > ' . $node['rgt']);

            Moraso_Db::query('' .
                    'update ' .
                    ' ' . $table . ' ' .
                    'set ' .
                    ' rgt = rgt - round((' . $node['rgt'] . ' - ' . $node['lft'] . ' + 1)) ' .
                    'where ' .
                    ' rgt > ' . $node['rgt']);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Description ...
     * 
     * @param array $data
     * @param type $parent
     * @param type $table
     * @return boolean 
     * 
     * @example Moraso_NestedSets::insert(array('name' => 'Example 1'));
     * @example Moraso_NestedSets::insert(array('name' => 'Example 2'), 17);
     * @example Moraso_NestedSets::insert(array('name' => 'Example 3'), 13, '_example_table');
     */
    public static function insert($parent_node_id = null, $public = true, $active = false, $table = '_cat', $primary = null)
    {
        if (empty($primary)) {
            $primary = self::_getPrimaryKey($table);
        }

        if (!empty($parent_node_id)) {
            $parent_node = self::getNodeLftRgt($parent_node_id, $table);
        } else {
            $parent_node = Moraso_Db::fetchRow('select max(rgt) + 1 as rgt from ' . $table);
        }

        Moraso_Db::query('' .
                'update ' .
                '   ' . $table . ' ' .
                'set ' .
                '   rgt = rgt + 2 ' .
                'where ' .
                '   rgt >= :rgt', array(
            ':rgt' => (int) $parent_node['rgt']
        ));

        Moraso_Db::query('' .
                'update ' .
                '   ' . $table . ' ' .
                'set ' .
                '   lft = lft + 2 ' .
                'where ' .
                '   lft >= :rgt', array(
            ':rgt' => (int) $parent_node['rgt']
        ));

        return Moraso_Db::put($table, $primary, array(
                    'public' => (int) $public,
                    'active' => (int) $active,
                    'lft' => (int) $parent_node['rgt'],
                    'rgt' => (int) $parent_node['rgt'] + 1
        ));
    }

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $level
     * @param type $table
     * @return type 
     * 
     * @example Moraso_NestedSets::getSet();
     * @example Moraso_NestedSets::getSet(17, 1);
     * @example Moraso_NestedSets::getSet(21, true, 2, '_example_table');
     */
    public static function getSet($node = null, $active = true, $level = 0, $table = '_cat')
    {
        $primary = self::_getPrimaryKey($table);

        $set = array();

        if (!empty($node)) {
            if ($active) {
                $set = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   o.' . $primary . ', ' .
                                '   o.lft, ' .
                                '   o.rgt, ' .
                                '   IF (o.lft + 1 = o.rgt, 0, 1) AS has_children, ' .
                                '   COUNT(p.' . $primary . ')-1 AS level, ' .
                                '   o.created, ' .
                                '   o.modified ' .
                                'FROM ' .
                                '   ' . $table . ' AS n, ' .
                                '   ' . $table . ' AS p, ' .
                                '   ' . $table . ' AS o ' .
                                'WHERE ' .
                                '   o.lft between p.lft AND p.rgt ' .
                                'AND ' .
                                '   o.active =:active ' .
                                'AND ' .
                                '   o.lft between n.lft AND n.rgt ' .
                                'AND ' .
                                '   n.' . $primary . ' =:id ' .
                                'GROUP BY ' .
                                '   o.lft ' .
                                'HAVING ' .
                                '   level =:level ' .
                                'ORDER BY ' .
                                '   o.lft ASC', array(
                            ':id' => $node,
                            ':level' => $level + 1,
                            ':active' => 1
                ));
            } else {
                $set = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   o.' . $primary . ', ' .
                                '   o.lft, ' .
                                '   o.rgt, ' .
                                '   IF (o.lft + 1 = o.rgt, 0, 1) AS has_children, ' .
                                '   COUNT(p.' . $primary . ')-1 AS level, ' .
                                '   o.created, ' .
                                '   o.modified ' .
                                'FROM ' .
                                '   ' . $table . ' AS n, ' .
                                '   ' . $table . ' AS p, ' .
                                '   ' . $table . ' AS o ' .
                                'WHERE ' .
                                '   o.lft between p.lft AND p.rgt ' .
                                'AND ' .
                                '   o.lft between n.lft AND n.rgt ' .
                                'AND ' .
                                '   n.' . $primary . ' =:id ' .
                                'GROUP BY ' .
                                '   o.lft ' .
                                'HAVING ' .
                                '   level =:level ' .
                                'ORDER BY ' .
                                '   o.lft ASC', array(
                            ':id' => $node,
                            ':level' => $level + 1
                ));
            }
        } else {
            if ($active) {
                $set = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   n.' . $primary . ', ' .
                                '   n.lft, ' .
                                '   n.rgt, ' .
                                '   IF (n.lft + 1 = n.rgt, 0, 1) AS has_children, ' .
                                '   COUNT(*)-1 AS level, ' .
                                '   n.created, ' .
                                '   n.modified ' .
                                'FROM ' .
                                '   ' . $table . ' AS n, ' .
                                '   ' . $table . ' AS p ' .
                                'WHERE ' .
                                '   n.lft between p.lft and p.rgt ' .
                                'AND ' .
                                '   n.active =:active ' .
                                'GROUP BY ' .
                                '   n.' . $primary . ' ' .
                                'HAVING ' .
                                '   level = 0 ' .
                                'ORDER BY ' .
                                '   n.lft ASC', array(
                            ':active' => 1
                ));
            } else {
                $set = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   n.' . $primary . ', ' .
                                '   n.lft, ' .
                                '   n.rgt, ' .
                                '   IF (n.lft + 1 = n.rgt, 0, 1) AS has_children, ' .
                                '   COUNT(*)-1 AS level, ' .
                                '   n.created, ' .
                                '   n.modified ' .
                                'FROM ' .
                                '   ' . $table . ' AS n, ' .
                                '   ' . $table . ' AS p ' .
                                'WHERE ' .
                                '   n.lft between p.lft and p.rgt ' .
                                'GROUP BY ' .
                                '   n.' . $primary . ' ' .
                                'HAVING ' .
                                '   level = 0 ' .
                                'ORDER BY ' .
                                '   n.lft ASC');
            }
        }

        return $set;
    }

    /**
     * Description ...
     * 
     * @param type $table
     * @return type 
     * 
     * @example self::_getPrimaryKey('_example_table');
     */
    private static function _getPrimaryKey($table)
    {
        $indexes = Moraso_Db::fetchRowC('eternal', '' .
                        'show ' .
                        ' indexes ' .
                        'from ' .
                        ' ' . $table . ' ' .
                        'where ' .
                        ' Key_name = :key_name', array(
                    ':key_name' => 'PRIMARY'
                        )
        );

        return $indexes['Column_name'];
    }

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $table
     * @param type $primary
     * @return type 
     * 
     * @example self::getNodeLftRgt(17, '_example_table');
     * @example self::getNodeLftRgt(21, '_example_table', 'idexample');
     */
    public static function getNodeLftRgt($node, $table, $primary = null)
    {
        if (empty($primary)) {
            $primary = self::_getPrimaryKey($table);
        }

        return Moraso_Db::fetchRow('select lft, rgt from ' . $table . ' where ' . $primary . ' =:id', array(':id' => $node));
    }

}