<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin Powered by AdLara Library</title>
        <link href="https://fonts.googleapis.com/css?family=Muli:300,400,600,700,800,900&display=swap" rel="stylesheet">
        <link rel="icon" href="https://jigeshraval.com/favicon.ico">
        <script>
            const ADMIN_ROUTE = "{{ config('adlara.admin_route') }}";
            const ADMIN_APP_ROUTE = "{{ config('adlara.admin_route') . '/app' }}";
            const ADMIN_API_ROUTE = "{{ config('adlara.admin_route') . '/api' }}";
        </script>
		<script src="//code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    </head>
    <body>
        <div id="dApp"></div>
        <script src="{{ url('js/vApp.js') }}"></script>
    </body>
</html>
