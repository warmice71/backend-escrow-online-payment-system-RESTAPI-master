<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bridgepay Systems</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 3rem;
            }

            .search-class {
                background-color: #D3D3D3;
            }

            .title-search {
                text-align: center;
                font-size: 2rem;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            table {
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid black;
                text-align: left;
            }

            td {
               height: 1.2rem;
               vertical-align: bottom;
            }
            th, td {
                padding: 15px;
                text-align: left;
            }

            
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            
            <div class="content">

                <div class="jumbotron jumbotron-fluid">                    
                    <h3>Your Invoice</h3>
                    
                    <table class="table table-bordered">
                    
                    <tbody>    
                        <tr>
                            <td><b>Transaction ID</b></td>                            
                            <td>{{ $params['paymentId'] }}</td>                            
                        </tr>                   
                        <tr>
                            <td><b>Item Name</b></td>                            
                            <td>{{ $params['itemname'] }}</td>                            
                        </tr> 
                        <tr>
                            <td><b>Item Price</b></td>                            
                            <td>{{ strtoupper($params['currency']) }}{{' '}}{{ $params['itemPrice'] }}</td>                            
                        </tr> 
                        <tr>
                            <td><b>Total amount paid <br>including commission & processing fees</b></td>                            
                            <td>{{ strtoupper($params['currency']) }}{{' '}}{{ $params['amountReceived'] }}</td>                            
                        </tr> 
                        <tr>
                            <td><b>Item description</b></td>                            
                            <td>{{ $params['itemDescription'] }}</td>                            
                        </tr> 
                        <tr>
                            <td><b>Payment Method</b></td>                            
                            <td>{{ $params['paymentOption'] }}</td>                            
                        </tr>
                        <tr>
                            <td><b>Seller</b></td>                            
                            <td>{{ $params['sellerEmail'] }}</td>                            
                        </tr> 
                        <tr>
                            <td><b>Payment date</b></td>                            
                            <td>{{ $params['paymentDate'] }}</td>                            
                        </tr>                    
                    </tbody>
                    </table>
                </div>                
                
                <br>
                <br>
                <div>
                    Thanks,<br>
                {{ config('app.name') }}
                </div>                
            </div>
        </div>
    </body>
</html>

