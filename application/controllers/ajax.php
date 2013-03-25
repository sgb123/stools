<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller
{
    const STATUS_SUCCESS = 200;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_APPLICATION_ERROR = 500;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_accounts()
    {
        if($this->input->is_ajax_request())
        {
            $result = $this->adwords_lib->get_accounts();
        }
        else
        {
            $result = array(
                'status' => self::STATUS_UNAUTHORIZED,
                'reason' => 'You don\'t have permission for this method.'
            );
        }
        echo json_encode($result);
    }

    public function get_account_campaigns($account_id)
    {
        if($this->input->is_ajax_request())
        {
            if(!empty($account_id))
            {
                $result = $this->adwords_lib->get_campaigns($account_id);
            }
            else
            {
                $result = array('status' => self::STATUS_BAD_REQUEST, 'reason' => 'Required parameters is missing');
            }
        }
        else
        {
            $result = array(
                'status' => self::STATUS_UNAUTHORIZED,
                'reason' => 'You don\'t have permission for this method.'
            );
        }
        echo json_encode($result);
    }

    public function get_page_rank()
    {
        if($this->input->is_ajax_request())
        {
            $keywords = ($this->input->get('keywords')) ? $this->input->get('keywords') : '';
            $page_url = ($this->input->get('page_url')) ? $this->input->get('page_url') : '';
            $result = $this->adwords_lib->get_page_rank($page_url, $keywords);
        }
        else
        {
            $result = array(
                'status' => self::STATUS_UNAUTHORIZED,
                'reason' => 'You don\'t have permission for this method.'
            );
        }
        echo json_encode($result);
    }

    public function get_single_page_rank()
    {
        if($this->input->is_ajax_request())
        {
            $page_url = ($this->input->get('page_url')) ? $this->input->get('page_url') : '';
            $result = $this->adwords_lib->get_page_rank($page_url, '');
        }
        else
        {
            $result = array(
                'status' => self::STATUS_UNAUTHORIZED,
                'reason' => 'You don\'t have permission for this method.'
            );
        }
        echo json_encode($result);
    }
}