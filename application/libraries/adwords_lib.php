<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package
 * @date 22.03.13 9:16
 */
class Adwords_Lib
{
    private $_CI;
    private $_oauth_customer_key = 'anonymous';
    private $_oauth_consumer_secret = 'anonymous';

    const STATUS_SUCCESS = 200;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_APPLICATION_ERROR = 500;

    public function __construct()
    {
        $this->_CI =& get_instance();

        $path = dirname(__FILE__).'';
        set_include_path(get_include_path().PATH_SEPARATOR.$path);

        require_once 'Google/Api/Ads/AdWords/Lib/AdWordsUser.php';
        require_once 'SessionManager.php';
        require_once 'seostats/seostats.php';
    }

    public function login()
    {
        $oauth_info = array(
            'oauth_consumer_key' => $this->_oauth_customer_key,
            'oauth_consumer_secret' => $this->_oauth_consumer_secret
        );

        // Create the AdWordsUser and set the OAuth info.
        $auth_file = ROOT_PATH.'auth.ini';
        $settings_file = ROOT_PATH.'settings.ini';
        $user = new AdWordsUser($auth_file, NULL, NULL, NULL, NULL, NULL, NULL, $settings_file);
        $user->SetOAuthInfo($oauth_info);

        $callback_url = site_url('main/login/1');

        // Request a new OAuth token.
        $user->RequestOAuthToken($callback_url);

        // Get authorization URL.
        $authorization_url = $user->GetOAuthAuthorizationUrl();

        // Store the AdWordsUser in session.
        $this->set_adwords_user($user);

        return $authorization_url;
    }

    /**
     * Method save adwords user in session
     *
     * @access public
     * @param AdWordsUser $user
     */
    public function set_adwords_user(AdWordsUser $user)
    {
        session_start();
        $_SESSION['AW_USER'] = serialize($user);
        session_write_close();
//        $this->_CI->session->set_userdata(array('AW_USER' => $user));
//        $this->_CI->input->set_cookie('AW_SESSION_ACTIVE', 'true');
        setcookie('AW_SESSION_ACTIVE', 'true');
    }

    /**
     * Method get adwords user from session
     *
     * @access public
     * @return object|null
     */
    public function get_adwords_user()
    {
        session_start();
        $user = NULL;
        if (isset($_SESSION['AW_USER']))
        {
            $user = unserialize($_SESSION['AW_USER']);
        }
        session_write_close();
//        $user = $this->_CI->session->userdata('AW_USER');
        return $user;
    }

    /**
     * Method remove adwords user from session
     *
     * @access public
     * @return void
     */
    public function remove_adwords_user()
    {
        session_start();
        $_SESSION['AW_USER'] = NULL;
        session_write_close();
//        $this->_CI->session->unset_userdata('AW_USER');
//        $this->_CI->input->set_cookie('AW_SESSION_ACTIVE', 'false', -3600);
        setcookie('AW_SESSION_ACTIVE', 'false');
        $this->remove_adwords_accounts();
    }

    /**
     * Method store adwords accounts in session
     *
     * @access public
     * @param $accounts
     */
    public function store_adwords_accounts($accounts)
    {
        session_start();
        $_SESSION['AW_Accounts'] = serialize($accounts);
        session_write_close();
    }

    /**
     * Method get adwords accounts from session
     *
     * @access public
     * @return array
     */
    public function get_adwords_accounts()
    {
        session_start();
        $accounts = array();
        if(isset($_SESSION['AW_Accounts']))
        {
            $accounts = unserialize($_SESSION['AW_Accounts']);
        }
        session_write_close();
        return $accounts;
    }

    /**
     * Method remove adwords accounts from session
     *
     * @access public
     */
    public function remove_adwords_accounts()
    {
        session_start();
        unset($_SESSION['AW_Accounts']);
        session_write_close();
    }

    /**
     * Method get adwords customer accounts by root account
     *
     * @access public
     * @return array
     */
    public function get_accounts()
    {
        try
        {
            $user = $this->get_adwords_user();

            // Get the ServicedAccountService.
            $managed_customer_service = $user->GetService('ManagedCustomerService', ADWORDS_VERSION);

            // Create selector.
            $selector = new Selector();
            // Specify the fields to retrieve.
            $selector->fields = array(
                'Login', 'CustomerId', 'Name'
            );
            $selector->ordering = array(
                'field' => 'Name',
                'sortOrder' => 'ASCENDING'
            );

            // Get serviced account graph.
            $graph = $managed_customer_service->get($selector);

            // Create map from customerId to parent links.
            $parent_links = array();
            if(isset($graph->links))
            {
                foreach($graph->links as $link)
                {
                    $parent_links[$link->clientCustomerId][] = $link;
                }
            }

            // Create map from customerID to account, and find root account.
            $accounts = array();
            $root_account = NULL;
            foreach ($graph->entries as $account) {
//                $accounts[$account->customerId] = $account;
                $accounts[] = $account;
                if (!array_key_exists($account->customerId, $parent_links)) {
                    $root_account = $account;
                }
            }
            $data['root_account'] = $root_account;

//            $data['accounts'] = $this->_filter_accounts($accounts, true);
            $data['accounts'] = $accounts;
            $result = array('status' => self::STATUS_SUCCESS, 'data' => $data);
        }
        catch(Exception $e)
        {
            $result = array('status' => self::STATUS_APPLICATION_ERROR, 'reason' => $e->getMessage());
        }
        return $result;
    }

    /**
     * Method get campaigns by adwords customer id
     *
     * @access public
     * @param $customer_id
     * @param string $status
     * @return array
     */
    public function get_campaigns($customer_id, $status = 'ACTIVE')
    {
        try
        {
            $user = $this->get_adwords_user();
            $user->SetClientId($customer_id);

            // Get the CampaignService.
            $campaign_service = $user->GetService('CampaignService', ADWORDS_VERSION);

            // Create selector.
            $selector = new Selector();
            $selector->fields = array(
                'Id', 'Name', 'Status', 'Amount', 'Clicks', 'Cost', 'Ctr'
            );
            if(!empty($status))
            {
                $selector->predicates[] = new Predicate('Status', 'IN', array($status));
            }

            // Get all campaigns.
            $page = $campaign_service->get($selector);
            if(!empty($page->entries))
            {
                $result['data'] = $page->entries;
                $result['status'] = self::STATUS_SUCCESS;
            }
            else
            {
                $result['reason'] = 'No '.strtolower($status).' campaigns.';
                $result['status'] = self::STATUS_NOT_FOUND;
            }
        }
        catch(Exception $e)
        {
            $result['reason'] = $e->getMessage();
            $result['status'] = self::STATUS_APPLICATION_ERROR;
        }
        return $result;
    }

    /**
     * Method get page rank about url
     * or search url's by keywords
     *
     * @access public
     * @param $url
     * @param $keywords
     * @return array
     */
    public function get_page_rank($url, $keywords)
    {
        try
        {
            $seostats = new SEOstats($url);
            $google = $seostats->Google();
            $data['keywords_result'] = array();
            $data['page'] = array();

            //get url page rank
            if(!empty($url))
            {
                $stat['page_rank'] = $google->getPageRank();
                $stat['page_speed'] = $google->getPagespeedScore();
                $data['page'] = $stat;
            }

            if(!empty($keywords))
            {
                if(!empty($url))
                {
                    $serps = $google->getSerps($keywords, 20, $url);
                }
                else
                {
                    $serps = $google->getSerps($keywords, 20);
                }
                if(!empty($serps))
                {
                    $i = 0;
                    $ranks = array();
                    foreach($serps as $item)
                    {
                        if(!empty($item['url']))
                        {
                            $ranks[$i]['url'] = $item['url'];
                            $ranks[$i]['headline'] = $item['headline'];
                            $ranks[$i]['page_rank'] = $google->getPageRank($item['url']);
//                            $ranks[$i]['page_speed'] = $google->getPagespeedScore($item['url']);
                            $i++;
                        }
                    }
                    $data['keywords_result'] = $ranks;
                }
            }

            $result = array(
                'status' => self::STATUS_SUCCESS,
                'data' => $data
            );
        }
        catch(SEOstatsException $e)
        {
            $result = array(
                'status' => self::STATUS_APPLICATION_ERROR,
                'reason' => $e->getMessage()
            );
        }
        return $result;
    }

    /**
     * Method get billing details about account
     * @TODO white list exception
     * @TODO https://developers.google.com/adwords/api/docs/reference/v201302/BudgetOrderService.NotWhitelistedError
     */
    public function get_billing_info()
    {
        $user = $this->get_adwords_user();
        $budget_order_service = $user->GetService('BudgetOrderService', ADWORDS_VERSION);
        $budget_order_service->getBillingAccounts();
        var_dump($budget_order_service);
    }

    public function get_account_alerts()
    {
        try
        {
            $user = $this->get_adwords_user();

            // Get the service, which loads the required classes.
            $alert_service = $user->GetService('AlertService', ADWORDS_VERSION);

            // Create alert query.
            $alert_query = new AlertQuery();
            $alert_query->clientSpec = 'ALL'; //get alerts for all clients
            $alert_query->filterSpec = 'ALL';
//            $alert_query->clientCustomerIds = 'ALL';
            $alert_query->types = array('ACCOUNT_BUDGET_BURN_RATE','ACCOUNT_BUDGET_ENDING',
                'ACCOUNT_ON_TARGET','CAMPAIGN_ENDED','CAMPAIGN_ENDING',
                'CREDIT_CARD_EXPIRING','DECLINED_PAYMENT',
                'MANAGER_LINK_PENDING','MISSING_BANK_REFERENCE_NUMBER',
                'PAYMENT_NOT_ENTERED','TV_ACCOUNT_BUDGET_ENDING','TV_ACCOUNT_ON_TARGET',
                'TV_ZERO_DAILY_SPENDING_LIMIT','USER_INVITE_ACCEPTED',
                'USER_INVITE_PENDING','ZERO_DAILY_SPENDING_LIMIT');
            $alert_query->severities = array('GREEN', 'YELLOW', 'RED');
            $alert_query->triggerTimeSpec = 'ALL_TIME';

            // Create selector.
            $selector = new AlertSelector();
            $selector->query = $alert_query;
            $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

            $alerts = array();
            $i = 0;
            do
            {
                // Make the get request.
                $page = $alert_service->get($selector);
                // Get page results.
                if(isset($page->entries))
                {
                    foreach($page->entries as $alert)
                    {
                        $alerts[$i]['alert_type'] = $alert->alertType;
                        $alerts[$i]['severity'] = $alert->alertSeverity;
                        $alerts[$i]['customer_id'] = $alert->clientCustomerId;
                        $alerts[$i]['details'] = $alert->details;
                        $i++;
                    }
                }
                // Advance the paging index.
                $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
            }
            while($page->totalNumEntries > $selector->paging->startIndex);
            if(!empty($alerts))
            {
                $result = array(
                    'status' => self::STATUS_SUCCESS,
                    'data' => $alerts
                );
            }
            else
            {
                $result = array(
                    'status' => self::STATUS_NOT_FOUND,
                    'reason' => 'No alerts found'
                );
            }
        }
        catch(Exception $e)
        {
            $result = array(
                'status' => self::STATUS_APPLICATION_ERROR,
                'reason' => $e->getMessage()
            );
        }
        return $result;
    }

    /**
     * Method filter received accounts
     *
     * @access private
     * @param $accounts
     * @param $can_manage_clients
     * @return array
     */
    private function _filter_accounts($accounts, $can_manage_clients)
    {
        $filtered_accounts = array();
        foreach($accounts as $account)
        {
            $matches = TRUE;
            if(!is_null($can_manage_clients))
            {
                $matches = $matches && ($account->canManageClients == $can_manage_clients);
            }
            if($matches){

                $filtered_accounts[] = $account;
            }
        }
        return $filtered_accounts;
    }
}
