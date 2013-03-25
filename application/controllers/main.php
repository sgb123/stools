<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package Adwords
 * @date 22.03.13 9:19
 */
class Main extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try
        {
            $user = $this->adwords_lib->get_adwords_user();
            $data['accounts'] = array();
            if(!empty($user))
            {
                $data['logged_in'] = true;
                $data['message'] = $this->session->flashdata('message');
                $accounts = $this->adwords_lib->get_accounts();
                if($accounts['status'] == 200)
                {
                    $data['accounts'] = $accounts['data']['accounts'];
                    $data['root_account'] = $accounts['data']['root_account'];
                }
                else
                {
                    $data['message'] = array(
                        'type' => 'error',
                        'message' => $accounts['reason']
                    );
                }
            }
            else
            {
                $data['logged_in'] = false;
            }
            $data['user'] = $user;
        }
        catch(Exception $e)
        {
            $data['message'] = array(
                'type' => 'error',
                'message' => $e->getMessage()
            );
        }
        $this->display_lib->display_page('main_view', $data);
    }

    public function login($callback = '')
    {
        try
        {
            if(!empty($callback))
            {
                // Retrieve the AdWordsUser from session.
                $user = $this->adwords_lib->get_adwords_user();

                // Upgrade to access token.
                $verifier = $this->input->get('oauth_verifier');
                $user->UpgradeOAuthToken($verifier);
                // Store AdWordsUser in session
                $this->adwords_lib->set_adwords_user($user);
                redirect('main');
            }
            $authorization_url = $this->adwords_lib->login();
            redirect($authorization_url);
        }
        catch(OAuthException $e)
        {
            $message = array(
                'type' => 'error',
                'message' => $e->getTraceAsString()
            );
            $this->session->set_flashdata('message', $message);
            $this->adwords_lib->remove_adwords_user();
        }
    }

    public function logout()
    {
        $this->adwords_lib->remove_adwords_user();
        $this->session->flashdata('message', array(
            'type' => 'success',
            'message' => 'Log out successfully'
        ));
        redirect('main');
    }

    public function page_rank()
    {
        $user = $this->adwords_lib->get_adwords_user();
        if(empty($user))
        {
            redirect('main');
        }
        $this->display_lib->display_page('page_rank_view');
    }

    public function billing()
    {
        $user = $this->adwords_lib->get_adwords_user();
        if(empty($user))
        {
            redirect('main');
        }

        $accounts = $this->adwords_lib->get_accounts();
        if($accounts['status'] == 200)
        {
            $data['accounts'] = $accounts['data']['accounts'];
        }

//        $billing = $this->adwords_lib->get_billing_info();
        $account_alerts = $this->adwords_lib->get_account_alerts();
        if($account_alerts['status'] == 200)
        {
            $data['alerts'] = $account_alerts['data'];
            $data['message'] = '';
        }
        else
        {
            $data['alerts'] = array();
            $data['message'] = $account_alerts['reason'];
        }
        $this->display_lib->display_page('billing_view', $data);
    }
}
