<?php
/**
 * Wrapper for the MailChimp API done The ezWay
 *
 * https://apidocs.mailchimp.com/api/downloads/  https://bitbucket.org/mailchimp/mailchimp-api-php/src
 *
 * PHP version 5.3
 *
 * LICENSE: TODO
 *
 * @package WPezClasses
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.0
 */

/**
 * CHANGE LOG
 *
 * --
 *
 */

/**
 * -- TODO --
 *
 */

if ( ! class_exists('Class_WP_ezClasses_API_Mailchimp_1') ) {

    class Class_WP_ezClasses_API_Mailchimp_1 extends Class_WP_ezClasses_Master_Singleton
    {

        protected $_version;
        protected $_url;
        protected $_path;
        protected $_path_parent;
        protected $_basename;
        protected $_file;

        protected $_arr_init;
        protected $_obj_mc;

        public function __construct() {
            parent::__construct();
        }

        /**
         *
         */
        public function ez__construct($str_api_key = ''){

            $this->setup();

            require('mailchimp/src/Mailchimp.php');

            $this->_arr_init = WPezHelpers::ez_array_merge( array ($this->init_defaults(), $this->mailchimp_todo() ) );

            if ( ! empty($str_api_key) && is_string($str_api_key) ){
                $this->_arr_init['api_key'] = $str_api_key;
            }

            $this->_obj_mc = new Mailchimp($this->_arr_init['api_key']);

        }



        /**
         *
         */
        protected function mailchimp_todo(){

            $arr_todo = array(
                'api_key'       => 'TODO',
                'list_web_id'   => 'TODO',           // list
            );

            return $arr_todo;
        }


        /**
         * @return array
         */
        protected function init_defaults(){

            $arr_defaults = array(

                'api_key'           => false,

                'get_lists_transient_active'             => true,
                'get_lists_transient_name'               => 'TODO_get_lists_transient_name',
                'get_lists_transient_expiration'         => 60 * 5,                     // once your lists are stable, jack this up. just keep in mind that if you add a new list you'll have delete the transient or make active false for a bit til the transient expires.

                'list_id'           => false,
                'list_web_id'       => false,

                'update_existing'   => true,
                'send_welcome'      => false,
                'double_optin'      => false,

            );

            return $arr_defaults;
        }

        /**
         * @param $url
         * @param $parms
         */
        public function call($url = 0, $parms = 0){

            if ( is_string($url) ){

                $arr_url_defaults = $this->get_call_defaults($url);

                // TODO - what if parms aren't an array?
                $arr_new_parms = WPezHelpers::ez_array_merge(array($arr_url_defaults, $parms ));

                return $this->_obj_mc->call($url, $arr_new_parms);
            }
        }

        /**
         * @param string $url
         * @return array
         */
        protected function get_call_defaults($url = ''){

            if ( isset ($this->call_defaults()[$url]) )
            {
                return $this->call_defaults()[$url];
            }

            return array();
        }

        /**
         *
         */
        protected function call_defaults(){

            $arr_call_defaults = array(

                'lists/subscribe'=> array(
                    'id'                => $this->get_id($this->_arr_init['list_web_id']),
                    'update_existing'   => $this->_arr_init['update_existing'],
                    'send_welcome'      => $this->_arr_init['send_welcome'],
                    'double_optin'      => $this->_arr_init['double_optin'],
                ),
            );

            return  $arr_call_defaults;
        }

        /**
         * @param int $key
         * @param int $value
         */
        public function set($key = 0, $value = 0){

            if ( $key != 0 and is_string($key)){

                $this->_arr_init[$key] = $value;
                return true;

            }
            return false;
        }


        /**
         *
         */
        public function get_lists()
        {

            if ( $this->_arr_init['get_lists_transient_active'] === true ) {

                $mix_get_transient = get_transient($this->_arr_init['get_lists_transient_name']);

                if ($mix_get_transient !== false){
                    return $mix_get_transient;
                }
                $arr_lists = $this->_obj_mc->call("lists/list", array());

                // TODO - check for errors before set_
                set_transient( $this->_arr_init['get_lists_transient_name'], $arr_lists, $this->_arr_init['get_lists_transient_expiration'] );
                return $arr_lists;
            }

            $arr_lists = $this->_obj_mc->call("lists/list", array());
            return $arr_lists;
        }

        /**
         * @param int $int_list_public_id
         */
        public function get_list($int_list_web_id = 0){

            if ($int_list_web_id != 0){

                $arr_lists = $this->get_lists();

                if ( isset($arr_lists['data']) ) {

                    if ( ! empty($arr_lists['data'])) {

                        foreach ($arr_lists['data'] as $arr_list){

                            if ( $arr_list['web_id'] == $int_list_web_id ){

                                return $arr_list;

                            }
                        }
                        return array ('error' => 'no list found for this web_id (' . $int_list_web_id . ')');
                    }
                    return array ('error' => 'no lists found for this account');
                }
                return array ('error' => 'problem retrieving lists from MC');
            }
            return array ('error' => 'public id not specified');
        }

        /**
         * Pass a web_id in get the (api friendly) id out
         *
         * @param int $int_list_web_id
         */
        public function get_id($int_list_web_id = 0){

            $arr_list = $this->get_list($int_list_web_id);

            if ( ! isset( $arr_list['error']) ){

                if ( isset($arr_list['id']) ){
                    return $arr_list['id'];
                }
                return array ('error' => 'this should never happen (lol)');
            }
            return $arr_list;
        }


        /**
         * all the usual suspects
         */
        protected function setup(){

            $this->_version = '0.5.0';
            $this->_url = plugin_dir_url( __FILE__ );
            $this->_path = plugin_dir_path( __FILE__ );
            $this->_path_parent = dirname($this->_path);
            $this->_basename = plugin_basename( __FILE__ );
            $this->_file = __FILE__ ;
        }

    }
}