<?php
if (!defined('IN_CKFINDER'))
    exit;
require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/XmlCommandHandlerBase.php";

class CKFinder_Connector_CommandHandler_FileEditor extends CKFinder_Connector_CommandHandler_XmlCommandHandlerBase
{
    function buildXml()
    {
        if (empty($_POST['CKFinderCommand']) || $_POST['CKFinderCommand'] != 'true') {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }

        $this->checkConnector();
        $this->checkRequest();
        if (!$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_DELETE)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }

        if (!isset($_POST["fileName"])) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_NAME);
        }
        if (!isset($_POST["content"])) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }
        $fileName         = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($_POST["fileName"]);
        $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();

        if (!$resourceTypeInfo->checkExtension($fileName)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
        }

        if (!CKFinder_Connector_Utils_FileSystem::checkFileName($fileName) || $resourceTypeInfo->checkIsHiddenFile($fileName)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }

        $filePath = CKFinder_Connector_Utils_FileSystem::combinePaths($this->_currentFolder->getServerPath(), $fileName);

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND);
        }

        if (!is_writable(dirname($filePath))) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
        }

        $fp = @fopen($filePath, 'wb');
        if ($fp === false || !flock($fp, LOCK_EX)) {
            $result = false;
        } else {
            $result = fwrite($fp, $_POST["content"]);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        if ($result === false) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
        }
    }
    function onBeforeExecuteCommand(&$command)
    {
        if ($command == 'SaveFile') {
            $this->sendResponse();
            return false;
        }
        return true;
    }
}
$CommandHandler_FileEditor                 = new CKFinder_Connector_CommandHandler_FileEditor();
$config['Hooks']['BeforeExecuteCommand'][] = array(
    $CommandHandler_FileEditor,
    "onBeforeExecuteCommand"
);
$config['Plugins'][]                       = 'fileeditor';