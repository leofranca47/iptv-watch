<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IPTV Watch</title>
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        <?php echo app('Illuminate\Foundation\Vite')(["resources/css/app.css", "resources/js/app.js"]); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body>
        <?php echo e($slot); ?>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH /var/www/html/resources/views/layouts/app.blade.php ENDPATH**/ ?>