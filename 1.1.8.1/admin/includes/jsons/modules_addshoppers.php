<?php

include_once(dirname(__FILE__) . '/../../includes/classes/addshoppers.php');

class toC_Json_Modules_addshoppers {

    const WIDGET_STOCK_IN_STOCK = 'InStock';
    const WIDGET_STOCK_OUT_OF_STOCK = 'OutOfStock';
    const REG_LOGIN_EXISTS = -1;
    const REG_ACCOUNT_NOT_CREATED = 0;
    const REG_ACCOUNT_CREATED = 1;
    const REG_PASSWORD_TOO_SHORT = 2;
    const REG_PASSWORD_CONSECUTIVE_CHARS = 8;
    const REG_PASSWORD_COMMON = 9;
    const REG_PARAM_MISSING = 10;
    const REG_WRONG_PLATFORM = 12;
    const REG_DOMAIN_BANNED = 17;
    const REG_CATEGORY_INVALID = 19;
    const LOGIN_ACCOUNT_CREATED = 1;
    const LOGIN_MISSING_PARAMETER = 10;
    const LOGIN_WRONG_CREDENTIALS = 11;
    const LOGIN_SITE_EXISTS = 15;

    protected static $endpoint = 'http://api.addshoppers.com/1.0';
    public static $defaultShopId = '500975935b3a42793000002b';
    protected static $defaultCategory = 'other';
    protected static $platform = 'other';
    protected static $table_name = 'toc_addshoppers';
    protected static $url;

    /**
     * @var array Login messages mapped from response code
     */
    public static $loginMessages = array(
        self::LOGIN_ACCOUNT_CREATED => 'Account authenticated successfuly',
        self::LOGIN_MISSING_PARAMETER => 'Please fill in all the fields',
        self::LOGIN_WRONG_CREDENTIALS => 'Wrong credentials',
        self::LOGIN_SITE_EXISTS => 'Site is already registered',
    );

    /**
     * @var array Registration messages mapped from response code
     */
    public static $registrationMessages = array(
        self::REG_LOGIN_EXISTS => 'Login already exists',
        self::REG_ACCOUNT_NOT_CREATED => 'Account was not created due to unknown error',
        self::REG_ACCOUNT_CREATED => 'Account was successfuly created!',
        self::REG_PASSWORD_TOO_SHORT => 'Password is too short',
        self::REG_PASSWORD_CONSECUTIVE_CHARS => 'Password must consist of different characters',
        self::REG_PASSWORD_COMMON => 'Password is too weak',
        self::REG_PARAM_MISSING => 'Request was invalid',
        self::REG_DOMAIN_BANNED => 'Your domain is banned',
        self::REG_WRONG_PLATFORM => 'Wrong platform in configuration',
    );

    function initForm() {
        
    }

    public function loadSettings() {
        global $toC_Json, $osC_Language;

        echo $toC_Json->encode(self::getSettings());
    }

    public function updateSettings() {
        global $toC_Json, $osC_Language;
        $pre_data = self::getSettings();

        if ($_REQUEST['login_password'] == '') {
            $_REQUEST['login_password'] = $pre_data['user_password'];
        }

        if (key_exists('use_default_buttons', $_REQUEST) && $_REQUEST['use_default_buttons'] == 'on') {
            $_REQUEST['use_default_buttons'] = 1;
        } else {
            $_REQUEST['use_default_buttons'] = 0;
        }

        if (key_exists('use_open_graph_buttons', $_REQUEST) && $_REQUEST['use_open_graph_buttons'] == 'on') {
            $_REQUEST['use_open_graph_buttons'] = 1;
        } else {
            $_REQUEST['use_open_graph_buttons'] = 0;
        }

        $data = array(
            "shopid" => $_REQUEST["shopid"],
            "api_key" => $_REQUEST["api_key"],
            "user_login" => $_REQUEST['login_email'],
            "user_password" => $_REQUEST['login_password'],
            "use_default_buttons" => $_REQUEST['use_default_buttons'],
            "use_open_graph_buttons" => $_REQUEST['use_open_graph_buttons'],
        );

        self::saveSettings($data);

        $response = array(
            'success' => true,
            'feedback' => self::getSettings()
        );
        echo $toC_Json->encode($response);
    }

    public function tryLogin() {
        global $toC_Json, $osC_Language;

        $data = array(
            'login' => $_REQUEST['login_email'],
            'password' => $_REQUEST['login_password'],
        );

        $result = self::sendCurlRequest('/login', $data);

        self::saveSettings(array(
            "shopid" => $result["shopid"],
            "api_key" => $result["api_key"],
            "user_login" => $_REQUEST['login_email'],
            "user_password" => $_REQUEST['login_password'],
        ));

        if ($result["result"] == 1 || $result["result"] == 15) {
            $response = array(
                'success' => true,
                'feedback' => self::getSettings()
            );
        } else {
            $response = array(
                'success' => false,
                'feedback' => self::$loginMessages[$result['result']]
            );
        }


        echo $toC_Json->encode($response);
    }

    public function tryRegister() {
        global $toC_Json, $osC_Language;

        $data = array(
            'email' => $_REQUEST['register_email'],
            'password' => $_REQUEST['register_password_1'],
            'category' => $_REQUEST['register_category'],
            'phone' => $_REQUEST['register_phone'],
        );

        $result = self::sendCurlRequest('/registration', $data);

        self::saveSettings(array(
            "shopid" => $result["shopid"],
            "api_key" => $result["api_key"],
            "user_login" => $_REQUEST['register_email'],
            "user_password" => $_REQUEST['register_password_1'],
            "use_default_buttons" => 1,
            "use_open_graph_buttons" => 1,
        ));

        if ($result["result"] == 1) {
            $response = array(
                'success' => true,
                'feedback' => self::getSettings()
            );
        } else {
            $response = array(
                'success' => false,
                'feedback' => self::$registrationMessages[$result['result']]
            );
        }


        echo $toC_Json->encode($response);
    }

    public function wipeData() {
        global $toC_Json;

        self::saveSettings(array(
            "shopid" => '',
            "api_key" => '',
            "user_login" => '',
            "user_password" => '',
            "use_default_buttons" => 0,
            "use_open_graph_buttons" => 0,
        ));

        $response = array(
            'success' => true,
            'feedback' => self::getSettings()
        );

        echo $toC_Json->encode($response);
    }

    protected static function saveSettings($params) {
        global $osC_Database;

        $check_settings_row = $osC_Database->query('select count(*) as count from :table_addshoppers');
        $check_settings_row->bindTable(':table_addshoppers', self::$table_name);
        $check_settings_row->execute();
        $does_exists = $check_settings_row->toArray();

        if ($does_exists["count"] > 0) {
            $query_string = 'update :table_addshoppers set ';

            $query_parts = array();

            if (key_exists("shopid", $params)) {
                $query_parts[] = "shopid = :shopid";
            }

            if (key_exists("api_key", $params)) {
                $query_parts[] = "api_key = :api_key";
            }

            if (key_exists("user_login", $params)) {
                $query_parts[] = "user_login = :user_login";
            }

            if (key_exists("user_password", $params)) {
                $query_parts[] = "user_password = :user_password";
            }

            if (key_exists("use_default_buttons", $params)) {
                $query_parts[] = "use_default_buttons = :use_default_buttons";
            }

            if (key_exists("use_open_graph_buttons", $params)) {
                $query_parts[] = "use_open_graph_buttons = :use_open_graph_buttons";
            }

            $setting_query = $osC_Database->query($query_string . implode(", ", $query_parts));
        } else {
            $query_string = 'insert into :table_addshoppers';

            $columns_array = array();
            $values_array = array();

            if (key_exists("shopid", $params)) {
                $columns_array[] = 'shopid';
                $values_array[] = ':shopid';
            }

            if (key_exists("api_key", $params)) {
                $columns_array[] = 'api_key';
                $values_array[] = ':api_key';
            }

            if (key_exists("user_login", $params)) {
                $columns_array[] = 'user_login';
                $values_array[] = ':user_login';
            }

            if (key_exists("user_password", $params)) {
                $columns_array[] = 'user_password';
                $values_array[] = ':user_password';
            }

            if (key_exists("use_default_buttons", $params)) {
                $columns_array[] = 'use_default_buttons';
                $values_array[] = ':use_default_buttons';
            }

            if (key_exists("use_open_graph_buttons", $params)) {
                $columns_array[] = 'use_open_graph_buttons';
                $values_array[] = ':use_open_graph_buttons';
            }

            $columns_query_string = " (" . implode(", ", $columns_array) . ")";
            $values_query_string = "VALUES (" . implode(", ", $values_array) . ")";

            $setting_query = $osC_Database->query($query_string . ' ' . $columns_query_string . ' ' . $values_query_string);
        }

        /**
         * @var osC_Database_Result
         */
        if (key_exists("shopid", $params)) {
            $setting_query->bindValue(':shopid', $params['shopid']);
        }

        if (key_exists("api_key", $params)) {
            $setting_query->bindValue(':api_key', $params['api_key']);
        }

        if (key_exists("user_login", $params)) {
            $setting_query->bindValue(':user_login', $params['user_login']);
        }

        if (key_exists("user_password", $params)) {
            $setting_query->bindValue(':user_password', $params['user_password']);
        }

        if (key_exists("use_default_buttons", $params)) {
            $setting_query->bindInt(':use_default_buttons', $params['use_default_buttons']);
        }

        if (key_exists("use_open_graph_buttons", $params)) {
            $setting_query->bindInt(':use_open_graph_buttons', $params['use_open_graph_buttons']);
        }

        $setting_query->bindTable(':table_addshoppers', self::$table_name);
        $setting_query->execute();

        return true;
    }

    public static function getSettings() {
        global $osC_Database;

        $settings_query = $osC_Database->query('select * from :table_addshoppers limit 1');

        $settings_query->bindTable(':table_addshoppers', self::$table_name);
        $settings_query->execute();

        $data = $settings_query->toArray();

        $settings_query->freeResult();

        return $data;
    }

    public static function sendCurlRequest($path, $data) {

        $data = array_merge(array(
            'url' => ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER,
            'site_name' => STORE_NAME,
            'platform' => self::$platform,
            'category' => self::$defaultCategory
                ), $data);

        $curl = curl_init(self::$endpoint . $path);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTHBASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "addshop:addshop123");
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result, true);
    }

}

