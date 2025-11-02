<?php
/**
 * Configuración del servicio WhatsApp
 * Configuración activa para desarrollo
 */

return [
    // Proveedor por defecto: 'simulation' para desarrollo
    'provider' => 'simulation',
    
    // Configuración para simulación (desarrollo)
    'simulation' => [
        'log_file' => 'whatsapp_simulacion.log'
    ],
    
    // Configuración para Twilio (comentada para desarrollo)
    /*
    'twilio' => [
        'account_sid' => 'tu_account_sid_aqui',
        'auth_token' => 'tu_auth_token_aqui',
        'from_number' => '+14155238886' // Sandbox de Twilio o tu número verificado
    ],
    */
    
    // Configuración para WhatsApp Business API (comentada para desarrollo)
    /*
    'whatsapp_business' => [
        'access_token' => 'tu_access_token_aqui',
        'phone_number_id' => 'tu_phone_number_id_aqui',
        'version' => 'v17.0'
    ]
    */
];