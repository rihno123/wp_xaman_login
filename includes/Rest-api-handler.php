<?php

class pluginRestAPI {
    public function __construct() {
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
        global $XamanLoginPlugin;
        $headers = $request->get_headers();
        $action = $headers['action'];
        switch ($action[0]) {
            case 'login':

                try {

                    $XamanLoginPlugin->login();

                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                    error_log($error_message);
                    echo json_encode($error_message);
                }
                break;

            case 'checking_transaction':

                try {
                    $uuid = $headers['uuid'][0];
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
                    $wallet = $headers['wallet'][0];
                    $balances = $XamanLoginPlugin->fetchBalances($wallet);
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
                    $destination = $headers['destination'][0];
                    $ammount = $headers['amount'][0];
                    $XamanLoginPlugin->sendRequest($destination,$ammount);
                
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

new pluginRestAPI();