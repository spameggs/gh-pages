<?php
ob_start();
require_once "./constants.php";
require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/CommandHandlerBase.php";
require_once CKFINDER_CONNECTOR_LIB_DIR . "/Core/Factory.php";
require_once CKFINDER_CONNECTOR_LIB_DIR . "/Utils/Misc.php";
require_once CKFINDER_CONNECTOR_LIB_DIR . "/Core/Hooks.php";
function resolveUrl($baseUrl)
{
    $fileSystem =& CKFinder_Connector_Core_Factory::getInstance("Utils_FileSystem");
    return $fileSystem->getDocumentRootPath() . $baseUrl;
}

$utilsSecurity =& CKFinder_Connector_Core_Factory::getInstance("Utils_Security");
$utilsSecurity->getRidOfMagicQuotes();
$config            = array();
$config['Hooks']   = array();
$config['Plugins'] = array();
require_once CKFINDER_CONNECTOR_CONFIG_FILE_PATH;

CKFinder_Connector_Core_Factory::initFactory();
$connector =& CKFinder_Connector_Core_Factory::getInstance("Core_Connector");
if (isset($_GET['command'])) {
    $connector->executeCommand($_GET['command']);
} else {
    $connector->handleInvalidCommand();
}
