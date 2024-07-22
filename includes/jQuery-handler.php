<?php

if(!defined('ABSPATH'))
{
    die('Nice try!');
}

function jQuery_Handler()
{
    ?>
    <script>   
       
        var storedBalance = getCookie('xrpBalance');
        var storedWallet = getCookie('walletAddress');
        var storedTokens = getCookie('allTokens');

        jQuery(document).ready(function ($) {
            
            if (storedWallet && storedBalance) {
                $('#xrpBalance').text(storedBalance);
                $('#walletAddress').text(storedWallet);
                var balances = JSON.parse(storedTokens);
                var allTokensHtml = storedTokens;
                addLogoutButton();
                addTransactionButtons();
                receiveButton();
                sendButton();
                $('.balance ~ .tokens').remove();
                reconstructTokens(balances);
                $('.loginbutton').remove();

            }

            var nonce = '<?php echo wp_create_nonce("wp_rest"); ?>'
            
        $('#loginButton').on("click", function (event) {

            event.preventDefault(); 
            var form = {
                "action": "login",
            };
            form = JSON.stringify(form);

            $.ajax({
                method: 'POST',
                url: '<?php echo get_rest_url(null, "xaman/login"); ?>',
                crossDomain: true, 
                headers: {
                    'X-WP-Nonce': nonce, 
                    "accept": "application/json", 
                    "Access-Control-Allow-Origin": "*" 
                },
                data: form,
                dataType: "json"
            }).done(function (res) {
                var popup = window.open(res.redirect_url, 'Popup', 'width=600,height=600');
                if (!popup) {
                    alert('Popup blocked by browser');
                }
                Websocket_handler(res.websocket, res.uuid);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            });
        });
    });
    </script>

<?php
}

function Xaman_handler()
{
    ?>
    <script>   
    function Websocket_handler(websocket, uuid){

    socket = new WebSocket(websocket);
        socket.onmessage = function (event) {

        let data = JSON.parse(event.data);


         if (data.expires_in_seconds < 0) {

                socket.close();

         } else if (data.signed) {

                socket.close();
                Checking_transaction(uuid);
                
         } else if (data.signed != undefined && !data.signed) {

                socket.close();

         } 
         else {
            
                console.log(`[message] Data received from server: ${event.data}`);

            }
        };
    }

    function Checking_transaction(uuid)
    {
        var nonce = '<?php echo wp_create_nonce("wp_rest"); ?>'
        var form = {
                "action": "checking_transaction",
                "uuid": uuid
            };
        form = JSON.stringify(form);
        jQuery.ajax({
                method: 'POST',
                url: '<?php echo get_rest_url(null, "xaman/login"); ?>',
                crossDomain: true, 
                headers: {
                    'X-WP-Nonce': nonce, 
                    "accept": "application/json", 
                    "Access-Control-Allow-Origin": "*" 
                },
                data: form,
                dataType: "json"

            }).done(function (res) { 
                if (res.data.response.account) 
                {
                    updateBalance(res.data.response.account)
                    setCookie('userToken', res.data.application.issued_user_token, 1);
                } 
                else 
                {
                    console.log("No account information received");
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("Checking transaction failed");
            });
    }

    function updateBalance(walletAddress) {
       
        var nonce = '<?php echo wp_create_nonce("wp_rest"); ?>'
        var form = {
                "action": "get_balance",
                "wallet": walletAddress
            };
        form = JSON.stringify(form);
        jQuery.ajax({
            method: 'POST',
            url: '<?php echo get_rest_url(null, "xaman/login"); ?>',
            headers: {
                'X-WP-Nonce': nonce,
                "Content-Type": "application/json",
                "Access-Control-Allow-Origin": "*" 
            },
            data: form,
            success: function(res) {
                if (res) {
                    jQuery('#xrpBalance').text(res.balances["XRP"]);
                    shortenedWallet = res.wallet.substring(0, 3) + '...' + res.wallet.slice(-3);
                    jQuery('#walletAddress').text(shortenedWallet);
                    jQuery('.balance ~ .tokens').remove();
                    
                    var allTokensHtml = '<div class="tokens">';
                    jQuery.each(res.balances, function(tokenName, balance) {
                        if (tokenName !== 'XRP') {
                            if(tokenName.length !== 3){
                                tokenName = hexToString(tokenName); 
                            }
                        allTokensHtml += `
                                    <div class="token">
                                        <h3><i class="fas fa-coins"></i> ${tokenName}</h3>
                                        <p><span>${balance}</span></p>
                                    </div>
                                `;
                        }
                    });
                    addTransactionButtons();
                    jQuery('.buttons').before(allTokensHtml);
                    jQuery('.loginbutton').remove();
                    setCookie('xrpBalance', res.balances["XRP"], 1);
                    setCookie('walletAddress', shortenedWallet, 1);
                    setCookie('allTokens', JSON.stringify(res.balances), 1);
                    receiveButton();
                    sendButton();
                    addLogoutButton();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to fetch XRP balance:', textStatus, errorThrown);
                jQuery('#xrpBalance').text('Failed to load');
                jQuery('#walletAddress').text('Wallet Address: Failed to load');
            }
        });
    }

    function reconstructTokens(balances) {
        var allTokensHtml = '<div class="tokens">';
        jQuery.each(balances, function(tokenName, balance) {
            if (tokenName !== 'XRP') {
                if(tokenName.length !== 3){
                                tokenName = hexToString(tokenName); 
                }
                allTokensHtml += `
                    <div class="token">
                        <h3><i class="fas fa-coins"></i> ${tokenName}</h3>
                        <p><span>${balance}</span></p>
                    </div>
                `;
            }
        });
        allTokensHtml += '</div>';
        jQuery('.buttons').before(allTokensHtml);
    }

    function addLogoutButton()
    {
        var logoutButtonHtml = `
        <div class = "logoutbutton">     
        <button class="button" id="logoutButton">LOGOUT</button>
        </div>
        `;
        jQuery('.amount-container').after(logoutButtonHtml);


        jQuery('#logoutButton').on('click', function() {

            setCookie('xrpBalance', '', -1);
            setCookie('walletAddress', '', -1);
            setCookie('allTokens', '', -1);

            location.reload();
        });
    }

    function addTransactionButtons()
    {
        var buttonsHtml = `
                    <div class="buttons">
                        <button class="button" id="sendButton">SEND</button>
                        <button class="button" id="receiveButton">RECEIVE</button>
                    </div>`;

        jQuery('.amount-container').before(buttonsHtml);
    }

    function sendButton()
    {
        jQuery('#sendButton').on("click", function (event) {
            
            var nonce = '<?php echo wp_create_nonce("wp_rest"); ?>'
            var destination = document.getElementById('destination').value;
            var amount = document.getElementById('amount').value;
            event.preventDefault(); 
            var form = {
                "action": "sendButton",
                "amount": amount,
                "destination": destination,
                "userToken" : getCookie("userToken")
            };
            form = JSON.stringify(form);

            jQuery.ajax({
                method: 'POST',
                url: '<?php echo get_rest_url(null, "xaman/login"); ?>',
                crossDomain: true, 
                headers: {
                    'X-WP-Nonce': nonce, 
                    "accept": "application/json", 
                    "Access-Control-Allow-Origin": "*" 
                },
                data: form,
                dataType: "json"
            }).done(function (res) {
                if(res)
                {
                    var popup = window.open(res.redirect_url, 'Popup', 'width=600,height=600');
                if (!popup) {
                    alert('Popup blocked by browser');
                }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            });
        });
    }

    function receiveButton()
    {
        jQuery('#receiveButton').on('click', function (event) {
            var destination = document.getElementById('destination').value;
            var amount = document.getElementById('amount').value;
            var popup = window.open("https://xumm.app/detect/request:" + destination + "?amount=" + amount, 'Popup', 'width=600,height=600');
            if (!popup) {
                alert('Popup blocked by browser');
            }
        });  
    }

    
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }


    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    function hexToString(hex) {
        if (hex.length % 2 !== 0) {
            console.error('Invalid hexadecimal string');
            return null;
        }
        const charCodes = hex.match(/.{1,2}/g).map(byte => parseInt(byte, 16));
        return String.fromCharCode(...charCodes);
    }

    </script>
<?php
}
add_action('wp_footer', 'Xaman_handler');
add_action('wp_footer', 'jQuery_Handler');