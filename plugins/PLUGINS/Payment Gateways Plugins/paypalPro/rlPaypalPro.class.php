<?php
class rlPaypalPro extends reefless
{
    var $api_endpoint;
    var $use_proxy = false;
    var $proxy_host;
    var $proxy_port;
    function rlPaypalPro()
    {
        $environment        = $GLOBALS['config']['dpp_test_mode'] ? 'sandbox' : 'live';
        $this->api_endpoint = 'https://api-3t.' . $environment . '.paypal.com/nvp/';
    }
    function post($data = false, $price = false)
    {
        if (!$data) {
            return false;
        }
        $method       = 'DoDirectPayment';
        $payment_type = 'Sale';
        $payment_type = urlencode($payment_type);
        $currency     = urldecode($GLOBALS['config']['dpp_currency']);
        $price        = urldecode($price);
        foreach ($data as $key => $val) {
            $data[$key] = urldecode($val);
        }
        $expiration_date = $data['month'] . $data['year'];
        $nvp             = "&PAYMENTACTION={$payment_type}&AMT={$price}&CREDITCARDTYPE={$data['card_type']}&ACCT={$data['card_number']}" . "&EXPDATE={$expiration_date}&CVV2={$data['csc']}&FIRSTNAME={$data['first_name']}&LASTNAME={$data['last_name']}" . "&STREET={$data['address_1']}&CITY={$data['city']}&STATE={$data['state']}&ZIP={$data['zip_code']}&COUNTRYCODE={$data['country']}&CURRENCYCODE={$currency}";
        return $this->call($method, $nvp);
    }
    function call($methodName = false, $nvp = false)
    {
        global $rlDebug;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($this->use_proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy_host . ":" . $this->proxy_port);
        }
        $version       = urldecode($GLOBALS['config']['dpp_version']);
        $api_signature = urldecode($GLOBALS['config']['dpp_api_signature']);
        $api_password  = urldecode($GLOBALS['config']['dpp_api_password']);
        $api_username  = urldecode($GLOBALS['config']['dpp_api_username']);
        $nvpreq        = "METHOD=" . $methodName . "&VERSION=" . $version . "&PWD=" . $api_password . "&USER=" . $api_username . "&SIGNATURE=" . $api_signature . $nvp;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
        $response = curl_exec($ch);
        if (!$response) {
            $rlDebug->logger('paypalPro: ' . $methodName . ' failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')');
            return false;
        }
        $response_data_tmp = explode("&", $response);
        $response_data     = array();
        foreach ($response_data_tmp as $i => $value) {
            $tmp = explode("=", $value);
            if (sizeof($tmp) > 1) {
                $response_data[$tmp[0]] = $tmp[1];
            }
        }
        if ((0 == sizeof($response_data)) || !array_key_exists('ACK', $response_data)) {
            $rlDebug->logger('paypalPro: Invalid HTTP Response for POST request(' . $nvpreq . ') to ' . $this->api_endpoint . '.');
            return false;
        }
        return $response_data;
    }
    function install()
    {
        $queryql_dump = fopen(RL_PLUGINS . 'paypalPro' . RL_DS . 'mysql/iso_country_list.sql', 'r');
        mysql_query("SET NAMES `utf8`");
        if ($queryql_dump) {
            while ($query = fgets($queryql_dump, 10240)) {
                $query = trim($query);
                if ($query[0] == '#')
                    continue;
                if ($query[0] == '-')
                    continue;
                if ($query[strlen($query) - 1] == ';') {
                    $query_sql .= $query;
                } else {
                    $query_sql .= $query;
                    continue;
                }
                if (!empty($query_sql)) {
                    $find    = array(
                        '{db_prefix}'
                    );
                    $replace = array(
                        RL_DBPREFIX
                    );
                }
                $query_sql = str_replace($find, $replace, $query_sql);
                $res       = $this->query($query_sql);
                if (!$res) {
                    $errors[] = "Can not run sql query.";
                }
                unset($query_sql);
            }
            fclose($sql_dump);
        }
        $queryql_dump = fopen(RL_PLUGINS . 'paypalPro' . RL_DS . 'mysql/iso_state_list.sql', 'r');
        mysql_query("SET NAMES `utf8`");
        if ($queryql_dump) {
            while ($query = fgets($queryql_dump, 10240)) {
                $query = trim($query);
                if ($query[0] == '#')
                    continue;
                if ($query[0] == '-')
                    continue;
                if ($query[strlen($query) - 1] == ';') {
                    $query_sql .= $query;
                } else {
                    $query_sql .= $query;
                    continue;
                }
                if (!empty($query_sql)) {
                    $find    = array(
                        '{db_prefix}'
                    );
                    $replace = array(
                        RL_DBPREFIX
                    );
                }
                $query_sql = str_replace($find, $replace, $query_sql);
                $res       = $this->query($query_sql);
                if (!$res) {
                    $errors[] = "Can not run sql query.";
                }
                unset($query_sql);
            }
            fclose($sql_dump);
        }
    }
    function uninstall()
    {
        $sql = "DROP TABLE `" . RL_DBPREFIX . "iso_countries`";
        $this->query($sql);
        $sql = "DROP TABLE `" . RL_DBPREFIX . "iso_states`";
        $this->query($sql);
    }
}