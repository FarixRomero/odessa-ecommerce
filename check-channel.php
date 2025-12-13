#!/usr/bin/env php
<?php

/*
 * Script para verificar configuración del canal en producción
 * Uso: php check-channel.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Obtener el canal actual
    $channel = core()->getCurrentChannel();

    echo "=== DIAGNÓSTICO DEL CANAL ===\n";
    echo "Canal ID: " . $channel->id . "\n";
    echo "Canal Nombre: " . $channel->name . "\n";
    echo "Canal Hostname: " . $channel->hostname . "\n";
    echo "Root Category ID: " . ($channel->root_category_id ?? 'NULL - ¡ESTE ES EL PROBLEMA!') . "\n";
    echo "Theme: " . $channel->theme . "\n";
    echo "\n";

    if (empty($channel->root_category_id)) {
        echo "❌ ERROR: El canal NO tiene root_category_id configurado\n";
        echo "📝 SOLUCIÓN: Configura una categoría raíz en el admin panel\n";
        echo "   Admin → Settings → Channels → Default → Root Category\n";
    } else {
        // Verificar si existen categorías
        $categoryRepo = app('Webkul\Category\Repositories\CategoryRepository');
        $categories = $categoryRepo->getVisibleCategoryTree($channel->root_category_id);

        echo "Categorías encontradas: " . count($categories) . "\n";

        if (count($categories) === 0) {
            echo "❌ ERROR: No hay categorías visibles\n";
            echo "📝 SOLUCIÓN: Crea categorías o verifica que estén habilitadas (status=1)\n";
        } else {
            echo "✅ Categorías OK\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
