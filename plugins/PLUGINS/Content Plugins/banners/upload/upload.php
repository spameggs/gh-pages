<?php
$reefless->loadClass('Actions');
$reefless->loadClass('Resize');
$reefless->loadClass('Crop');
$reefless->loadClass('Banners', null, 'banners');
class UploadHandler extends reefless
{
    var $options;
    function UploadHandler($options = null)
    {
        global $rlBanners;
        $this->options = array(
            'script_url' => $_SERVER['PHP_SELF'],
            'banners_dir' => 'banners' . RL_DS,
            'upload_dir' => RL_FILES . 'banners' . RL_DS,
            'upload_url' => RL_FILES_URL . 'banners/',
            'dir_name' => null,
            'param_name' => 'files',
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/^image\/(gif|jpeg|png)$/i',
            'accept_file_types_ie' => '/\.(gif|jpeg|png|jpg|jpe)$/i',
            'max_number_of_files' => null,
            'discard_aborted_uploads' => true,
            'image_versions' => array(
                'thumbnail' => array(
                    'prefix' => 'banner_',
                    'max_width' => (int) $_POST['box_width'],
                    'max_height' => (int) $_POST['box_height'],
                    'watermark' => false
                )
            )
        );
        if ($options) {
            $this->options = array_replace_recursive($this->options, $options);
        }
    }
    function get_file_object($file_name)
    {
        $file_path = $this->options['upload_dir'] . $file_name;
        if (is_file($file_path) && $file_name[0] !== '.') {
            $file       = new stdClass();
            $file->name = $file_name;
            $file->size = filesize($file_path);
            $file->url  = $this->options['upload_url'] . rawurlencode($file->name);
            foreach ($this->options['image_versions'] as $version => $options) {
                if (is_file($options['upload_dir'] . $file_name)) {
                    $file->{$version . '_url'} = $options['upload_url'] . rawurlencode($file->name);
                }
            }
            $file->delete_url = $this->options['script_url'] . '?file=' . rawurlencode($file->name);
            $file->delete_url .= '&_method=DELETE';
            $file->delete_type = 'POST';
            return $file;
        }
        return null;
    }
    function get_file_objects()
    {
        global $banner_id;
        $banners = $this->fetch('*', array(
            'ID' => $banner_id
        ), "AND `Image` <> '' AND `Image` <> 'html'", null, 'banners');
        if (!$banners)
            return false;
        $controller = defined('REALM') && REALM == 'admin' ? 'admin' : 'account';
        foreach ($banners as $banner) {
            $info                = getimagesize(RL_FILES . $this->options['banners_dir'] . RL_DS . $banner['Photo']);
            $file                = new stdClass();
            $file->banner_id     = $banner['ID'];
            $file->name          = $banner['Image'];
            $file->size          = filesize(RL_FILES . $this->options['banners_dir'] . RL_DS . $photo['Image']);
            $file->thumbnail_url = $this->options['upload_url'] . '/' . $banner['Image'];
            $file->delete_url    = RL_PLUGINS_URL . 'banners/upload/' . $controller . '.php?file=' . $banner['Image'] . '&id=' . $banner_id;
            $file->delete_url .= '&_method=DELETE';
            $file->delete_type = 'POST';
            $files[]           = $file;
        }
        return $files;
    }
    function create_scaled_image($file_name, $new_file_name, $options)
    {
        global $rlResize, $rlCrop, $config, $rlBanners;
        $file_path     = $this->options['upload_dir'] . $file_name;
        $new_file_path = $this->options['upload_dir'] . $new_file_name;
        if ($rlBanners->is_animated_gif($file_path)) {
            copy($file_path, $new_file_path);
        } else {
            $rlCrop->loadImage($file_path);
            $rlCrop->cropBySize($options['max_width'], $options['max_height'], ccCENTER);
            $rlCrop->saveImage($new_file_path, $config['img_quality']);
            $rlCrop->flushImages();
            $rlResize->resize($new_file_path, $new_file_path, 'C', array(
                $options['max_width'],
                $options['max_height']
            ), true, $options['watermark']);
        }
        return true;
    }
    function has_error($uploaded_file, $file, $error)
    {
        if ($error) {
            return $error;
        }
        if (!preg_match($this->options['accept_file_types'], $file->type)) {
            if (!preg_match($this->options['accept_file_types_ie'], $file->name)) {
                return 'acceptFileTypes';
            }
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = filesize($uploaded_file);
        } else {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }
        if ($this->options['max_file_size'] && ($file_size > $this->options['max_file_size'] || $file->size > $this->options['max_file_size'])) {
            return 'maxFileSize';
        }
        if ($this->options['min_file_size'] && $file_size < $this->options['min_file_size']) {
            return 'minFileSize';
        }
        if (is_int($this->options['max_number_of_files']) && (count($this->get_file_objects()) >= $this->options['max_number_of_files'])) {
            return 'maxNumberOfFiles';
        }
        return $error;
    }
    function handle_file_upload($uploaded_file, $name, $size, $type, $error)
    {
        global $banner_id, $dir, $config;
        $uf_info = getimagesize($uploaded_file);
        if (!$uf_info || !preg_match('/^image\/(gif|jpe?g|png)$/i', $uf_info['mime'])) {
            die("Filetype not allowed");
        }
        $ext        = array_reverse(explode('.', $name));
        $ext        = strtolower($ext[0]);
        $file       = new stdClass();
        $file->name = 'tmp_' . time() . mt_rand() . '.' . $ext;
        $file->size = intval($size);
        $file->type = $type;
        $error      = $this->has_error($uploaded_file, $file, $error);
        $controller = defined('REALM') && REALM == 'admin' ? 'admin' : 'account';
        if (!$error && $file->name) {
            $file_path   = $this->options['upload_dir'] . $file->name;
            $append_file = is_file($file_path) && $file->size > filesize($file_path);
            clearstatcache();
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                if ($append_file) {
                    file_put_contents($file_path, fopen($uploaded_file, 'r'), FILE_APPEND);
                } else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                file_put_contents($file_path, fopen('php://input', 'r'), $append_file ? FILE_APPEND : 0);
            }
            $file_size = filesize($file_path);
            if ($file_size === $file->size) {
                $file->url = $this->options['upload_url'] . rawurlencode($file->name);
                foreach ($this->options['image_versions'] as $version => $options) {
                    $new = $options['prefix'] . time() . mt_rand() . '.' . $ext;
                    if ($version == 'thumbnail') {
                        $delete_filename = $new;
                    }
                    if ($this->create_scaled_image($file->name, $new, $options)) {
                        $file->{$version} = $new;
                    }
                }
            } else if ($this->options['discard_aborted_uploads']) {
                unlink($file_path);
                $file->error = 'abort';
            }
            unlink($file_path);
            $file->size       = $file_size;
            $file->delete_url = RL_PLUGINS_URL . 'banners/upload/' . $controller . '.php?file=' . $this->options['dir_name'] . rawurlencode($delete_filename) . '&id=' . $banner_id;
            $file->delete_url .= '&_method=DELETE';
            $file->delete_type = 'POST';
        } else {
            $file->error = $error;
        }
        return $file;
    }
    function get()
    {
        global $rlJson;
        $file_name = isset($_REQUEST['file']) ? basename(stripslashes($_REQUEST['file'])) : null;
        if ($file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = $this->get_file_objects();
        }
        header('Content-type: application/json');
        echo $rlJson->encode($info);
    }
    function post()
    {
        global $banner_id, $rlActions, $rlJson, $rlBanners, $config;
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete($print_response);
        }
        if (defined('REALM') && REALM == 'admin' && $banner_id == 0) {
            $fInsert = array(
                'Box' => $_POST['banner_box'],
                'Type' => $_POST['banner_type'],
                'Status' => 'incomplete'
            );
            $rlActions->insertOne($fInsert, 'banners');
            $banner_id = $_SESSION['banner_id'] = $_SESSION['add_banner_id'] = mysql_insert_id();
        }
        $folderInfo                  = $rlBanners->makeBannerFolder($banner_id, $this->options);
        $this->options['upload_dir'] = $folderInfo['dir'];
        $this->options['upload_url'] = $folderInfo['url'];
        $this->options['dir_name']   = $folderInfo['dirName'];
        $upload                      = isset($_FILES[$this->options['param_name']]) ? $_FILES[$this->options['param_name']] : array(
            'tmp_name' => null,
            'name' => null,
            'size' => null,
            'type' => null,
            'error' => null
        );
        $banner                      = $this->handle_file_upload($upload['tmp_name'], isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'], isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'], isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'], $upload['error']);
        $update                      = array(
            'fields' => array(
                'Image' => $folderInfo['dirName'] . $banner->thumbnail,
                'Status' => 'incomplete'
            ),
            'where' => array(
                'ID' => $banner_id
            )
        );
        if (defined('REALM') && REALM == 'admin') {
            $update['fields']['Box']  = $_POST['banner_box'];
            $update['fields']['Type'] = $_POST['banner_type'];
        }
        $rlActions->updateOne($update, 'banners');
        $_SESSION['banner_id'] = $banner_id;
        $banner->banner_id     = $banner_id;
        $banner->thumbnail_url = $this->options['upload_url'] . $banner->thumbnail;
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo $rlJson->encode(array(
            $banner
        ));
    }
    function delete()
    {
        global $banner_id, $rlJson;
        $id       = (int) $_REQUEST['id'];
        $fileName = isset($_REQUEST['file']) ? stripslashes($_REQUEST['file']) : null;
        $filePath = $this->options['upload_dir'] . $fileName;
        $success  = $id == $banner_id;
        $image    = $this->getOne('Image', "`ID` = '{$banner_id}' AND `Image` = '{$fileName}'", 'banners');
        if ($success && $image) {
            $dateRelease = explode('/', $image);
            $this->query("UPDATE `" . RL_DBPREFIX . "banners` SET `Image` = '' WHERE `ID` = '{$banner_id}' LIMIT 1");
            $this->deleteDirectory(RL_FILES . $this->options['banners_dir'] . $dateRelease[0] . RL_DS . "b{$banner_id}" . RL_DS);
        }
        header('Content-type: application/json');
        echo $rlJson->encode($success);
    }
}
$uploadHandler = new UploadHandler();
header('Pragma: no-cache');
header('Cache-Control: private, no-cache');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
switch ($_SERVER['REQUEST_METHOD']) {
    case 'HEAD':
    case 'GET':
        $uploadHandler->get();
        break;
    case 'POST':
        $uploadHandler->post();
        break;
    case 'DELETE':
        $uploadHandler->delete();
        break;
    case 'OPTIONS':
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
}