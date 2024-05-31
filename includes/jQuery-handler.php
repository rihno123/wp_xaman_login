<?php

if(!defined('ABSPATH'))
{
    die('Nice try!');
}

function jQuery_Handler()
{
    ?>
    <script>   
        
    jQuery(document).ready(function ($) {
        var nonce = '<?php echo wp_create_nonce("wp_rest"); ?>'

        document.getElementById('receiveButton').addEventListener('click', () => {
            var destination = document.getElementById('destination').value;
            var amount = document.getElementById('amount').value;
            var popup = window.open("https://xumm.app/detect/request:" + destination + "?amount=" + amount, 'Popup', 'width=600,height=600');
                if (!popup) {
                    alert('Popup blocked by browser');
                }
            
            });

        jQuery('#loginButton').on("click", function (event) {

            event.preventDefault(); 
            var form = {
                "action": "login",
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
                var popup = window.open(res.redirect_url, 'Popup', 'width=600,height=600');
                if (!popup) {
                    alert('Popup blocked by browser');
                }
                Websocket_handler(res.websocket, res.uuid);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            });
        });

        jQuery('#sendButton').on("click", function (event) {
            
            var destination = document.getElementById('destination').value;
            var amount = document.getElementById('amount').value;
            event.preventDefault(); 
            var form = {
                "action": "sendButton",
                "amount": amount,
                "destination": destination
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
                    $('#xrpBalance').text(res.balances["XRP"]);
                    shortenedWallet = res.wallet.substring(0, 3) + '...' + res.wallet.slice(-3);
                    $('#walletAddress').text(shortenedWallet);
                    $('.balance ~ .token').remove();
                    
                    var allTokensHtml = '';
                    $.each(res.balances, function(tokenName, balance) {
                        if (tokenName !== 'XRP') {
                            allTokensHtml += `
                                <div class="token">
                                    <h3><i class="fas fa-coins"></i> ${tokenName}</h3>
                                    <p>Balance: <span>${balance}</span></p>
                                </div>
                            `;
                        }
                    });

                    
                    $('.buttons').before(allTokensHtml);
                    $('.loginbutton').remove();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to fetch XRP balance:', textStatus, errorThrown);
                $('#xrpBalance').text('Failed to load');
                $('#walletAddress').text('Wallet Address: Failed to load');
            }
        });
    }


    </script>
<?php
}
add_action('wp_footer', 'Xaman_handler');
add_action('wp_footer', 'jQuery_Handler');