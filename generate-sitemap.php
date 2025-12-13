#!/usr/bin/env php
<?php

/**
 * Script para generar sitemap manualmente
 * Uso: php generate-sitemap.php
 */

echo "\n🗺️  GENERADOR DE SITEMAP PARA ODESSA ECOMMERCE\n";
echo str_repeat("=", 60) . "\n\n";

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

try {
    // Verificar si el sitemap está habilitado
    $enabled = core()->getConfigData('general.sitemap.settings.enabled');

    if (!$enabled) {
        echo "❌ ERROR: El sitemap está DESHABILITADO en la configuración\n";
        echo "📝 SOLUCIÓN:\n";
        echo "   1. Ve a: Admin → Settings → General → Sitemap\n";
        echo "   2. Habilita 'Enabled' = Yes\n";
        echo "   3. Guarda y vuelve a ejecutar este script\n\n";
        exit(1);
    }

    echo "✅ Sitemap está habilitado\n\n";

    // Buscar o crear el sitemap
    $sitemapRepo = app('Webkul\Sitemap\Repositories\SitemapRepository');
    $sitemap = $sitemapRepo->first();

    if (!$sitemap) {
        echo "⚠️  No existe ningún sitemap configurado. Creando uno...\n";

        $sitemap = $sitemapRepo->create([
            'file_name' => 'sitemap.xml',
            'path' => '/',
        ]);

        echo "✅ Sitemap creado: {$sitemap->file_name}\n";
    } else {
        echo "📄 Sitemap encontrado: {$sitemap->file_name}\n";
        echo "   Path: {$sitemap->path}\n";
        echo "   ID: {$sitemap->id}\n";
    }

    echo "\n🔄 Generando sitemap...\n";

    // Despachar el job de forma sincrónica
    \Webkul\Sitemap\Jobs\ProcessSitemap::dispatchSync($sitemap);

    echo "✅ Sitemap generado exitosamente!\n\n";

    // Mostrar información del sitemap generado
    $sitemap->refresh();

    if ($sitemap->additional && isset($sitemap->additional['sitemaps'])) {
        echo "📋 Archivos generados:\n";

        foreach ($sitemap->additional['sitemaps'] as $sitemapFile) {
            $fullPath = storage_path('app/public/' . $sitemapFile);
            $exists = file_exists($fullPath) ? '✅' : '❌';
            $size = file_exists($fullPath) ? number_format(filesize($fullPath)) . ' bytes' : 'N/A';

            echo "   {$exists} {$sitemapFile} ({$size})\n";
        }

        echo "\n📄 Archivo índice:\n";
        $indexPath = storage_path('app/public/' . $sitemap->additional['index']);
        $exists = file_exists($indexPath) ? '✅' : '❌';
        $size = file_exists($indexPath) ? number_format(filesize($indexPath)) . ' bytes' : 'N/A';

        echo "   {$exists} {$sitemap->additional['index']} ({$size})\n";
    }

    echo "\n🌐 URLs para verificar:\n";
    echo "   → " . url('/sitemap.xml') . "\n";
    echo "   → " . url('/storage' . $sitemap->index_file_name) . "\n";

    echo "\n📝 Para robots.txt, asegúrate de tener:\n";
    echo "   Sitemap: " . url('/sitemap.xml') . "\n";

    echo "\n✨ ¡COMPLETADO!\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}
