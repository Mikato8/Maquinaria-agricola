<?php

declare(strict_types=1);

if (!function_exists('icon')) {
    /**
     * Iconos SVG inline (trazo de 1.7 px, 24×24). Sin dependencias externas.
     */
    function icon(string $name, string $class = 'icon'): string
    {
        $paths = [
            'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
            'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'arrow-left' => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
            'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
            'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
            'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
            'check' => '<path d="m5 12 4 4L19 6"/>',
            'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
            'alert-triangle' => '<path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v5M12 17.5v.5"/>',
            'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.5v.5"/>',
            'external-link' => '<path d="M14 4h6v6M20 4l-9 9"/><path d="M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/>',
            'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>',
            'map-pin' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
            'stethoscope' => '<path d="M6 3v5a5 5 0 0 0 10 0V3"/><path d="M4 3h4M14 3h4"/><path d="M16 13v2a5 5 0 0 1-10 0"/><circle cx="19" cy="11" r="2.5"/><path d="M19 13.5V17a2 2 0 0 1-2 2h-1"/>',
            'book-open' => '<path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H11v18H5.5A2.5 2.5 0 0 1 3 18.5v-13Z"/><path d="M21 5.5A2.5 2.5 0 0 0 18.5 3H13v18h5.5a2.5 2.5 0 0 0 2.5-2.5v-13Z"/>',
            'database' => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
            'activity' => '<path d="M3 12h4l2.5-7 4 14 2.5-7h5"/>',
            'heart-pulse' => '<path d="M20.8 5.6a5.5 5.5 0 0 0-7.8 0L12 6.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 22l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/><path d="M3.5 12h4l1.5-3 2.5 6 1.5-3h4"/>',
            'paw' => '<circle cx="6" cy="9" r="2"/><circle cx="10" cy="5" r="2"/><circle cx="15" cy="5" r="2"/><circle cx="18.5" cy="9" r="2"/><path d="M12 11c-2.8 0-5 2.2-5 5 0 2.5 2 4 5 4s5-1.5 5-4c0-2.8-2.2-5-5-5Z"/>',
            'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 21a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M17.5 15.5A6.5 6.5 0 0 1 22 21"/>',
            'plus' => '<path d="M12 5v14M5 12h14"/>',
            'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"/>',
            'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
            'eye-off' => '<path d="m3 3 18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A9.8 9.8 0 0 1 12 5c7 0 10 7 10 7a17 17 0 0 1-3.2 4.1"/><path d="M6.6 6.6C3.6 8.5 2 12 2 12s3 7 10 7a9.8 9.8 0 0 0 5.4-1.6"/>',
            'message-circle' => '<path d="M21 11.5a8.5 8.5 0 0 1-12.4 7.5L3 21l2-5.6A8.5 8.5 0 1 1 21 11.5Z"/>',
            'share' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-4M8.6 13.5l6.8 4"/>',
            'copy' => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/>',
            'filter' => '<path d="M3 5h18l-7 8v6l-4 2v-8L3 5Z"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>',
            'award' => '<circle cx="12" cy="8" r="5"/><path d="m8.5 12-2 9 5.5-3 5.5 3-2-9"/>',
            'sparkles' => '<path d="m12 3 1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6L12 3Z"/><path d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"/><path d="m5 3 .6 1.4L7 5l-1.4.6L5 7l-.6-1.4L3 5l1.4-.6L5 3Z"/>',
            'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h6"/>',
            'upload' => '<path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 20h16"/>',
            'trash' => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/>',
            'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21h-4v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3v-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.5V3h4v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.5 1h.1v4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
            'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
            'folder' => '<path d="M3 6a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6Z"/>',
            'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'trending-up' => '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/>',
            'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 3.8 5.6 3.8 9S14.5 18.5 12 21c-2.5-2.5-3.8-5.6-3.8-9S9.5 5.5 12 3Z"/>',
            'lock' => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
            'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'flask' => '<path d="M9 3h6M10 3v6L4.5 18a2 2 0 0 0 1.7 3h11.6a2 2 0 0 0 1.7-3L14 9V3"/><path d="M7 15h10"/>',
            'microscope' => '<path d="M6 18h12M9 18V9a3 3 0 0 1 6 0v9"/><path d="M12 6V3M9 9H6M18 21a6 6 0 0 0-6-6"/>',
            'pin' => '<path d="M12 21s7-6 7-11a7 7 0 1 0-14 0c0 5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
            'bookmark' => '<path d="M6 3h12v18l-6-4-6 4V3Z"/>',
            'cpu' => '<rect x="7" y="7" width="10" height="10" rx="2"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/>',
            'layers' => '<path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/>',
            'download' => '<path d="M12 3v12M7 10l5 5 5-5"/><path d="M4 20h16"/>',
            'printer' => '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M6 14h12v7H6z"/>',
            'sprout' => '<path d="M12 21v-8"/><path d="M12 13c0-4-3-6-7-6 0 4 3 6 7 6Z"/><path d="M12 13c0-4 3-6 7-6 0 4-3 6-7 6Z"/><path d="M7 21h10"/>',
            'tree' => '<path d="M12 21v-5"/><path d="M12 3 6 12h3l-3 5h12l-3-5h3L12 3Z"/>',
            'wheat' => '<path d="M12 21V9"/><path d="M12 9c-3 0-4-2-4-5 3 0 4 2 4 5Z"/><path d="M12 9c3 0 4-2 4-5-3 0-4 2-4 5Z"/><path d="M12 15c-3 0-4-2-4-5 3 0 4 2 4 5Z"/><path d="M12 15c3 0 4-2 4-5-3 0-4 2-4 5Z"/>',
            'leaf' => '<path d="M20 4C12 4 5 8 5 15c0 3 2 5 5 5 7 0 10-7 10-16Z"/><path d="M4 21c2-6 6-9 11-11"/>',
            'tractor' => '<path d="M4 16h3"/><circle cx="7" cy="17" r="3.2"/><circle cx="18" cy="18" r="2.6"/><path d="M10.5 17H12l2-6h-4l1-4h3l1.5 4H19v4"/>',
            'wrench' => '<path d="M14.5 6.5a4 4 0 1 0 5 5L21 10l-2.5-2.5-1.5 1.5-1-1L17.5 6l-3-3-2.5 2.5a5.5 5.5 0 0 0-2 6.4L3 19l2 2 7.1-7.1a5.5 5.5 0 0 0 2.4-7.4Z"/>',
            'gauge' => '<path d="M4 18a9 9 0 1 1 16 0"/><path d="M12 14l4-4"/><circle cx="12" cy="18" r="1.4"/>',
            'droplet' => '<path d="M12 3s6 6.5 6 10.5a6 6 0 0 1-12 0C6 9.5 12 3 12 3Z"/>',
            'zap' => '<path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"/>',
            'fuel' => '<path d="M4 21V5a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v16"/><path d="M3 21h11"/><path d="M6 7h5v4H6z"/><path d="M13 8h3l3 3v7a2 2 0 0 1-4 0v-5h-2"/>',
            'cog' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1"/>',
            'truck' => '<path d="M2 16V6h11v10"/><path d="M13 9h4l3 3v4h-7"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/><path d="M2 16h2M8 16h7"/>',
            'satellite' => '<path d="m8 8 8 8"/><path d="m4 12 4-4 4 4-4 4-4-4Z"/><path d="m12 20 4-4 4 4-4 4-4-4Z"/><path d="M16 4h4v4"/><path d="M14 6a6 6 0 0 1 4 4"/>',
            'code' => '<path d="m8 6-5 6 5 6M16 6l5 6-5 6"/>',
            'chip' => '<rect x="6" y="6" width="12" height="12" rx="2"/><path d="M10 10h4v4h-4z"/><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"/>',
            'thermometer' => '<path d="M14 14.8V5a2 2 0 1 0-4 0v9.8a4 4 0 1 0 4 0Z"/><path d="M12 9v6"/>',
            'shield-check' => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
            'calculator' => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M8 6h8M8 11h2M12 11h2M16 11h.01M8 15h2M12 15h2M16 15h.01M8 19h6"/>',
            'clipboard' => '<rect x="5" y="4" width="14" height="18" rx="2"/><path d="M9 4V2h6v2"/><path d="M9 10h6M9 14h6M9 18h3"/>',
            'tool' => '<path d="M3 21l6-6"/><path d="M14 4a5 5 0 0 0-6.5 6.5L3 15l3 3 4.5-4.5A5 5 0 0 0 17 7l-3 3-2-2 3-3a5 5 0 0 1-1-1Z"/>',
            'battery' => '<rect x="2" y="8" width="17" height="9" rx="2"/><path d="M21 11v3"/><path d="M7 12v1M11 12v1"/>',
            'wind' => '<path d="M3 8h11a3 3 0 1 0-3-3"/><path d="M3 12h15a3 3 0 1 1-3 3"/><path d="M3 16h7"/>',
            'hard-hat' => '<path d="M3 17h18"/><path d="M5 17v-3a7 7 0 0 1 14 0v3"/><path d="M10 7V4h4v3"/>',
            'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15M15 6v15"/>',
        ];

        $path = $paths[$name] ?? $paths['info'];
        return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
    }
}

if (!function_exists('crop_emoji')) {
    function crop_emoji(?string $code): string
    {
        return [
            'maiz' => '🌽', 'frijol' => '🫘', 'trigo' => '🌾', 'cafe' => '☕',
            'aguacate' => '🥑', 'limon' => '🍋', 'naranja' => '🍊', 'mango' => '🥭',
            'platano' => '🍌', 'jitomate' => '🍅', 'chile' => '🌶️', 'papa' => '🥔',
            'fresa' => '🍓', 'vid' => '🍇', 'cacao' => '🍫', 'pina' => '🍍',
            'sandia' => '🍉', 'calabaza' => '🎃', 'brocoli' => '🥦', 'zanahoria' => '🥕',
        ][(string) $code] ?? '🌱';
    }
}
