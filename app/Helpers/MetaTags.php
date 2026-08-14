<?php

namespace App\Helpers;

class MetaTags
{
    private static array $pages = [
        // Home
        '' => [
            'title' => 'GetVeraSign - Plataforma de Firma Digital | Soluciones de Firma Electrónica',
            'description' => 'GetVeraSign es la plataforma líder de firma digital en México. Firma documentos electrónicamente de forma segura, legal y eficiente. ¡Comienza gratis hoy!',
            'keywords' => 'firma digital, firma electrónica, documentos electrónicos, plataforma de firma, firma online, documentos digitales, firma segura',
            'image' => '/images/getverasign.webp',
        ],
        
        // Dashboard
        'dashboard' => [
            'title' => 'Dashboard | GetVeraSign',
            'description' => 'Tu panel de control de GetVeraSign. Gestiona documentos, monitorea firmas y automatiza procesos.',
            'keywords' => 'dashboard firma digital, gestión documentos',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        // Documentos
        'documentos' => [
            'title' => 'Mis Documentos | GetVeraSign',
            'description' => 'Gestiona todos tus documentos digitales. Envía, firma y recibe documentos de forma segura.',
            'keywords' => 'documentos digitales, gestión documentos, documentos electrónicos',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        // Nuevo Documento
        'documento/nuevo' => [
            'title' => 'Nuevo Documento | GetVeraSign',
            'description' => 'Crea y envía un nuevo documento para firmar. Carga, configura y envía en minutos.',
            'keywords' => 'nuevo documento, subir documento, crear firma',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        // Firmantes
        'firmantes' => [
            'title' => 'Gestión de Firmantes | GetVeraSign',
            'description' => 'Administra tus firmantes y contactos. Personaliza roles y permisos para mayor control.',
            'keywords' => 'firmantes, contactos, gestión de usuarios',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        // Configuración
        'configuracion' => [
            'title' => 'Configuración | GetVeraSign',
            'description' => 'Personaliza tu cuenta, seguridad y preferencias en GetVeraSign.',
            'keywords' => 'configuración cuenta, preferencias usuario, seguridad',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        // Policy Pages
        'privacy-policy' => [
            'title' => 'Política de Privacidad | GetVeraSign',
            'description' => 'Lee nuestra política de privacidad para entender cómo protegemos tu información.',
            'keywords' => 'privacidad, protección datos, política privacidad',
            'image' => '/images/getverasign.webp',
        ],
        
        'terms-of-service' => [
            'title' => 'Términos de Servicio | GetVeraSign',
            'description' => 'Revisa nuestros términos de servicio y comprende tus derechos.',
            'keywords' => 'términos servicio, acuerdo usuario, términos legales',
            'image' => '/images/getverasign.webp',
        ],

        'refund-policy' => [
            'title' => 'Política de Reembolso | GetVeraSign',
            'description' => 'Conoce nuestra política de reembolso y garantía de satisfacción.',
            'keywords' => 'política reembolso, garantía, devoluciones',
            'image' => '/images/getverasign.webp',
        ],
        
        // Auth Pages (no index)
        'login' => [
            'title' => 'Inicia Sesión | GetVeraSign',
            'description' => 'Inicia sesión en tu cuenta de GetVeraSign para acceder a tus documentos.',
            'keywords' => 'login, iniciar sesión, acceso cuenta',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        'register' => [
            'title' => 'Crear Cuenta | GetVeraSign',
            'description' => 'Crea una nueva cuenta en GetVeraSign y comienza a firmar documentos hoy.',
            'keywords' => 'registrarse, crear cuenta, nuevo usuario',
            'image' => '/images/getverasign.webp',
            'index' => false,
        ],
        
        'default' => [
            'title' => 'GetVeraSign - Plataforma de Firma Digital | Soluciones de Firma Electrónica',
            'description' => 'GetVeraSign es la plataforma líder de firma digital en México. Firma documentos electrónicamente de forma segura, legal y eficiente. ¡Comienza gratis hoy!',
            'keywords' => 'firma digital, firma electrónica, documentos electrónicos, plataforma de firma, firma online, documentos digitales, firma segura',
            'image' => '/images/getverasign.webp',
        ],
    ];

    public static function getMeta()
    {
        $path = trim(request()->getPathInfo(), '/');
        
        // Extraer el slug principal (sin query strings)
        $slug = explode('?', $path)[0];
        
        // Manejo de rutas dinámicas como /seguimiento-pedido/:orderNumber
        if (str_starts_with($slug, 'seguimiento-pedido/')) {
            return [
                'title' => 'Seguimiento de Pedido | Serviram',
                'description' => 'Realiza el seguimiento de tu pedido en tiempo real. Obtén actualizaciones sobre tu envío.',
                'keywords' => 'seguimiento pedido, rastreo envío, estado pedido',
                'image' => 'https://tienda.serviram.com.mx/og-image.png',
                'index' => false,
            ];
        }
        
        // Buscar la página en la configuración
        $page = self::$pages[$slug] ?? self::$pages['default'];
        
        return $page;
    }

    public static function getTitle()
    {
        return self::getMeta()['title'];
    }

    public static function getDescription()
    {
        return self::getMeta()['description'];
    }

    public static function getKeywords()
    {
        return self::getMeta()['keywords'];
    }

    public static function getImage()
    {
        return self::getMeta()['image'];
    }
    
    public static function shouldIndex()
    {
        $meta = self::getMeta();
        return $meta['index'] ?? true; // Index by default unless explicitly set to false
    }
}

