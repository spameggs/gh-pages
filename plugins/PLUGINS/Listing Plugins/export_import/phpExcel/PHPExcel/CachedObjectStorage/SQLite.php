<?php
class PHPExcel_CachedObjectStorage_SQLite extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    private $_TableName = null;
    private $_DBHandle = null;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty) {
            $this->_currentObject->detach();
            if (!$this->_DBHandle->queryExec("INSERT OR REPLACE INTO kvp_" . $this->_TableName . " VALUES('" . $this->_currentObjectID . "','" . sqlite_escape_string(serialize($this->_currentObject)) . "')"))
                throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
            $this->_currentCellIsDirty = false;
        }
        $this->_currentObjectID = $this->_currentObject = null;
    }
    public function addCacheData($pCoord, PHPExcel_Cell $cell)
    {
        if (($pCoord !== $this->_currentObjectID) && ($this->_currentObjectID !== null)) {
            $this->_storeData();
        }
        $this->_currentObjectID    = $pCoord;
        $this->_currentObject      = $cell;
        $this->_currentCellIsDirty = true;
        return $cell;
    }
    public function getCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return $this->_currentObject;
        }
        $this->_storeData();
        $query         = "SELECT value FROM kvp_" . $this->_TableName . " WHERE id='" . $pCoord . "'";
        $cellResultSet = $this->_DBHandle->query($query, SQLITE_ASSOC);
        if ($cellResultSet === false) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        } elseif ($cellResultSet->numRows() == 0) {
            return null;
        }
        $this->_currentObjectID = $pCoord;
        $cellResult             = $cellResultSet->fetchSingle();
        $this->_currentObject   = unserialize($cellResult);
        $this->_currentObject->attach($this->_parent);
        return $this->_currentObject;
    }
    public function isDataSet($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return true;
        }
        $query         = "SELECT id FROM kvp_" . $this->_TableName . " WHERE id='" . $pCoord . "'";
        $cellResultSet = $this->_DBHandle->query($query, SQLITE_ASSOC);
        if ($cellResultSet === false) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        } elseif ($cellResultSet->numRows() == 0) {
            return false;
        }
        return true;
    }
    public function deleteCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            $this->_currentObject->detach();
            $this->_currentObjectID = $this->_currentObject = null;
        }
        $query = "DELETE FROM kvp_" . $this->_TableName . " WHERE id='" . $pCoord . "'";
        if (!$this->_DBHandle->queryExec($query))
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        $this->_currentCellIsDirty = false;
    }
    public function getCellList()
    {
        if ($this->_currentObjectID !== null) {
            $this->_storeData();
        }
        $query         = "SELECT id FROM kvp_" . $this->_TableName;
        $cellIdsResult = $this->_DBHandle->unbufferedQuery($query, SQLITE_ASSOC);
        if ($cellIdsResult === false)
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        $cellKeys = array();
        foreach ($cellIdsResult as $row) {
            $cellKeys[] = $row['id'];
        }
        return $cellKeys;
    }
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        $this->_currentCellIsDirty;
        $this->_storeData();
        $tableName = str_replace('.', '_', $this->_getUniqueID());
        if (!$this->_DBHandle->queryExec('CREATE TABLE kvp_' . $tableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)
													AS SELECT * FROM kvp_' . $this->_TableName))
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        $this->_TableName = $tableName;
    }
    public function unsetWorksheetCells()
    {
        if (!is_null($this->_currentObject)) {
            $this->_currentObject->detach();
            $this->_currentObject = $this->_currentObjectID = null;
        }
        $this->_parent = null;
        $this->__destruct();
    }
    public function __construct(PHPExcel_Worksheet $parent)
    {
        parent::__construct($parent);
        if (is_null($this->_DBHandle)) {
            $this->_TableName = str_replace('.', '_', $this->_getUniqueID());
            $_DBName          = ':memory:';
            $this->_DBHandle  = new SQLiteDatabase($_DBName);
            if ($this->_DBHandle === false)
                throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
            if (!$this->_DBHandle->queryExec('CREATE TABLE kvp_' . $this->_TableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)'))
                throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        }
    }
    public function __destruct()
    {
        $this->_DBHandle = null;
    }
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('sqlite_open')) {
            return false;
        }
        return true;
    }
}