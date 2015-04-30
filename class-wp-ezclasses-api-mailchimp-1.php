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

        protected $_str_api_key;
        protected $_obj_mc;

        public function __construct() {
            parent::__construct();
        }

        /**
         *
         */
        public function ez__construct($arr_args = ''){

            $this->setup();

            $this->mailchimp_todo();

            require('mailchimp/src/Mailchimp.php');

            $this->_obj_mc = new Mailchimp($this->_str_api_key);


            $listid = "fcbb18ca26";

            $MailChimp = $this->_obj_mc;

         //   $x = $this->get_lists();
            $x = $this->get_list(11920730);
        //    print_r($x);

            echo '<br><br>';
            $x = $this->get_id(1192073);
        //    print_r($x);
            var_dump($x);




        //    $result=$MailChimp->call("lists/list");
        //        var_dump($result);

            $result=$MailChimp->call("lists/subscribe", array(
                "id"=>$listid,
                "email"=>array("email"=>"mark.simchock@alchemyunited.com"),
                "update_existing"=>true,
                "send_welcome"=>false,
            ));
        //    var_dump($result);


        }



        /**
         *
         */
        protected function mailchimp_todo(){

            $this->_str_api_key = 'TODO';

        }


        /**
         *
         */
        public function get_lists(){

            $arr_lists = $this->_obj_mc->call("lists/list");

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