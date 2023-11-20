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

            .button {
                background-color: #555555;
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
            }            
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            
            <div class="content">

                <div class="jumbotron jumbotron-fluid">   
                    @isset($seller)     
                        <h4>Your payment from {{ $buyerEmail }} has been received. Please log on to our platform to confirm with the Payment ID before you ship your goods or release your services</h4>  
                        
                        <table class="table table-bordered">
                        
                            <tbody>  
                                <tr>
                                    <td><b>Payment Id</b></td>                            
                                    <td>{{ $paymentId }}</td>                            
                                </tr>                  
                                <tr>
                                    <td><b>Item Name</b></td>                            
                                    <td>{{ $itemname }}</td>                            
                                </tr> 
                                <tr>
                                    <td><b>Item Price</b></td>                            
                                    <td>{{ strtoupper($currency) }}{{' '}}{{ $itemPrice }}</td>                            
                                </tr> 
                                <tr>
                                    <td><b>Total amount paid<br> including commission & processing fees</b></td>                            
                                    <td>{{ strtoupper($currency) }}{{' '}}{{ $amountReceived }}</td>                            
                                </tr> 
                                <tr>
                                    <td><b>Item description</b></td>                            
                                    <td>{{ $itemDescription }}</td>                            
                                </tr> 
                                <tr>
                                    <td><b>Payment Method</b></td>                            
                                    <td>{{ $paymentOption }}</td>                            
                                </tr>
                                <tr>
                                    <td><b>Buyer Email</b></td>                            
                                    <td>{{ $buyerEmail }}</td>                            
                                </tr> 
                                <tr>
                                    <td><b>Payment Date</b></td>                            
                                    <td>{{ $paymentDate }}</td>                            
                                </tr>              
                            </tbody>
                        </table>
                    @endisset

                    @empty($seller)
            
                    <h3>Your Invoice</h3>  
                       <a href="{{ url('pdf?paymentId='.$paymentId) }}"><button class="button">Export to PDF</button></a>

                    <table class="table table-bordered">
                    
                        <tbody>  
                            <tr>
                                <td><b>Payment Id</b></td>                            
                                <td>{{ $paymentId }}</td>                            
                            </tr>                  
                            <tr>
                                <td><b>Item Name</b></td>                            
                                <td>{{ $itemname }}</td>                            
                            </tr> 
                            <tr>
                                <td><b>Item Price</b></td>                            
                                <td>{{ strtoupper($currency) }}{{' '}}{{ $itemPrice }}</td>                            
                            </tr> 
                            <tr>
                                <td><b>Total amount paid<br> including commission & processing fees</b></td>                            
                                <td>{{ strtoupper($currency) }}{{' '}}{{ $amountReceived }}</td>                            
                            </tr> 
                            <tr>
                                <td><b>Item description</b></td>                            
                                <td>{{ $itemDescription }}</td>                            
                            </tr> 
                            <tr>
                                <td><b>Payment Method</b></td>                            
                                <td>{{ $paymentOption }}</td>                            
                            </tr>
                            <tr>
                                <td><b>Seller</b></td>                            
                                <td>{{ $sellerEmail }}</td>                            
                            </tr> 
                            <tr>
                                <td><b>Payment Date</b></td>                            
                                <td>{{ $paymentDate }}</td>                            
                            </tr>              
                        </tbody>
                    </table>

                    @endempty
                </div>                
                
                <br>
                <br>
                <div>
                    Thanks for your patronage<br>
                
                </div>                
            </div>
        </div>
    </body>
</html>

