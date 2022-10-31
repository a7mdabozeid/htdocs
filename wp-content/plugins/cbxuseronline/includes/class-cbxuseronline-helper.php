<?php

/**
 * The file that defines the helper plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXUserOnline
 * @subpackage CBXUserOnline/includes
 */


/**
 * The helper plugin class.
 *
 * This is used to define some helper methods that can be used in frontend and backend
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CBXUserOnline
 * @subpackage CBXUserOnline/includes
 * @author     codeboxr <info@codeboxr.com>
 */
class CBXUserOnlineHelper
{
    /**
     * Create tables on plugin activate, resets etc
     */
    public static function create_tables()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();


        $useronline_table = $wpdb->prefix.'cbxuseronline';

        require_once(ABSPATH."wp-admin/includes/upgrade.php");


        $sql = "CREATE TABLE $useronline_table (
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `user_type` varchar( 20 ) NOT NULL default 'guest',
		  `userid` varchar ( 25 ) NOT NULL default '',
		  `user_name` varchar( 250 ) NOT NULL default '',
		  `user_ip` varchar( 39 ) NOT NULL default '',
		  `user_agent` text NOT NULL,
		  `page_title` text NOT NULL,
		  `page_url` varchar( 255 ) NOT NULL default '',
		  `referral` varchar( 255 ) NOT NULL default '',
		  `mobile` tinyint(1 ) NOT NULL default '0'
          ) $charset_collate;";


        dbDelta($sql);
    }//end method create_tables

    /**
     * @return string
     */
    public static function cbxuseronline_table_name()
    {
        global $wpdb;

        return $wpdb->prefix."cbxuseronline";
    }//end method cbxuseronline_table_name

    /**
     * Get all  core tables list
     */
    public static function getAllDBTablesList()
    {
        global $wpdb;

        $table_names                  = array();
        $table_names['cbxuseronline'] = CBXUseronlineHelper::cbxuseronline_table_name();


        return apply_filters('cbxuseronlinetable_list', $table_names);
    }//end method getAllDBTablesList

    /**
     * List all global option name with prefix cbxuseronline_
     */
    public static function getAllOptionNames()
    {
        global $wpdb;

        $prefix       = 'cbxuseronline_';
        $option_names = $wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'", ARRAY_A);

        return apply_filters('cbxuseronline_option_names', $option_names);
    }//end method getAllOptionNames

    /**
     * initialize cookie
     */
    public static function init_cookie()
    {

        $php_file = basename($_SERVER["SCRIPT_FILENAME"]);
        if($php_file == 'wp-cron.php' )  return;
        //write_log(DOING_CRON);
        /*if (defined('DOING_CRON') && DOING_CRON ) {
            return;
        }*/
        if (is_admin()) {
            return;
        }
        if (is_user_logged_in()) {
            return;
        }

        //write_log($_REQUEST['doing_wp_cron']);

        //if(isset($_REQUEST['doing_wp_cron'])) return;

        //write_log('cookie init work');


        $cookie_value = 'wpuseronlineguest-'.rand(CBX_USERONLINE_RAND_MIN, CBX_USERONLINE_RAND_MAX);
        if ( ! isset($_COOKIE[CBX_USERONLINE_COOKIE_NAME]) && empty($_COOKIE[CBX_USERONLINE_COOKIE_NAME])) {

            setcookie(CBX_USERONLINE_COOKIE_NAME, $cookie_value, CB_RATINGSYSTEM_COOKIE_EXPIRATION_30DAYS, SITECOOKIEPATH, COOKIE_DOMAIN);

            //$_COOKIE var accepts immediately the value so it will be retrieved on page first load.
            $_COOKIE[CBX_USERONLINE_COOKIE_NAME] = $cookie_value;

        } elseif (isset($_COOKIE[CBX_USERONLINE_COOKIE_NAME])) {


            if (substr($_COOKIE[CBX_USERONLINE_COOKIE_NAME], 0, 17) != 'wpuseronlineguest') {
                setcookie(CBX_USERONLINE_COOKIE_NAME, $cookie_value, CB_RATINGSYSTEM_COOKIE_EXPIRATION_30DAYS, SITECOOKIEPATH, COOKIE_DOMAIN);

                //$_COOKIE var accepts immediately the value so it will be retrieved on page first load.
                $_COOKIE[CBX_USERONLINE_COOKIE_NAME] = $cookie_value;
            }
        }


    }//end method init_cookie

    /**
     * Get Ip of the current visitor
     *
     * @return array
     * @since 1.0.0
     *
     */
    public static function get_ipaddress()
    {
        if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {

            $ip_address = $_SERVER["REMOTE_ADDR"];
        } else {

            $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }

        if (strpos($ip_address, ',') !== false) {

            $ip_address = explode(',', $ip_address);
            $ip_address = $ip_address[0];
        }

        return esc_attr($ip_address);
    }//end method get_ipaddress

    /**
     * Returns popular bot names as array
     *
     * @return array
     * @since 1.0.0
     *
     */
    public static function get_bots()
    {
        $bots = array(
            'Google Bot'    => 'google',
            'MSN'           => 'msnbot',
            'BingBot'       => 'bingbot',
            'Alex'          => 'ia_archiver',
            'Lycos'         => 'lycos',
            'Ask Jeeves'    => 'jeeves',
            'Altavista'     => 'scooter',
            'AllTheWeb'     => 'fast-webcrawler',
            'Inktomi'       => 'slurp@inktomi',
            'Turnitin.com'  => 'turnitinbot',
            'Technorati'    => 'technorati',
            'Yahoo'         => 'yahoo',
            'Findexa'       => 'findexa',
            'NextLinks'     => 'findlinks',
            'Gais'          => 'gaisbo',
            'WiseNut'       => 'zyborg',
            'WhoisSource'   => 'surveybot',
            'Bloglines'     => 'bloglines',
            'BlogSearch'    => 'blogsearch',
            'PubSub'        => 'pubsub',
            'Syndic8'       => 'syndic8',
            'RadioUserland' => 'userland',
            'Gigabot'       => 'gigabot',
            'Become.com'    => 'become.com',
            'Baidu'         => 'baidu',
            'Yandex'        => 'yandex',
            'Amazon'        => 'amazonaws.com',
            'Ahrefs'        => 'AhrefsBot',
            'Yandex Bot'    => 'YandexBot' //added new
        );

        return apply_filters('cbxuseronline_bots', $bots);
    }//end method get_bots


    public static function get_referral()
    {
        $referral = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referral = sanitize_text_field($_SERVER['HTTP_REFERER']);
        } else {
            $referral = '';
        }

        return apply_filters('cbxuseronline_referral', $referral);
    }//end method get_referral

    /**
     * Get user agent
     *
     * @return string
     * @since 1.0.0
     *
     */
    public static function get_useragent()
    {
        $user_agent = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        } else {
            $user_agent = '';
        }

        return apply_filters('cbxuseronline_useragents', $user_agent);
    }//end method get_useragent

    public static function get_tablename()
    {
        global $wpdb;

        return $wpdb->prefix."cbxuseronline";
    }//end method get_tablename

    /**
     * Detection mobile
     *
     * @since 1.0.0
     *
     * return true if mobile or false
     */
    public static function is_mobile()
    {
        return wp_is_mobile();
    }//end method is_mobile

    /**
     * Detection mobile
     *
     * @since 1.0.0
     *
     * return true if mobile or false
     */
    public static function is_mobile_deprecated()
    {

        $user_agent = strtolower(CBXUseronlineHelper::get_useragent());

        $accept = strtolower($_SERVER['HTTP_ACCEPT']);

        if (
            0
            or preg_match('/ip[ao]d/', $user_agent)
            or preg_match('/iphone/', $user_agent) //iPhone or iPod
            or preg_match('/android/', $user_agent) //Android
            or preg_match('/opera mini/', $user_agent) //Opera Mini
            or preg_match('/blackberry/', $user_agent) //Blackberry
            or preg_match('/series ?60/', $user_agent) //Symbian OS
            or preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/', $user_agent) //Palm OS
            or preg_match('/(iris|3g_t|windows ce|opera mobi|iemobile)/', $user_agent) //Windows OS
            or preg_match('/(maemo|tablet|qt embedded|com2)/', $user_agent) //Nokia Tablet
        ) {
            return true;
        }

        /**
         * Now look for standard phones & mobile devices
         */

        //Mix of standard phones
        if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo|vnd.rim|wml|nitro|nintendo|wii|xbox|archos|openweb|mini|docomo)/',
            $user_agent)) {
            return true;
        }

        //Any falling through the cracks
        if (
            0
            or strpos($accept, 'text/vnd.wap.wml') > 0
            or strpos($accept, 'application/vnd.wap.xhtml+xml') > 0
            or isset($_SERVER['HTTP_X_WAP_PROFILE'])
            or isset($_SERVER['HTTP_PROFILE'])
        ) {
            return true;
        }

        //Catch all
        if (
        in_array(
            substr($user_agent, 0, 4),
            array(
                '1207',
                '3gso',
                '4thp',
                '501i',
                '502i',
                '503i',
                '504i',
                '505i',
                '506i',
                '6310',
                '6590',
                '770s',
                '802s',
                'a wa',
                'acer',
                'acs-',
                'airn',
                'alav',
                'asus',
                'attw',
                'au-m',
                'aur ',
                'aus ',
                'abac',
                'acoo',
                'aiko',
                'alco',
                'alca',
                'amoi',
                'anex',
                'anny',
                'anyw',
                'aptu',
                'arch',
                'argo',
                'bell',
                'bird',
                'bw-n',
                'bw-u',
                'beck',
                'benq',
                'bilb',
                'blac',
                'c55/',
                'cdm-',
                'chtm',
                'capi',
                'cond',
                'craw',
                'dall',
                'dbte',
                'dc-s',
                'dica',
                'ds-d',
                'ds12',
                'dait',
                'devi',
                'dmob',
                'doco',
                'dopo',
                'el49',
                'erk0',
                'esl8',
                'ez40',
                'ez60',
                'ez70',
                'ezos',
                'ezze',
                'elai',
                'emul',
                'eric',
                'ezwa',
                'fake',
                'fly-',
                'fly_',
                'g-mo',
                'g1 u',
                'g560',
                'gf-5',
                'grun',
                'gene',
                'go.w',
                'good',
                'grad',
                'hcit',
                'hd-m',
                'hd-p',
                'hd-t',
                'hei-',
                'hp i',
                'hpip',
                'hs-c',
                'htc ',
                'htc-',
                'htca',
                'htcg',
                'htcp',
                'htcs',
                'htct',
                'htc_',
                'haie',
                'hita',
                'huaw',
                'hutc',
                'i-20',
                'i-go',
                'i-ma',
                'i230',
                'iac',
                'iac-',
                'iac/',
                'ig01',
                'im1k',
                'inno',
                'iris',
                'jata',
                'java',
                'kddi',
                'kgt',
                'kgt/',
                'kpt ',
                'kwc-',
                'klon',
                'lexi',
                'lg g',
                'lg-a',
                'lg-b',
                'lg-c',
                'lg-d',
                'lg-f',
                'lg-g',
                'lg-k',
                'lg-l',
                'lg-m',
                'lg-o',
                'lg-p',
                'lg-s',
                'lg-t',
                'lg-u',
                'lg-w',
                'lg/k',
                'lg/l',
                'lg/u',
                'lg50',
                'lg54',
                'lge-',
                'lge/',
                'lynx',
                'leno',
                'm1-w',
                'm3ga',
                'm50/',
                'maui',
                'mc01',
                'mc21',
                'mcca',
                'medi',
                'meri',
                'mio8',
                'mioa',
                'mo01',
                'mo02',
                'mode',
                'modo',
                'mot ',
                'mot-',
                'mt50',
                'mtp1',
                'mtv ',
                'mate',
                'maxo',
                'merc',
                'mits',
                'mobi',
                'motv',
                'mozz',
                'n100',
                'n101',
                'n102',
                'n202',
                'n203',
                'n300',
                'n302',
                'n500',
                'n502',
                'n505',
                'n700',
                'n701',
                'n710',
                'nec-',
                'nem-',
                'newg',
                'neon',
                'netf',
                'noki',
                'nzph',
                'o2 x',
                'o2-x',
                'opwv',
                'owg1',
                'opti',
                'oran',
                'p800',
                'pand',
                'pg-1',
                'pg-2',
                'pg-3',
                'pg-6',
                'pg-8',
                'pg-c',
                'pg13',
                'phil',
                'pn-2',
                'pt-g',
                'palm',
                'pana',
                'pire',
                'pock',
                'pose',
                'psio',
                'qa-a',
                'qc-2',
                'qc-3',
                'qc-5',
                'qc-7',
                'qc07',
                'qc12',
                'qc21',
                'qc32',
                'qc60',
                'qci-',
                'qwap',
                'qtek',
                'r380',
                'r600',
                'raks',
                'rim9',
                'rove',
                's55/',
                'sage',
                'sams',
                'sc01',
                'sch-',
                'scp-',
                'sdk/',
                'se47',
                'sec-',
                'sec0',
                'sec1',
                'semc',
                'sgh-',
                'shar',
                'sie-',
                'sk-0',
                'sl45',
                'slid',
                'smb3',
                'smt5',
                'sp01',
                'sph-',
                'spv ',
                'spv-',
                'sy01',
                'samm',
                'sany',
                'sava',
                'scoo',
                'send',
                'siem',
                'smar',
                'smit',
                'soft',
                'sony',
                't-mo',
                't218',
                't250',
                't600',
                't610',
                't618',
                'tcl-',
                'tdg-',
                'telm',
                'tim-',
                'ts70',
                'tsm-',
                'tsm3',
                'tsm5',
                'tx-9',
                'tagt',
                'talk',
                'teli',
                'topl',
                'hiba',
                'up.b',
                'upg1',
                'utst',
                'v400',
                'v750',
                'veri',
                'vk-v',
                'vk40',
                'vk50',
                'vk52',
                'vk53',
                'vm40',
                'vx98',
                'virg',
                'vite',
                'voda',
                'vulc',
                'w3c ',
                'w3c-',
                'wapj',
                'wapp',
                'wapu',
                'wapm',
                'wig ',
                'wapi',
                'wapr',
                'wapv',
                'wapy',
                'wapa',
                'waps',
                'wapt',
                'winc',
                'winw',
                'wonu',
                'x700',
                'xda2',
                'xdag',
                'yas-',
                'your',
                'zte-',
                'zeto',
                'acs-',
                'alav',
                'alca',
                'amoi',
                'aste',
                'audi',
                'avan',
                'benq',
                'bird',
                'blac',
                'blaz',
                'brew',
                'brvw',
                'bumb',
                'ccwa',
                'cell',
                'cldc',
                'cmd-',
                'dang',
                'doco',
                'eml2',
                'eric',
                'fetc',
                'hipt',
                'http',
                'ibro',
                'idea',
                'ikom',
                'inno',
                'ipaq',
                'jbro',
                'jemu',
                'java',
                'jigs',
                'kddi',
                'keji',
                'kyoc',
                'kyok',
                'leno',
                'lg-c',
                'lg-d',
                'lg-g',
                'lge-',
                'libw',
                'm-cr',
                'maui',
                'maxo',
                'midp',
                'mits',
                'mmef',
                'mobi',
                'mot-',
                'moto',
                'mwbp',
                'mywa',
                'nec-',
                'newt',
                'nok6',
                'noki',
                'o2im',
                'opwv',
                'palm',
                'pana',
                'pant',
                'pdxg',
                'phil',
                'play',
                'pluc',
                'port',
                'prox',
                'qtek',
                'qwap',
                'rozo',
                'sage',
                'sama',
                'sams',
                'sany',
                'sch-',
                'sec-',
                'send',
                'seri',
                'sgh-',
                'shar',
                'sie-',
                'siem',
                'smal',
                'smar',
                'sony',
                'sph-',
                'symb',
                't-mo',
                'teli',
                'tim-',
                'tosh',
                'treo',
                'tsm-',
                'upg1',
                'upsi',
                'vk-v',
                'voda',
                'vx52',
                'vx53',
                'vx60',
                'vx61',
                'vx70',
                'vx80',
                'vx81',
                'vx83',
                'vx85',
                'wap-',
                'wapa',
                'wapi',
                'wapp',
                'wapr',
                'webc',
                'whit',
                'winw',
                'wmlb',
                'xda-'
            )
        )
        ) {
            return true;
        }

        return false;
    }// end of _check_mobile_device

    /**
     * Get online users
     *
     * @param  string  $page
     *
     * @return array
     */
    public static function user_online($page = '')
    {

        global $wpdb;
        $cbxuseronline_tablename = CBXUseronlineHelper::get_tablename();


        $where = '';
        if ($page != '') {

            $where = $wpdb->prepare(' WHERE page_url = %s ', $page);
        }


        $userdata = $wpdb->get_results("SELECT * FROM $cbxuseronline_tablename ".$where." ORDER BY timestamp DESC");

        $userdata_group = array();

        foreach ($userdata as $user) {
            $userdata_group[$user->user_type][] = $user;
        }

        return array('users_bygroup' => $userdata_group, 'count' => sizeof($userdata));

    }//end method user_online

    public static function cbxuseronline_display($atts, $scope = "shortcode")
    {

        extract($atts, EXTR_SKIP);

        $userdata = CBXUseronlineHelper::user_online($page);

        //$output = '';
        $output_members            = '';
        $output_online_count       = '';
        $output_online_count_parts = '';

        //usercount
        if ($count) {
            $user_count = isset($userdata['count']) ? intval($userdata['count']) : 0;


            $output_online_count .= CBXUseronlineHelper::get_correct_plugral_text($user_count, __('Total <strong>%</strong> users', 'cbxuseronline'), __('Total <strong>%</strong> user', 'cbxuseronline'));
        }


        if ($member_count && $count_individual) {
            $members       = isset($userdata['users_bygroup']['user']) ? $userdata['users_bygroup']['user'] : array();
            $members_count = sizeof($members);

            if ($output_online_count_parts != '' && $member_count == 1) {

                $output_online_count_parts .= ',';

            }


            $output_online_count_parts .= CBXUseronlineHelper::get_correct_plugral_text($members_count, __(' <strong>%</strong> members', 'cbxuseronline'), __(' <strong>%</strong> member', 'cbxuseronline'));


        }


        if ($guest_count && $count_individual) {
            $guest        = isset($userdata['users_bygroup']['guest']) ? $userdata['users_bygroup']['guest'] : array();
            $guests_count = sizeof($guest);
            if ($output_online_count_parts != '' && $guest_count) {

                $output_online_count_parts .= ',';

            }


            $output_online_count_parts .= CBXUseronlineHelper::get_correct_plugral_text($guests_count, __(' <strong>%</strong> guests', 'cbxuseronline'), __(' <strong>%</strong> guest', 'cbxuseronline'));
        }


        if ($bot_count && $count_individual) {
            $bot        = isset($userdata['users_bygroup']['bot']) ? $userdata['users_bygroup']['bot'] : array();
            $bots_count = sizeof($bot);


            if ($output_online_count_parts != '' && ($bot_count)) {
                $output_online_count_parts .= ',';
            }

            $output_online_count_parts .= CBXUseronlineHelper::get_correct_plugral_text($bots_count, __(' <strong>%</strong> bots', 'cbxuseronline'), __(' <strong>%</strong> bot', 'cbxuseronline'));
        }

        if ($output_online_count_parts != '' && $count_individual) {
            $output_online_count .= esc_html__(' including', 'cbxuseronline');
            $output_online_count .= $output_online_count_parts;
        }

        $output_online_count .= esc_html__(' online', 'cbxuseronline');
        if ($page != '') {
            $output_online_count .= esc_html__(' on this page', 'cbxuseronline');
        }
        $output_online_count = '<p class="cbxuseronline_total_count">'.$output_online_count.'</p>';


        $mostuseronline_html = '';
        if ($mostuseronline) {
            $mostuser = get_option('cbxuseronline_mostonline');

            $mostuser_count = isset($mostuser['count']) ? intval($mostuser['count']) : 0;
            $mostuser_date  = isset($mostuser['date']) ? intval($mostuser['date']) : 0;

            $mysql_date = false;

            if ($mysql_date) {
                $mostuser_date = mysql2date(sprintf(__('%s @ %s', 'cbxuseronline'), get_option('date_format', __('F j, Y')), get_option('time_format', __('g:i a'))), $mostuser_date, true);
            } else {
                $mostuser_date = date_i18n(sprintf(__('%s @ %s', 'cbxuseronline'), get_option('date_format', __('F j, Y')), get_option('time_format', __('g:i a'))), $mostuser_date);

            }


            $mostuseronline_html = '<p class="cbxuseronline_most_userinfo">'.sprintf(__('Most users ever online were <strong>%d</strong>, on %s', 'cbxuseronline'), $mostuser_count, $mostuser_date).'</p>';
        }

        if ($memberlist && isset($userdata['users_bygroup']['user'])) {

            $output_members .= '<ul class="cbxuseronline_memberlist cbxuseronline_memberlist_'.$scope.'">';


            foreach ($userdata['users_bygroup']['user'] as $member) {

                $single_member_html = '';

                $mobile_label       = '';
                $mobile_label_class = '';

                if ($mobile) {
                    $mobile_label = '<span class="cbxuseronline_'.(($member->mobile) ? 'mobile' : 'desktop').' '.apply_filters('mobile_label_class', $mobile_label_class, $atts).'">&nbsp;</span>';
                }

                $memberlist_css_class = 'cbxuseronline_memberlist_item';
                $memberlist_css_class = apply_filters('memberlist_css_class', $memberlist_css_class, $atts);

                $member_name = apply_filters('cbxuseronline_memberitemname', $member->user_name, $atts);


                $user_profile_link = get_author_posts_url($member->userid);
                $user_profile_link = apply_filters('cbxuseronline_member_profile_link', $user_profile_link, $member->userid);

                $cbxuseronline_membername_classname = apply_filters('cbxuseronline_membername_classname', 'cbxuseronline_membername', $member->userid);

                $member_name_html = (($member_name != '') ? '<span class="'.$cbxuseronline_membername_classname.'">'.$member_name.'</span>' : '').$mobile_label;

                $single_member_html_start = '<li class="'.$memberlist_css_class.'">';

                if ($linkusername) {
                    $single_member_html .= '<a class="cbxuseronline_memberlist_item_name" title="'.$member->user_name.'" href="'.$user_profile_link.'">';
                } else {
                    $single_member_html .= '<div class="cbxuseronline_memberlist_item_name">';
                }

                $single_member_html .= apply_filters('cbxuseronline_memberitemhtml', $member_name_html, $member->userid, $atts, $member);

                if ($linkusername) {
                    $single_member_html .= '</a>';
                } else {
                    $single_member_html .= '</div>';
                }

                $single_member_html = apply_filters('cbxuseronline_memberitemhtml_extra', $single_member_html, $member->userid, $atts, $member);


                $single_member_html_end = '</li>';

                $single_member_html = $single_member_html_start.$single_member_html.$single_member_html_end;

                $output_members .= $single_member_html;
            }


            $output_members .= '</ul>';
            $output_members .= '<div class="cbxuseronlineclear"></div>';
        }


        return '<div class="cbxuseronline cbxuseronline_'.$scope.'">'.$output_online_count.$mostuseronline_html.$output_members.'</div>';
    }//end method cbxuseronline_display

    public static function cbxuseronline_number($nooped_plural, $count, $text_domain)
    {

        return printf(translate_nooped_plural($nooped_plural, $count, $text_domain), $count);
    }//end method cbxuseronline_number


    /**
     * Displays the plural strings if value is more than 1, unless singular
     *
     * @param $number   interger
     * @param $plural   string
     * @param $singular string
     *
     * @return string
     */
    public static function get_correct_plugral_text($number, $plural, $singular)
    {
        if ($number > 1) {
            $output = str_replace('%', number_format_i18n($number), $plural);
        } else {
            //less than 1
            $output = str_replace('%', number_format_i18n($number), $singular);
        }

        return $output;
    }//end method get_correct_plugral_text

    /**
     * Add utm params to any url
     *
     * @param  string  $url
     *
     * @return string
     */
    public static function url_utmy($url = '')
    {
        if ($url == '') {
            return $url;
        }

        $url = add_query_arg(array(
            'utm_source'   => 'plgsidebarinfo',
            'utm_medium'   => 'plgsidebar',
            'utm_campaign' => 'wpfreemium',
        ), $url);

        return $url;
    }//end url_utmy

    /**
     * Returns true/false based on user online by user id
     *
     * @param  int  $user_id
     *
     * @return true/false
     */
    public static function is_user_online($user_id = 0)
    {
        $user_online = false;

        $user_id = intval($user_id);
        if ($user_id == 0) {
            return $user_online;
        }


        global $wpdb;
        $user_online_table = CBXUseronlineHelper::get_tablename();


        $where = $wpdb->prepare(' WHERE userid = %d ', intval($user_id));

        $data = $wpdb->get_row("SELECT * FROM $user_online_table $where");
        if ($data !== null && is_object($data)) {
            $user_online = true;
        }

        return $user_online;
    }//end is_user_online

}//end class CBXUseronlineHelper