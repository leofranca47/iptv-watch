<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IPTV Watch</title>
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        @vite(["resources/css/app.css", "resources/js/app.js"])
        @livewireStyles
    </head>
    <body>
        {{ $slot }}
        @livewireScripts
    </body>
</html>
