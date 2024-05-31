<?php

if(!defined('ABSPATH'))
{
    die('Nice try!');
}

class Rest_api {

    private $loginHandler;
    public function __construct() {
        $this->loginHandler = new login_Handler();
        add_action('rest_api_init', array($this, 'register_rest_api'));
    }
    
    public function register_rest_api()
    {
        register_rest_route(
            'xaman',
            'login',
            array(
                'methods' => 'GET, POST',
                'callback' => array($this,'handle_rest_api_reqs'),
                'permission_callback' => '__return_true'
            )
        );

    }

    public function handle_rest_api_reqs($request)
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);
        $action = $data['action'] ?? 'Not provided';

        switch ($action) {
            case 'login':

                try {

                    $this->loginHandler->login();

                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                    error_log($error_message);
                    echo json_encode($error_message);
                }
                break;

            case 'checking_transaction':

                try {
                    $uuid = $data['uuid'] ?? 'Not provided';
                    $client = new \GuzzleHttp\Client();
                    $url = "https://xumm.app/api/v1/platform/payload/" . $uuid;
                    $response = $client->request('GET', $url, [
                        'headers' => [
                            'X-API-Key' => get_option("XUMM_KEY"),
                            'X-API-Secret' => get_option("XUMM_SECRET"),
                            'accept' => 'application/json',
                        ],
                    ]);
                $body = json_decode($response->getBody(), true);
                return wp_send_json_success($body);
        
        
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                    error_log($error_message);
                    echo json_encode($error_message);
                }
                break;

            case 'get_balance':

                try {
                    $wallet = $data['wallet'] ?? 'Not provided';
                    $balances = $this->loginHandler->fetchBalances($wallet);
                    $info = json_encode([
                        'balances' => $balances,
                        'wallet' => $wallet
                    ]);
                    echo($info);
            
            
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                    error_log($error_message);
                    echo json_encode($error_message);
                }
                break;
            case 'sendButton':

                try {
                    $destination = $data['destination'] ?? 'Not provided';
                    $amount = (int)$data['amount'] ?? 'Not provided';
                    $this->loginHandler->sendRequest($destination,$amount);
                
                    } catch (Exception $e) {
                        $error_message = $e->getMessage();
                        error_log($error_message);
                        echo json_encode($error_message);
                    }
                    break;    
            default:
            error_log("Not valid requst!");
        }
    }
}

new Rest_api();