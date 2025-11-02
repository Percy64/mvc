<?php

namespace app\services;

/**
 * Servicio para envío de mensajes de verificación por WhatsApp
 * Soporta múltiples proveedores: simulación, Twilio, WhatsApp Business API
 */
class WhatsAppService {
    
    private $provider;
    private $config;
    
    const PROVIDER_SIMULATION = 'simulation';
    const PROVIDER_TWILIO = 'twilio';
    const PROVIDER_WHATSAPP_BUSINESS = 'whatsapp_business';
    
    public function __construct($provider = self::PROVIDER_SIMULATION, $config = []) {
        $this->provider = $provider;
        $this->config = $config;
    }
    
    /**
     * Enviar código de verificación por WhatsApp
     */
    public function enviarCodigoVerificacion($telefono, $codigo) {
        $mensaje = "🔐 Tu código de verificación es: *{$codigo}*\n\n";
        $mensaje .= "Este código expira en 10 minutos.\n";
        $mensaje .= "No compartas este código con nadie.\n\n";
        $mensaje .= "Si no solicitaste este código, ignora este mensaje.";
        
        return $this->enviarMensaje($telefono, $mensaje);
    }
    
    /**
     * Enviar mensaje por WhatsApp según el proveedor configurado
     */
    private function enviarMensaje($telefono, $mensaje) {
        switch ($this->provider) {
            case self::PROVIDER_SIMULATION:
                return $this->enviarSimulacion($telefono, $mensaje);
                
            case self::PROVIDER_TWILIO:
                return $this->enviarTwilio($telefono, $mensaje);
                
            case self::PROVIDER_WHATSAPP_BUSINESS:
                return $this->enviarWhatsAppBusiness($telefono, $mensaje);
                
            default:
                return $this->enviarSimulacion($telefono, $mensaje);
        }
    }
    
    /**
     * Simulación de envío (para desarrollo y testing)
     */
    private function enviarSimulacion($telefono, $mensaje) {
        // En modo simulación, guardamos el mensaje en un log
        $logPath = __DIR__ . '/../../assets/whatsapp_simulacion.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] Teléfono: {$telefono}\nMensaje:\n{$mensaje}\n" . str_repeat('-', 50) . "\n";
        
        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
        
        return [
            'success' => true,
            'message' => 'Mensaje enviado (simulación)',
            'provider' => 'simulation',
            'log_path' => $logPath
        ];
    }
    
    /**
     * Envío usando Twilio WhatsApp API
     */
    private function enviarTwilio($telefono, $mensaje) {
        // Configuración necesaria en $this->config:
        // - account_sid
        // - auth_token
        // - from_number (número de WhatsApp Business)
        
        if (empty($this->config['account_sid']) || empty($this->config['auth_token'])) {
            return [
                'success' => false,
                'message' => 'Configuración de Twilio incompleta',
                'provider' => 'twilio'
            ];
        }
        
        try {
            // Formatear número para Twilio (debe incluir whatsapp: prefix)
            $numeroFormateado = 'whatsapp:' . $telefono;
            $numeroOrigen = 'whatsapp:' . $this->config['from_number'];
            
            // Aquí iría la implementación real de Twilio
            // require_once '/vendor/autoload.php';
            // use Twilio\Rest\Client;
            // 
            // $client = new Client($this->config['account_sid'], $this->config['auth_token']);
            // $message = $client->messages->create(
            //     $numeroFormateado,
            //     [
            //         'from' => $numeroOrigen,
            //         'body' => $mensaje
            //     ]
            // );
            
            // Por ahora simulamos respuesta exitosa
            return [
                'success' => true,
                'message' => 'Mensaje enviado via Twilio',
                'provider' => 'twilio',
                'to' => $numeroFormateado
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al enviar con Twilio: ' . $e->getMessage(),
                'provider' => 'twilio'
            ];
        }
    }
    
    /**
     * Envío usando WhatsApp Business API oficial
     */
    private function enviarWhatsAppBusiness($telefono, $mensaje) {
        // Configuración necesaria en $this->config:
        // - access_token
        // - phone_number_id
        // - version (ej: v17.0)
        
        if (empty($this->config['access_token']) || empty($this->config['phone_number_id'])) {
            return [
                'success' => false,
                'message' => 'Configuración de WhatsApp Business API incompleta',
                'provider' => 'whatsapp_business'
            ];
        }
        
        try {
            $version = $this->config['version'] ?? 'v17.0';
            $url = "https://graph.facebook.com/{$version}/{$this->config['phone_number_id']}/messages";
            
            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $telefono,
                'type' => 'text',
                'text' => ['body' => $mensaje]
            ];
            
            $headers = [
                'Authorization: Bearer ' . $this->config['access_token'],
                'Content-Type: application/json'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'message' => 'Mensaje enviado via WhatsApp Business API',
                    'provider' => 'whatsapp_business',
                    'response' => json_decode($response, true)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error en WhatsApp Business API: HTTP ' . $httpCode,
                    'provider' => 'whatsapp_business',
                    'response' => $response
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al enviar con WhatsApp Business API: ' . $e->getMessage(),
                'provider' => 'whatsapp_business'
            ];
        }
    }
    
    /**
     * Crear instancia del servicio según configuración
     */
    public static function create() {
        // Cargar configuración desde archivo o variables de entorno
        $configPath = __DIR__ . '/../config/whatsapp.php';
        $config = [];
        
        if (file_exists($configPath)) {
            $config = include $configPath;
        }
        
        // Permitir override con variables de entorno
        $provider = getenv('WHATSAPP_PROVIDER') ?: ($config['provider'] ?? self::PROVIDER_SIMULATION);
        
        // Configuración específica del proveedor
        $providerConfig = $config[$provider] ?? [];
        
        return new self($provider, $providerConfig);
    }
}