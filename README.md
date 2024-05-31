# XAMAN LOGIN

XAMAN Login allows you to use XAMAN signing requests to load your tokens and balances on a webpage, with additional functionality for sending and receiving transactions. To get started, you'll need a XAMAN API account for your API Key and API Secret.

## Usage

1. **Configure Settings**  
   First, you need to configure the settings.  
   ![Settings Configuration](https://github.com/rihno123/wp_xaman_login/assets/122835110/bc464d2d-c840-4ce6-89a9-c8baa3be6196)

2. **Add Shortcode**  
   Use the shortcode `[login_form]` to display the login form on your webpage.

That's it! Enjoy using the XAMAN Login.

## Custom Form Configuration

If you prefer to use your own form, make sure to use the following IDs:

- **Login Button**: `loginButton`
- **Send Button**: `sendButton`
- **Receive Button**: `receiveButton`
- **Amount Input**: `amount`
- **Destination Input**: `destination`
- **XRP Balance**: `xrpBalance`
- **Wallet Address**: `walletAddress`

Alternatively, you can customize the form by editing the `Tokens-list-menu.php` file.

## Note

The plugin is configured to operate on the Testnet.
