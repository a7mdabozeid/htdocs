<?php

namespace Shiaka;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Regions
{
    protected static $instance = null;

    protected array $states;

    /**
     * Initiator
     *
     * @return object
     * @since 1.0.0
     */
    public static function instance($data)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($data);
        }

        return self::$instance;
    }


    public function __construct($data)
    {


        $this->set_states($data);
        $this->get_states_name();

        add_action('wp_ajax_nopriv_get_states_ajax', [$this, 'get_states_ajax']);
        add_action('wp_ajax_get_states_ajax', [$this, 'get_states_ajax']);

        add_action('wp_ajax_nopriv_get_cities_of_state_ajax', [$this, 'get_cities_of_state_ajax']);
        add_action('wp_ajax_get_cities_of_state_ajax', [$this, 'get_cities_of_state_ajax']);


    }

    public function set_states(array $states)
    {
        $this->states = $states;
    }


    public function get_states()
    {
        // get all states bulkجعرانة
        // check whether request action
        // Is rtl param && state code Param
        // only in one state
        return $this->states;

    }

    public function arabic_state($state): array
    {
        return $this->ar_states()[$state];
    }

    public function english_state($state): array
    {
        return $this->en_states()[$state];
    }



    protected function ar_states(): array
    {
        $ar_states = [];
        foreach ($this->get_states() as $state => $state_values) {
            if (is_array($state_values)) {
                foreach ($state_values as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if ($k === 'ar') {
                                $ar_states[$state][$key] = $v;
                            }
                        }
                    }
                }
            }
            $ar_states[$state]['code'] = $state;
        }
        return $ar_states;
    }

    protected function en_states(): array
    {
        $en_states = [];
        foreach ($this->get_states() as $state => $state_values) {
            if (is_array($state_values)) {
                foreach ($state_values as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if ($k === 'en') {
                                $en_states[$state][$key] = $v;
                            }
                        }
                    }
                }
            }


            $en_states[$state]['code'] = $state;

        }
        return $en_states;
    }

    public function get_states_name($language = 'en'): array
    {
        $states = [];
        if ($language === 'ar') {
            foreach ($this->ar_states() as $state) {
                $states[$state['code']] = $state['name'];
            }
        }
        if ($language === 'en') {
            foreach ($this->en_states() as $state) {
                $states[$state['code']] = $state['name'];
            }
        }
        return $states;
    }

    public function get_cities_of_state_ajax()
    {
        if (isset($_POST['state_code']) && isset($_POST['lang'])) {
            if ('ar' === $_POST['lang']) {
                $response = $this->ar_states()[$_POST['state_code']]['cites'];
                wp_send_json_success($response);
            }
            if ('en-US' === $_POST['lang']) {
                $response = $this->en_states()[$_POST['state_code']]['cites'];
                wp_send_json_success($response);
            }
            wp_send_json_error(['responce' => "error no more details"]);
        }
    }

    public function get_states_ajax()
    {
        // check if state code set
        if (isset($_POST['lang'])) {
            $lang = $_POST['lang'];
            $response = null;
            switch ($lang) {
                case 'ar' :
                    $response = $this->ar_states();
                    break;
                default  :
                    $response = $this->en_states();
            }
            // Key not in data set retrun success but no data in our set Happy code @khalil
            wp_send_json_success($response);
        }
        wp_send_json_error(['responce' => "error no more details"]);
    }



    public function get_state_cities($state)
    {
        return $this->get_state($state)['cites'];
    }


}