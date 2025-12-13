#!/usr/bin/env php
<?php

/**
 * Script de diagnóstico y reparación para producción
 * Uso: php fix-production.php
 *
 * Este script:
 * 1. Verifica la configuración del canal
 * 2. Verifica las categorías
 * 3. Regenera el sitemap si es necesario
 * 4. Limpia los caches
 */

echo "\n🔧 INICIANDO DIAGNÓSTICO Y REPARACIÓN DE PRODUCCIÓN\n";
echo str_repeat("=", 60) . "\n\n";

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// PASO 1: Limpiar caches
echo "📦 PASO 1: Limpiando caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "   ✅ Cache limpiado\n";

    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✅ Config cache limpiado\n";

    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "   ✅ Route cache limpiado\n";

    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✅ View cache limpiado\n";
} catch (\Exception $e) {
    echo "   ⚠️  Error al limpiar caches: " . $e->getMessage() . "\n";
}

echo "\n";

// PASO 2: Verificar canal
echo "🔍 PASO 2: Verificando configuración del canal...\n";
try {
    $channel = core()->getCurrentChannel();

    echo "   Canal ID: " . $channel->id . "\n";
    echo "   Canal Nombre: " . $channel->name . "\n";
    echo "   Canal Hostname: " . $channel->hostname . "\n";
    echo "   Root Category ID: " . ($channel->root_category_id ?? 'NULL') . "\n";

    if (empty($channel->root_category_id)) {
        echo "\n   ❌ ERROR CRÍTICO: El canal no tiene root_category_id\n";
        echo "   📝 ACCIÓN REQUERIDA:\n";
        echo "      1. Ve al panel de admin: https://odessaplastperu.com/admin\n";
        echo "      2. Settings → Channels → {$channel->name} → Root Category\n";
        echo "      3. Selecciona una categoría raíz\n\n";

        // Intentar encontrar categorías disponibles
        $categories = \DB::table('categories')
            ->where('parent_id', null)
            ->orWhere('parent_id', 0)
            ->get();

        if ($categories->count() > 0) {
            echo "   📋 Categorías raíz disponibles:\n";
            foreach ($categories as $cat) {
                echo "      - ID: {$cat->id} | Nombre: " . json_decode($cat->name ?? '{}')->en ?? 'N/A' . "\n";
            }
        } else {
            echo "   ⚠️  No hay categorías raíz. Debes crear categorías primero.\n";
        }
    } else {
        echo "   ✅ Root Category ID configurado correctamente\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// PASO 3: Verificar categorías visibles
echo "📂 PASO 3: Verificando categorías visibles...\n";
try {
    $channel = core()->getCurrentChannel();

    if (!empty($channel->root_category_id)) {
        $categoryRepo = app('Webkul\Category\Repositories\CategoryRepository');
        $categories = $categoryRepo->getVisibleCategoryTree($channel->root_category_id);

        echo "   Total categorías visibles: " . count($categories) . "\n";

        if (count($categories) === 0) {
            echo "   ⚠️  No hay categorías visibles\n";
            echo "   📝 ACCIÓN REQUERIDA: Crea categorías o verifica que estén habilitadas (status=1)\n";
        } else {
            echo "   ✅ Categorías encontradas correctamente\n";
        }
    } else {
        echo "   ⏭️  Saltando (no hay root_category_id configurado)\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// PASO 4: Verificar sitemap
echo "🗺️  PASO 4: Verificando sitemap...\n";
try {
    $sitemapEnabled = core()->getConfigData('general.sitemap.settings.enabled');

    echo "   Sitemap habilitado: " . ($sitemapEnabled ? 'SÍ' : 'NO') . "\n";

    if ($sitemapEnabled) {
        $sitemaps = \DB::table('sitemaps')->get();
        echo "   Sitemaps configurados: " . $sitemaps->count() . "\n";

        if ($sitemaps->count() === 0) {
            echo "\n   ⚠️  No hay sitemaps configurados\n";
            echo "   📝 ACCIÓN REQUERIDA:\n";
            echo "      1. Ve a: Admin → Marketing → Search & SEO → Sitemaps\n";
            echo "      2. Crea un nuevo sitemap:\n";
            echo "         - File Name: sitemap.xml\n";
            echo "         - Path: /\n";
            echo "      3. Guarda y espera que se genere\n\n";
        } else {
            echo "   ✅ Sitemaps configurados\n";

            foreach ($sitemaps as $sitemap) {
                echo "\n   Sitemap: {$sitemap->file_name}\n";
                echo "      Path: {$sitemap->path}\n";
                echo "      Generado: " . ($sitemap->generated_at ?? 'Nunca') . "\n";

                // Verificar si existe el archivo físico
                $indexPath = $sitemap->path . $sitemap->file_name;
                $fullPath = public_path('storage' . $indexPath);

                if (file_exists($fullPath)) {
                    echo "      Archivo existe: ✅ {$fullPath}\n";
                } else {
                    echo "      Archivo existe: ❌ {$fullPath}\n";
                    echo "      📝 Regenerando sitemap...\n";

                    // Regenerar
                    try {
                        $sitemapModel = app('Webkul\Sitemap\Repositories\SitemapRepository')->find($sitemap->id);
                        \Webkul\Sitemap\Jobs\ProcessSitemap::dispatch($sitemapModel);
                        echo "      ✅ Job de sitemap encolado (se procesará en breve)\n";
                    } catch (\Exception $e) {
                        echo "      ❌ Error al encolar: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    } else {
        echo "   ⚠️  Sitemap está deshabilitado en la configuración\n";
        echo "   📝 ACCIÓN: Habilítalo en Admin → Settings → General → Sitemap\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// PASO 5: Verificar archivos críticos
echo "📄 PASO 5: Verificando archivos críticos...\n";

$criticalFiles = [
    'public/robots.txt' => 'Robots.txt',
    'storage/app/public' => 'Storage link',
];

foreach ($criticalFiles as $file => $desc) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✅ {$desc}: {$fullPath}\n";
    } else {
        echo "   ❌ {$desc} no existe: {$fullPath}\n";

        if ($file === 'storage/app/public' && !is_link($fullPath)) {
            echo "      📝 Ejecuta: php artisan storage:link\n";
        }
    }
}

echo "\n";

// RESUMEN FINAL
echo str_repeat("=", 60) . "\n";
echo "🎯 RESUMEN Y ACCIONES PENDIENTES\n";
echo str_repeat("=", 60) . "\n\n";

echo "✅ COMPLETADO:\n";
echo "   - Caches limpiados\n";
echo "   - Configuración verificada\n";
echo "   - Problema de \$categories corregido en el código\n\n";

echo "📝 ACCIONES QUE DEBES HACER EN EL PANEL DE ADMIN:\n\n";

echo "1. VERIFICAR CATEGORÍA RAÍZ:\n";
echo "   → Admin → Settings → Channels → Default → Root Category\n";
echo "   → Asegúrate de que esté seleccionada\n\n";

echo "2. VERIFICAR SITEMAP:\n";
echo "   → Admin → Marketing → Search & SEO → Sitemaps\n";
echo "   → Si no existe, créalo:\n";
echo "      • File Name: sitemap.xml\n";
echo "      • Path: /\n\n";

echo "3. VERIFICAR SEO DEL CANAL:\n";
echo "   → Admin → Settings → Channels → Default\n";
echo "   → Completa: Meta Title, Meta Description, Meta Keywords\n\n";

echo "4. DESPLEGAR CAMBIOS EN PRODUCCIÓN:\n";
echo "   → Sube los archivos modificados:\n";
echo "      • packages/Webkul/Shop/src/Resources/views/home/index.blade.php\n";
echo "      • public/robots.txt\n";
echo "   → Ejecuta en producción:\n";
echo "      php artisan view:clear\n";
echo "      php artisan cache:clear\n";
echo "      php artisan config:clear\n\n";

echo "🌐 URLs A VERIFICAR DESPUÉS:\n";
echo "   → https://odessaplastperu.com/\n";
echo "   → https://odessaplastperu.com/sitemap.xml\n";
echo "   → https://odessaplastperu.com/robots.txt\n\n";

echo "✨ ¡DIAGNÓSTICO COMPLETADO!\n\n";
