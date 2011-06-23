<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Adm_Script_Synchronize_Database_Structure extends Aitsu_Adm_Script_Abstract {

	protected $_methodMap = array ();
	protected $_xml = null;

	protected $_tableRestoreOffset = null;
	protected $_constraintRestoreOffset = null;

	public static function getName() {

		return Aitsu_Translate :: translate('Synchronize database structure');
	}

	protected function _init() {

		$this->_methodMap = array (
			'_beforeRemoveConstraints',
			'_removeConstraints',
			'_beforeRemoveIndexes',
			'_removeIndexes',
			'_removeViews',
			'_removeEmptyTables'
		);

		$this->_xml = DOMDocument :: loadXML('<database></database>');
		$xmls = Aitsu_Util_Dir :: scan(APPLICATION_PATH, 'database.xml');
		foreach ($xmls as $xml) {
			$dom = DOMDocument :: load($xml)->documentElement;
			foreach ($dom->childNodes as $node) {
				$node = $this->_xml->importNode($node, true);
				$this->_xml->documentElement->appendChild($node);
			}
		}

		$tables = $this->_xml->getElementsByTagName('table');

		$this->_tableRestoreOffset = 6;
		for ($i = 0; $i < $tables->length; $i++) {
			$this->_methodMap[] = '_restoreTables';
		}

		$this->_constraintRestoreOffset = $this->_tableRestoreOffset + $tables->length;
		for ($i = 0; $i < $tables->length; $i++) {
			$this->_methodMap[] = '_restoreConstraints';
		}

		$this->_methodMap[] = '_restoreViews';
	}
	
	protected function _beforeRemoveConstraints() {
		
		return Aitsu_Translate :: translate('Removing constraints. This may take quite a while. Please be patient to allow to remove the constraints.');
	}

	protected function _beforeRemoveIndexes() {
		
		return Aitsu_Translate :: translate('Removing indexes. This may take quite a while. Please be patient to allow to remove the indexes.');
	}

	protected function _hasNext() {

		if ($this->_currentStep < count($this->_methodMap)) {
			return true;
		}

		return false;
	}

	protected function _next() {

		return 'Next line to be executed.';
	}

	protected function _executeStep() {

		$method = $this->_methodMap[$this->_currentStep];
		$response = call_user_func_array(array (
			$this,
			$method
		), array ());

		if (is_object($response)) {
			return Aitsu_Adm_Script_Response :: factory($response->message, 'warning');
		}

		return Aitsu_Adm_Script_Response :: factory($response);
	}

	protected function _removeConstraints() {

		$constraints = Aitsu_Db :: fetchAll('' .
		'select * from information_schema.table_constraints ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name like :prefix ' .
		'	and constraint_type = \'FOREIGN KEY\' ', array (
			':schema' => Aitsu_Config :: get('database.params.dbname'),
			':prefix' => Aitsu_Config :: get('database.params.tblprefix') . '%'
		));

		foreach ($constraints as $constraint) {
			Aitsu_Db :: query('' .
			'alter table `' . $constraint['TABLE_NAME'] . '` drop foreign key `' . $constraint['CONSTRAINT_NAME'] . '`');
		}

		return Aitsu_Translate :: translate('Constraints have been removed.');
	}

	protected function _removeIndexes() {

		$indexes = Aitsu_Db :: fetchAll('' .
		'select distinct ' .
		'	table_name, ' .
		'	index_name ' .
		'from information_schema.statistics ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name like :prefix ' .
		'	and index_name != \'PRIMARY\' ', array (
			':schema' => Aitsu_Config :: get('database.params.dbname'),
			':prefix' => Aitsu_Config :: get('database.params.tblprefix') . '%'
		));

		foreach ($indexes as $index) {
			Aitsu_Db :: query('' .
			'alter table `' . $index['table_name'] . '` drop index `' . $index['index_name'] . '`');
		}

		return Aitsu_Translate :: translate('Indexes have been removed.');
	}

	protected function _removeViews() {

		$views = Aitsu_Db :: fetchAll('' .
		'select * from information_schema.views ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name like :prefix ', array (
			':schema' => Aitsu_Config :: get('database.params.dbname'),
			':prefix' => Aitsu_Config :: get('database.params.tblprefix') . '%'
		));

		try {
			foreach ($views as $view) {
				Aitsu_Db :: query('' .
				'drop view `' . $view['TABLE_NAME'] . '`');
			}
		} catch (Exception $e) {
			return (object) array (
				'message' => Aitsu_Translate :: translate('Views have not been dropped due to insufficient privileges. If they are unchanged, they still might work properly. However, it is recommended to drop and recreate them using mysql command line interface.')
			);
		}

		return Aitsu_Translate :: translate('Views have been removed.');
	}

	protected function _removeEmptyTables() {

		$tables = Aitsu_Db :: fetchAll('' .
		'select * from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name like :prefix ', array (
			':schema' => Aitsu_Config :: get('database.params.dbname'),
			':prefix' => Aitsu_Config :: get('database.params.tblprefix') . '%'
		));

		foreach ($tables as $table) {
			if (Aitsu_Db :: fetchOne('' .
				'select count(*) from ' . $table['TABLE_NAME']) == 0) {
				Aitsu_Db :: query('' .
				'drop table `' . $table['TABLE_NAME'] . '`');
			}
		}

		return Aitsu_Translate :: translate('Empty tables have been removed.');
	}

	protected function _restoreTables() {

		$currentIndex = $this->_currentStep - $this->_tableRestoreOffset;
		$table = $this->_xml->getElementsByTagName('table')->item($currentIndex);

		if (Aitsu_Db :: fetchOne('' .
			'select count(*) from information_schema.tables ' .
			'where ' .
			'	table_schema = :schema ' .
			'	and table_name = :tablename', array (
				':schema' => Aitsu_Config :: get('database.params.dbname'),
				':tablename' => Aitsu_Config :: get('database.params.tblprefix') . $table->attributes->getNamedItem('name')->nodeValue
			)) == 0) {
			$this->_createTable($table);
		} else {
			$this->_reconstructTable($table);
		}

		$this->_restoreIndexes($table);

		return 'Table ' . $table->attributes->getNamedItem('name')->nodeValue . ' has been restored.';
	}

	protected function _restoreConstraints() {

		$currentIndex = $this->_currentStep - $this->_constraintRestoreOffset;
		$table = $this->_xml->getElementsByTagName('table')->item($currentIndex);

		$prefix = Aitsu_Config :: get('database.params.tblprefix');

		$tableName = $prefix . $table->getAttribute('name');
		foreach ($table->getElementsByTagName('field') as $field) {
			$columnName = $field->getAttribute('name');
			foreach ($field->getElementsByTagName('constraint') as $constraint) {
				$refTable = $prefix . $constraint->getAttribute('table');
				$refColumn = $constraint->getAttribute('column');
				$onUpdate = $constraint->hasAttribute('onupdate') ? $constraint->getAttribute('onupdate') : 'no action';
				$onDelete = $constraint->hasAttribute('ondelete') ? $constraint->getAttribute('ondelete') : 'no action';
				$indexName = 'fk_' . $columnName . '_' . $refColumn;

				/*
				 * Add an index for the specified column.
				 */
				Aitsu_Db :: query("alter table $tableName add index `$indexName` (`$columnName`)");

				if ($constraint->hasAttribute('ondelete') && $constraint->getAttribute('ondelete') == 'set null') {
					/*
					 * Set values to null that would case a referential integrity violation.
					 */
					Aitsu_Db :: query("" .
					"update `$tableName` src " .
					"set src.$columnName = null " .
					"where src.$columnName not in (" .
					"	select tgt.$refColumn from `$refTable` tgt" .
					")");
				} else {
					/*
					 * Remove entries that would cause a referential integrity violation.
					 */
					Aitsu_Db :: query("" .
					"delete src.* from `$tableName` src " .
					"where src.$columnName not in (" .
					"	select tgt.$refColumn from `$refTable` tgt" .
					")");
				}

				/*
				 * Add the constraint.
				 */
				Aitsu_Db :: query("" .
				"alter table `$tableName` add foreign key (`$columnName`) " .
				"references `$refTable` (`$refColumn`) " .
				"on delete $onDelete " .
				"on update $onUpdate");
			}
		}

		return Aitsu_Translate :: translate('Constraints on ' . $tableName . ' have been restored.');
	}

	protected function _restoreViews() {

		$prefix = Aitsu_Config :: get('database.params.tblprefix');

		try {
			foreach ($this->_xml->getElementsByTagName('view') as $view) {
				$viewName = $prefix . $view->getAttribute('name');
				$viewSelect = $view->nodeValue;
				Aitsu_Db :: query("create view `$viewName` as $viewSelect");
			}
		} catch (Exception $e) {
			return (object) array (
				'message' => Aitsu_Translate :: translate('Views have not been restored due to insufficient privilegies. You have to add them using the mysql command line interface.')
			);
		}

		return Aitsu_Translate :: translate('Views have been restored.');
	}

	protected function _createTable($node) {

		$statement = 'CREATE TABLE `' . Aitsu_Config :: get('database.params.tblprefix') . $node->attributes->getNamedItem('name')->nodeValue . '` (';

		$primaryKeys = array ();
		$fields = array ();
		foreach ($node->getElementsByTagName('field') as $field) {
			$name = $field->getAttribute('name');
			$type = $field->getAttribute('type');
			$null = $field->hasAttribute('nullable') && $field->getAttribute('nullable') == 'true' ? 'null' : 'not null';
			$default = $field->getAttribute('default') == 'null' ? '' : "default '" . $field->getAttribute('default') . "'";
			$autoincrement = $field->hasAttribute('autoincrement') && $field->getAttribute('autoincrement') == 'true' ? 'auto_increment' : '';
			$comment = $field->hasAttribute('comment') ? "comment '" . str_replace("'", "''", $field->getAttribute('comment')) . "'" : '';

			$tmp = "`$name` $type $null $default $autoincrement $comment";

			$tmp = str_replace("'CURRENT_TIMESTAMP'", 'current_timestamp', $tmp);

			$fields[] = $tmp;

			if ($field->hasAttribute('primary') && $field->getAttribute('primary') == 'true') {
				$primaryKeys[] = $name;
			}
		}

		$statement .= implode(',', $fields);

		if (count($primaryKeys) > 0) {
			$statement .= ', PRIMARY KEY (`' . implode('`,`', $primaryKeys) . '`)';
		}

		$statement .= ') ENGINE=' . $node->attributes->getNamedItem('engine')->nodeValue;

		$statement .= ' COMMENT=\'Since ' . $node->getAttribute('since') . '\'';

		Aitsu_Db :: query($statement);
	}

	protected function _restoreIndexes($table) {

		$tableName = Aitsu_Config :: get('database.params.tblprefix') . $table->getAttribute('name');

		foreach ($table->getElementsByTagName('index') as $index) {
			$type = $index->hasAttribute('type') ? $index->getAttribute('type') : 'index';
			$name = $index->hasAttribute('name') ? '`' . $index->getAttribute('name') . '`' : '';
			$columns = $index->getAttribute('columns');
			Aitsu_Db :: query("alter table $tableName add $type $name ($columns)");
		}
	}

	protected function _reconstructTable($table) {

		$schema = Aitsu_Config :: get('database.params.dbname');
		$backupTable = Aitsu_Config :: get('database.params.tblprefix') . 'backup_table';
		$tableName = Aitsu_Config :: get('database.params.tblprefix') . $table->getAttribute('name');

		Aitsu_Db :: query('drop table if exists ' . $backupTable);
		Aitsu_Db :: query('rename table ' . $tableName . ' to ' . $backupTable);
		$this->_createTable($table);

		/*
		 * Identify the field intersection to be used to move data to the new structure.
		 */
		$intersection = Aitsu_Db :: fetchCol('' .
		'select ' .
		'	concat(\'`\', newtable.column_name, \'`\') ' .
		'from information_schema.columns newtable, information_schema.columns oldtable ' .
		'where ' .
		'	newtable.table_schema = oldtable.table_schema ' .
		'	and newtable.column_name = oldtable.column_name ' .
		'	and newtable.table_schema = :schema ' .
		'	and newtable.table_name = :newtable ' .
		'	and oldtable.table_name = :oldtable ', array (
			':schema' => $schema,
			':newtable' => $tableName,
			':oldtable' => $backupTable
		));

		/*
		 * Move data from backup table to the new structure.
		 */
		Aitsu_Db :: query("" .
		"insert into $tableName (" . implode(', ', $intersection) . ") " .
		"select " . implode(', ', $intersection) . " from $backupTable ");

		Aitsu_Db :: query('drop table ' . $backupTable);
	}

}