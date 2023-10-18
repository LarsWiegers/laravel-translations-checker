<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <header>
        {{__('messages.header')}}
    </header>
    <main>
        <h1>{{__('messages.welcome')}}</h1>
        <h2>{{__('messages.about')}}</h2>
        <h3>{{__('messages.list')}}</h3>
        <h4>{{__('messages.contact')}}</h4>
        <p>
            {{__('messages.welcome.text')}}
        </p>
    </main>
    <footer>
        {{__('messages.footer')}}
    </footer>
</body>
</html>
