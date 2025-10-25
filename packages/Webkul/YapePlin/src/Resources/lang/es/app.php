<?php

return [
    'admin' => [
        'system' => [
            'title'          => 'Título',
            'description'    => 'Descripción',
            'instructions'   => 'Instrucciones de Pago',
            'account_number' => 'Número de Cuenta',
            'account_holder' => 'Titular de la Cuenta',
            'status'         => 'Estado',
            'sort_order'     => 'Orden de Clasificación',
        ],

        'receipts' => [
            'index' => [
                'title'         => 'Comprobantes Yape / Plin',
                'no_receipts'   => 'No hay comprobantes para revisar',
                'order_id'      => 'ID Pedido',
                'customer'      => 'Cliente',
                'uploaded_at'   => 'Fecha de Subida',
                'status'        => 'Estado',
                'receipt'       => 'Comprobante',
                'actions'       => 'Acciones',
                'view_details'  => 'Ver detalles',
            ],

            'show' => [
                'title'              => 'Comprobante de Pago',
                'order_info'         => 'Información del Pedido',
                'receipt_info'       => 'Información del Comprobante',
                'order_total'        => 'Total del Pedido',
                'order_date'         => 'Fecha del Pedido',
                'original_file'      => 'Archivo Original',
                'uploaded_date'      => 'Fecha de Subida',
                'verified_by'        => 'Verificado por',
                'admin_notes'        => 'Notas del Administrador',
                'approve'            => 'Aprobar Pago',
                'reject'             => 'Rechazar Pago',
                'notes_optional'     => 'Notas (opcional)',
                'rejection_reason'   => 'Razón del rechazo',
                'approve_message'    => 'Comprobante aprobado exitosamente',
                'reject_message'     => 'Comprobante rechazado',
                'approve_confirm'    => '¿Estás seguro de aprobar este comprobante?',
                'reject_confirm'     => '¿Estás seguro de rechazar este comprobante?',
            ],

            'status' => [
                'pending'  => 'Pendiente',
                'approved' => 'Aprobado',
                'rejected' => 'Rechazado',
            ],
        ],
    ],

    'shop' => [
        'checkout' => [
            'title'              => 'Yape / Plin',
            'description'        => 'Realiza tu pago mediante Yape o Plin y sube el comprobante',
            'instructions_title' => 'Instrucciones de pago:',
            'account_number'     => 'Número de cuenta:',
            'account_holder'     => 'Titular:',
            'upload_receipt'     => 'Comprobante de pago',
            'allowed_formats'    => 'Formatos permitidos: JPG, PNG, PDF (Máx. 5MB)',
            'verification_note'  => 'Tu pedido será verificado una vez que subas el comprobante de pago.',
            'confirmation_note'  => 'Recibirás una confirmación por correo cuando se apruebe el pago.',
        ],

        'errors' => [
            'file_required'  => 'Debes subir el comprobante de pago',
            'file_too_large' => 'El archivo es demasiado grande. El tamaño máximo es 5MB.',
            'invalid_type'   => 'Tipo de archivo no permitido. Solo se aceptan JPG, PNG o PDF.',
        ],
    ],
];
