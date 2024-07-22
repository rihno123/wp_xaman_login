<?php

if(!defined('ABSPATH'))
{
    die('Nice try!');
}

function Form() {
    ob_start();
    ?>
   <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALLET INFO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Global styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #fff;
            line-height: 1.6;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: #1f1f1f;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ee7600;
        }
        p {
            margin-bottom: 10px;
        }
        .balance {
            text-align: center;
            margin-bottom: 30px;
        }
        .balance p {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .button {
            display: block;
            width: 100%;
            padding: 12px 0;
            margin-bottom: 10px;
            background-color: #ee7600;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #ff9642;
        }
        .button {
            margin-top: 10px;
        }
        .tokens {
            max-height: 400px;
            overflow-y: auto;
        }
        .tokens::-webkit-scrollbar {
            width: 10px; 
        }
        .tokens::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .tokens::-webkit-scrollbar-thumb {
            background: #888; 
        }
        .tokens::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }
        .token {
            padding: 15px;
            margin-bottom: 20px;
            background-color: #333;
            border-radius: 8px;
            display: flex; 
            justify-content: space-between;
        }
        .token h3 {
            margin-bottom: 10px;
            color: #ee7600;
            font-size: 25px;
        }
        .token p {
            font-size: 20px;
            align-self: center;
            font-weight: bold;
        }
        .fa-coins {
            margin-right: 5px;
        }
        .amount-container {
            background-color: #333;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .amount-container label {
            display: block;
            margin-bottom: 10px;
            color: #ee7600;
        }
        .amount-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #666;
            border-radius: 4px;
            background-color: #1f1f1f;
            color: #fff;
            text-align: left;
        }
        .amount-container input[type=number]::-webkit-inner-spin-button,
        .amount-container input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .amount-container input[type=number] {
            -moz-appearance: textfield; 
        }
        .loginbutton button {
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
        .logoutbutton button {
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>WALLET</h1>
        <div class="balance">
            <p>XRP Balance: <span id="xrpBalance">XXX XRP</span></p>
            <p>Wallet Address: <span id="walletAddress">rXXXXXXXXXXXXXX</span></p>
        </div>
        <h2>Your Tokens</h2>
        <div class = "tokens">
            <div class="token">
                <h3><i class="fas fa-coins"></i>Token1</h3>
                <p>Balance: <span>XXX</span></p>
            </div>
            <div class="token">
                <h3><i class="fas fa-coins"></i>Token2</h3>
                <p>Balance: <span>XXX</span></p>
            </div>
        </div>
        
        <!-- Amount container -->
        <div class="amount-container">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" placeholder="Enter amount">
            <label for="destination">Destination:</label>
            <input type="text" id="destination" placeholder="Enter destination address">
        </div>
        <div class = "loginbutton">     
            <button class="button" id="loginButton">LOGIN</button>
        </div>
    </div>
</body>
</html>
    <?php
    return ob_get_clean();
}
add_shortcode('login_form', 'Form');