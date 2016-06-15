<?php
if (!defined('PCLZIP_TEMPORARY_DIR')) {
    define('PCLZIP_TEMPORARY_DIR', PHPExcel_Shared_File::sys_get_temp_dir());
}
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/PCLZip/pclzip.lib.php';
class PHPExcel_Shared_ZipArchive
{
    const OVERWRITE = 'OVERWRITE';
    const CREATE = 'CREATE';
    private $_tempDir;
    private $_zip;
    public function open($fileName)
    {
        $this->_tempDir = PHPExcel_Shared_File::sys_get_temp_dir();
        $this->_zip     = new PclZip($fileName);
        return true;
    }
    public function close()
    {
    }
    public function addFromString($localname, $contents)
    {
        $filenameParts = pathinfo($localname);
        $handle        = fopen($this->_tempDir . '/' . $filenameParts["basename"], "wb");
        fwrite($handle, $contents);
        fclose($handle);
        $res = $this->_zip->add($this->_tempDir . '/' . $filenameParts["basename"], PCLZIP_OPT_REMOVE_PATH, $this->_tempDir, PCLZIP_OPT_ADD_PATH, $filenameParts["dirname"]);
        if ($res == 0) {
            throw new PHPExcel_Writer_Exception("Error zipping files : " . $this->_zip->errorInfo(true));
        }
        unlink($this->_tempDir . '/' . $filenameParts["basename"]);
    }
}