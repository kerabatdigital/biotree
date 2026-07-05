<?php

namespace App\Services;

class QrCodeService
{
    public static function generate(string $url, array $options = []): string
    {
        $size = $options['size'] ?? 300;
        $margin = $options['margin'] ?? 2;
        $format = $options['format'] ?? 'svg';
        
        $query = http_build_query([
            'data' => $url,
            'size' => "{$size}x{$size}",
            'margin' => $margin,
            'format' => $format,
            'download' => 0,
            'file' => 0,
        ]);
        
        return "https://api.qrserver.com/v1/create-qr-code/?{$query}";
    }

    public static function download(string $url, array $options = []): string
    {
        $options['download'] = 1;
        return self::generate($url, $options);
    }
}
