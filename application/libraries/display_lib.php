<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Display_Lib
{
    private $_CI;
    private $_template_data;

    public function __construct()
    {
        $this->_CI =& get_instance();
    }

    public function set($key, $value)
    {
        $this->_template_data[$key] = $value;
    }

    public function get($key)
    {
        return $this->_template_data[$key];
    }

    public function getTemplateData()
    {
        return $this->_template_data;
    }

    /**
     * Method show single page view
     *
     * @access public
     * @param $view
     * @param array $data
     */
    public function display_page($view, $data = array())
    {
        //set main content
        $this->set('content', $this->_CI->load->view($view, $data, TRUE));
        $this->_CI->load->view('templates/main_template', $this->getTemplateData());
    }

    public function is_loggedin()
    {
        $user = $this->_CI->adwords_lib->get_adwords_user();
        return (empty($user)) ? FALSE : TRUE;
    }
}