<?php
/**
 * Plugin Name: Xaman login
 * Plugin URI:  
 * Description: Provides Xaman wallet login functionality, send/receive functions and list of all tokens owned.
 * Version:     1.0
 * Author:      Lein
 */
require_once __DIR__ . '/vendor/autoload.php';
use Xrpl\XummSdkPhp\Payload\Payload;
use Xrpl\XummSdkPhp\XummSdk;

if(!defined('ABSPATH'))
{
    die('Nice try!');
}

if (!function_exists('add_action')) {
    echo 'This is a plugin for WordPress and cannot be called directly.';
    exit;
}
const MAINNET_URL=  'https://xrplcluster.com/';
const TESTNET_URL = 'https://testnet.xrpl-labs.com/';

class XamanLoginPlugin {

    private $xummSdk;

    public function __construct() {
        $this->initHooks();
        $this->xummSdk = new XummSdk(get_option('XUMM_KEY'), get_option('XUMM_SECRET'));
        error_log("uspio");
    }

    private function initHooks() {
       add_action('wp_enqueue_scripts', array($this, 'enqueueCustomScripts'));
        require_once plugin_dir_path(__FILE__) . 'includes/jQuery-handler.php';
        require_once plugin_dir_path(__FILE__) . 'includes/Settings.php';
        require_once plugin_dir_path(__FILE__) . 'includes/Tokens-list-menu.php';
        require_once plugin_dir_path(__FILE__) . 'includes/Rest-api-handler.php';
    }

    public function enqueueCustomScripts() {
        wp_enqueue_script('jquery');
    }

    public function login() {
        $payload = new Payload([
            "TransactionType" => "SignIn",
            "CustomField" => "Optional custom data or instructions if needed"
        ]);
        
        try {
            $response = $this->xummSdk->createPayload($payload);
            if (isset($response->uuid)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'websocket' => $response->refs->websocketStatus,
                    'redirect_url' => $response->next->always,
                    'uuid' => $response->uuid
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create payload']);
            }
        } catch (Exception $e) {
            error_log('Failed to create sign-in payload: ' . $e->getMessage());
        }
    }

    public function sendRequest($destination, $amount) {

        $payload = new Payload( [
            'TransactionType' => 'Payment',
            'Destination' => $destination,
            'Amount' => strval($amount * 1000000)
        ]);
    
        try {
            $response = $this->xummSdk->createPayload($payload);
    
            if (isset($response->uuid)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'redirect_url' => $response->next->always,
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create payload']);
                return null;
            }
        } catch (Exception $e) {
            error_log('Failed to create sign-in payload: ' . $e->getMessage());
            return null;
        }
    }

    public function fetchBalances($walletAddress) {
        $result = $this->fetch_data("account_info", $walletAddress);
        $balances = [];

        if (isset($result['result']['account_data']['Balance'])) {
            $balances['XRP'] = $result['result']['account_data']['Balance'] / 1000000;
        }

        $result = $this->fetch_data("account_lines", $walletAddress);
        if (isset($result['result']['lines'])) {
            foreach ($result['result']['lines'] as $line) {
                $currency = $line['currency']; 
                $balance = $line['balance'];  
                $balances[$currency] = $balance;
            }
        }

        if (empty($balances)) {
            error_log('Failed to fetch account info or no balances available: ' . json_encode($result));
            return null;
        }

        return $balances;
    }

    private function fetch_data($method, $walletAddress) {
        $url = TESTNET_URL;
        $data = [
            "method" => $method,
            "params" => [
                [
                    "account" => $walletAddress,
                    "strict" => true,
                    "ledger_index" => "current",
                    "queue" => true
                ]
            ]
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('Curl error: ' . curl_error($ch));
            return null;
        }
        curl_close($ch);
    
        $result = json_decode($result, true);
        return $result;
    }
}

global $XamanLoginPlugin;
$XamanLoginPlugin = new XamanLoginPlugin();
