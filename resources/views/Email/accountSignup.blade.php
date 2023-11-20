<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

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
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            
            <div class="content">

                <div class="jumbotron jumbotron-fluid">
                    @isset($token)
                        <div class="container">
                            <h2>Welcome to {{ config('app.name') }}.</h2>
                            <p>It is good to have you here</p>
                            <p>In order to verify your email, click on the link below or copy and paste it into your browser</p>
                            
                            <p>
                                <a href="{{ config('app.url') }}{{ $token }}">
                                {{ config('app.url') }}{{ $token }}
                                </a>
                            </p>                      
                        </div>
                        <br>
                        <br>
                    @endisset

                     @isset($url)
                        <div class="container">
                            <h2>Welcome to {{ config('app.name') }} Password reset.</h2>
                            
                            <p>In order to reset your password, click on the link below or copy and paste it into your browser</p>
                            
                            <p>
                                <a href="{{ $url }}">
                                {{ $url }}
                                </a>
                            </p>                      
                        </div>
                        <br>
                        <br>
                    @endisset

                    @isset($verified)
                        <div class="container">
                            <h2>Welcome to {{ config('app.name') }}.</h2>
                            <p>Your email {{ $email }} has been successfully verified</p>
                                                        
                            
                        </div>
                        <br>
                        <br>
                    @endisset

                    @isset($searchId)
                        <div class="container">
                            <h2>Hi {{ $email }}, You have just created an item to be paid for on {{ config('app.name') }}.</h2>
                            
                            <p>Below is the Search ID you need to send to your customer. They need to put in this ID into the search bar to find the Item profile you've created, to pay for it</p>
                            
                            <p class="title-search">
                                
                                Search ID:<div class="search-class">{{ $searchId }}</div>
                                
                            </p>                      
                        </div>
                        <br>
                        <br>
                    @endisset
                    
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

