<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counter App</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            padding: 50px 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }

        .counter-display {
            background: #f9f9f9;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
        }

        .counter-label {
            color: #999;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        #value {
            font-size: 3.5rem;
            font-weight: 700;
            color: #2c3e50;
            display: block;
        }

        .button-group {
            display: flex;
            gap: 12px;
        }

        button {
            flex: 1;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #add {
            background: #3498db;
            color: white;
        }

        #add:hover {
            background: #2980b9;
        }

        #add:active {
            transform: scale(0.98);
        }

        #reset {
            background: #ecf0f1;
            color: #555;
        }

        #reset:hover {
            background: #d5dbdb;
        }

        #reset:active {
            transform: scale(0.98);
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 30px;
            }

            h1 {
                font-size: 1.5rem;
            }

            #value {
                font-size: 2.8rem;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Compteur</h1>
        
        <div class="counter-display">
            <div class="counter-label">Total</div>
            <span id="value">{{ $value }}</span>
        </div>

        <div class="button-group">
            <button id="add">+1</button>
            <button id="reset">Réinitialiser</button>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            // Load initial value
            loadCounter();

            // Increment button
            $('#add').click(function(){
                $.ajax({
                    url: '/api/counter/increment',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ amount: 1 }),
                    dataType: 'json',
                    success: function(data){
                        $('#value').text(data.value);
                    },
                    error: function(xhr){
                        alert('Error: ' + xhr.responseJSON?.error || 'Unknown error');
                    }
                });
            });

            // Reset button
            $('#reset').click(function(){
                if(confirm('Réinitialiser le compteur ?')) {
                    $.ajax({
                        url: '/api/counter/reset',
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(data){
                            $('#value').text(data.value);
                        },
                        error: function(xhr){
                            alert('Error: ' + xhr.responseJSON?.error || 'Unknown error');
                        }
                    });
                }
            });

            // Load current counter value
            function loadCounter(){
                $.get('/api/counter/count', function(data){
                    $('#value').text(data.value);
                });
            }
        });
    </script>
</body>
</html>
