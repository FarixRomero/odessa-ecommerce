<?php

return [
    [
        'key'    => 'sales.payment_methods.yapeplin',
        'name'   => 'Yape / Plin',
        'info'   => 'Método de pago Yape / Plin con subida de comprobante',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'active',
                'title'         => 'Estado',
                'type'          => 'boolean',
                'default_value' => true,
                'channel_based' => true,
            ],
            [
                'name'          => 'title',
                'title'         => 'Título',
                'type'          => 'text',
                'default_value' => 'Yape / Plin',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'description',
                'title'         => 'Descripción',
                'type'          => 'textarea',
                'default_value' => 'Realiza tu pago mediante Yape o Plin y sube el comprobante',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'instructions',
                'title'         => 'Instrucciones de Pago',
                'type'          => 'textarea',
                'default_value' => 'Por favor realiza el pago a la siguiente cuenta y sube tu comprobante de pago.',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'account_number',
                'title'         => 'Número de Cuenta',
                'type'          => 'text',
                'default_value' => '987654321',
                'channel_based' => false,
            ],
            [
                'name'          => 'account_holder',
                'title'         => 'Titular de la Cuenta',
                'type'          => 'text',
                'default_value' => 'Odessa E-commerce',
                'channel_based' => false,
            ],
            [
                'name'          => 'sort',
                'title'         => 'Orden de Clasificación',
                'type'          => 'text',
                'default_value' => '1',
            ],
        ],
    ],
];
