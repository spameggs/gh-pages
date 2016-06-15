<?php
class Facebook extends BaseFacebook
{
    const FBSS_COOKIE_NAME = 'fbss';
    const FBSS_COOKIE_EXPIRE = 31556926;
    protected $sharedSessionID;
    public function __construct($config)
    {
        if (!session_id()) {
            session_start();
        }
        parent::__construct($config);
        if (!empty($config['sharedSession'])) {
            $this->initSharedSession();
        }
    }
    protected static $kSupportedKeys = array('state', 'code', 'access_token', 'user_id');
    protected function initSharedSession()
    {
        $cookie_name = $this->getSharedSessionCookieName();
        if (isset($_COOKIE[$cookie_name])) {
            $data = $this->parseSignedRequest($_COOKIE[$cookie_name]);
            if ($data && !empty($data['domain']) && self::isAllowedDomain($this->getHttpHost(), $data['domain'])) {
                $this->sharedSessionID = $data['id'];
                return;
            }
        }
        $base_domain           = $this->getBaseDomain();
        $this->sharedSessionID = md5(uniqid(mt_rand(), true));
        $cookie_value          = $this->makeSignedRequest(array(
            'domain' => $base_domain,
            'id' => $this->sharedSessionID
        ));
        $_COOKIE[$cookie_name] = $cookie_value;
        if (!headers_sent()) {
            $expire = time() + self::FBSS_COOKIE_EXPIRE;
            setcookie($cookie_name, $cookie_value, $expire, '/', '.' . $base_domain);
        } else {
            self::errorLog('Shared session ID cookie could not be set! You must ensure you ' . 'create the Facebook instance before headers have been sent. This ' . 'will cause authentication issues after the first request.');
        }
    }
    protected function setPersistentData($key, $value)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to setPersistentData.');
            return;
        }
        $session_var_name            = $this->constructSessionVariableName($key);
        $_SESSION[$session_var_name] = $value;
    }
    protected function getPersistentData($key, $default = false)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to getPersistentData.');
            return $default;
        }
        $session_var_name = $this->constructSessionVariableName($key);
        return isset($_SESSION[$session_var_name]) ? $_SESSION[$session_var_name] : $default;
    }
    protected function clearPersistentData($key)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to clearPersistentData.');
            return;
        }
        $session_var_name = $this->constructSessionVariableName($key);
        unset($_SESSION[$session_var_name]);
    }
    protected function clearAllPersistentData()
    {
        foreach (self::$kSupportedKeys as $key) {
            $this->clearPersistentData($key);
        }
        if ($this->sharedSessionID) {
            $this->deleteSharedSessionCookie();
        }
    }
    protected function deleteSharedSessionCookie()
    {
        $cookie_name = $this->getSharedSessionCookieName();
        unset($_COOKIE[$cookie_name]);
        $base_domain = $this->getBaseDomain();
        setcookie($cookie_name, '', 1, '/', '.' . $base_domain);
    }
    protected function getSharedSessionCookieName()
    {
        return self::FBSS_COOKIE_NAME . '_' . $this->getAppId();
    }
    protected function constructSessionVariableName($key)
    {
        $parts = array(
            'fb',
            $this->getAppId(),
            $key
        );
        if ($this->sharedSessionID) {
            array_unshift($parts, $this->sharedSessionID);
        }
        return implode('_', $parts);
    }
}