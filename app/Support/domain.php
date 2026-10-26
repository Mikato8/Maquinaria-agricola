<?php

declare(strict_types=1);

/**
 * Funciones de presentación propias de AgroMáquina MX.
 * Se mantienen aparte de helpers.php (base común) para que el dominio
 * de maquinaria agrícola quede en un solo archivo legible.
 */

if (!function_exists('category_config')) {
    /** @return array<string, array{name: string, emoji: string, singular?: string, description?: string}> */
    function category_config(string $group): array
    {
        $config = (array) config('app.' . $group, []);
        /** @var array<string, array{name: string, emoji: string}> $config */
        return $config;
    }
}

if (!function_exists('machine_category_label')) {
    function machine_category_label(?string $code): string
    {
        if ($code === 'implemento') {
            return 'Implementos';
        }
        if ($code === 'general') {
            return 'General';
        }
        return category_config('machine_categories')[$code ?: '']['name'] ?? 'Maquinaria';
    }
}

if (!function_exists('machine_category_singular')) {
    function machine_category_singular(?string $code): string
    {
        if ($code === 'implemento') {
            return 'Implemento';
        }
        if ($code === 'general') {
            return 'General';
        }
        return category_config('machine_categories')[$code ?: '']['singular'] ?? 'Máquina';
    }
}

if (!function_exists('machine_category_emoji')) {
    function machine_category_emoji(?string $code): string
    {
        if ($code === 'implemento') {
            return '🔧';
        }
        if ($code === 'general') {
            return '🛠️';
        }
        return category_config('machine_categories')[$code ?: '']['emoji'] ?? '🚜';
    }
}

if (!function_exists('implement_category_label')) {
    function implement_category_label(?string $code): string
    {
        return category_config('implement_categories')[$code ?: '']['name'] ?? 'Implemento';
    }
}

if (!function_exists('implement_category_emoji')) {
    function implement_category_emoji(?string $code): string
    {
        return category_config('implement_categories')[$code ?: '']['emoji'] ?? '🔧';
    }
}

if (!function_exists('fault_system_label')) {
    function fault_system_label(?string $code): string
    {
        return category_config('fault_systems')[$code ?: ''] ?? 'General';
    }
}

if (!function_exists('severity_label')) {
    function severity_label(?string $severity): string
    {
        return [
            'leve' => 'Leve',
            'media' => 'Media',
            'alta' => 'Alta',
            'critica' => 'Crítica',
        ][(string) $severity] ?? 'Media';
    }
}

if (!function_exists('severity_tone')) {
    function severity_tone(?string $severity): string
    {
        return [
            'leve' => 'success',
            'media' => 'warning',
            'alta' => 'danger',
            'critica' => 'danger',
        ][(string) $severity] ?? 'warning';
    }
}

if (!function_exists('frequency_label')) {
    function frequency_label(?string $frequency): string
    {
        return [
            'comun' => 'Falla común',
            'ocasional' => 'Ocasional',
            'rara' => 'Poco frecuente',
        ][(string) $frequency] ?? 'Ocasional';
    }
}

if (!function_exists('difficulty_label')) {
    function difficulty_label(?int $level): string
    {
        return match (true) {
            $level === null || $level <= 1 => 'Muy sencilla · herramienta básica',
            $level === 2 => 'Sencilla · herramienta básica',
            $level === 3 => 'Intermedia · taller o herramienta especial',
            $level === 4 => 'Avanzada · taller con equipo de diagnóstico',
            default => 'Especializada · concesionario o especialista',
        };
    }
}

if (!function_exists('difficulty_short')) {
    function difficulty_short(?int $level): string
    {
        return match (true) {
            $level === null || $level <= 1 => 'Muy sencilla',
            $level === 2 => 'Sencilla',
            $level === 3 => 'Intermedia',
            $level === 4 => 'Avanzada',
            default => 'Especializada',
        };
    }
}

if (!function_exists('tech_kind_label')) {
    function tech_kind_label(?string $kind): string
    {
        return category_config('tech_kinds')[$kind ?: ''] ?? 'Recurso';
    }
}

if (!function_exists('tech_kind_plural')) {
    function tech_kind_plural(?string $kind): string
    {
        return [
            'software' => 'Software',
            'hardware' => 'Hardware',
            'dataset' => 'Conjuntos de datos',
            'modelo' => 'Modelos de IA',
            'espacio' => 'Demos y espacios',
            'norma' => 'Normas y protocolos',
        ][(string) $kind] ?? 'Recursos';
    }
}

if (!function_exists('tech_category_label')) {
    function tech_category_label(?string $category): string
    {
        return category_config('tech_categories')[$category ?: ''] ?? 'Tecnología';
    }
}

if (!function_exists('provider_type_label')) {
    function provider_type_label(?string $type): string
    {
        return category_config('provider_types')[$type ?: ''] ?? 'Proveedor';
    }
}

if (!function_exists('guide_category_label')) {
    function guide_category_label(?string $category): string
    {
        return category_config('guide_categories')[$category ?: ''] ?? 'Guía';
    }
}

if (!function_exists('outcome_label')) {
    function outcome_label(?string $outcome): string
    {
        return [
            'resuelto' => 'Resuelto',
            'parcial' => 'Mejoró parcialmente',
            'no_resuelto' => 'No se resolvió',
        ][(string) $outcome] ?? 'Resuelto';
    }
}

if (!function_exists('outcome_tone')) {
    function outcome_tone(?string $outcome): string
    {
        return [
            'resuelto' => 'success',
            'parcial' => 'warning',
            'no_resuelto' => 'neutral',
        ][(string) $outcome] ?? 'neutral';
    }
}

if (!function_exists('role_label')) {
    function role_label(?string $role): string
    {
        return category_config('roles')[$role ?: ''] ?? 'Productor';
    }
}

if (!function_exists('verification_label')) {
    function verification_label(?string $method): string
    {
        return [
            'cedula' => 'Cédula profesional',
            'taller' => 'Taller registrado',
            'manual' => 'Revisión manual',
            'admin' => 'Alta administrativa',
        ][(string) $method] ?? 'Revisión manual';
    }
}

if (!function_exists('num')) {
    /** Número en formato es-MX sin decimales innecesarios. */
    function num(float|int|string|null $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        $number = (float) $value;
        $formatted = number_format($number, $decimals, '.', ',');
        if ($decimals === 0) {
            return $formatted;
        }
        return rtrim(rtrim($formatted, '0'), '.');
    }
}

if (!function_exists('hp_label')) {
    function hp_label(float|int|string|null $hp): string
    {
        if ($hp === null || $hp === '') {
            return '—';
        }
        return num($hp, 1) . ' HP';
    }
}

if (!function_exists('power_range')) {
    function power_range(float|int|string|null $min, float|int|string|null $max): string
    {
        if (($min === null || $min === '') && ($max === null || $max === '')) {
            return 'Sin dato';
        }
        if ($max === null || $max === '' || (float) $max === (float) $min) {
            return hp_label($min);
        }
        return num($min) . '–' . num($max) . ' HP';
    }
}

if (!function_exists('price_range')) {
    function price_range(float|int|string|null $min, float|int|string|null $max): string
    {
        if (($min === null || $min === '') && ($max === null || $max === '')) {
            return 'Precio a cotización';
        }
        if ($max === null || $max === '' || (float) $max === (float) $min) {
            return compact_money($min);
        }
        return compact_money($min) . ' – ' . compact_money($max);
    }
}

if (!function_exists('hours_label')) {
    function hours_label(float|int|string|null $hours): string
    {
        if ($hours === null || $hours === '') {
            return '—';
        }
        $value = (float) $hours;
        if ($value < 1) {
            return num($value * 60) . ' min';
        }
        return num($value, 1) . ' h';
    }
}

if (!function_exists('decode_json_list')) {
    /** @return array<int, array<string, mixed>> */
    function decode_json_list(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || trim($value) === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('likelihood_label')) {
    function likelihood_label(?string $value): string
    {
        return [
            'alta' => 'Causa más probable',
            'media' => 'Causa probable',
            'baja' => 'Causa posible',
        ][(string) $value] ?? 'Causa probable';
    }
}

if (!function_exists('likelihood_tone')) {
    function likelihood_tone(?string $value): string
    {
        return [
            'alta' => 'danger',
            'media' => 'warning',
            'baja' => 'neutral',
        ][(string) $value] ?? 'neutral';
    }
}

if (!function_exists('years_label')) {
    function years_label(?int $from, ?int $to): string
    {
        if ($from === null && $to === null) {
            return 'Modelo vigente';
        }
        if ($to === null || $to === 0) {
            return 'Desde ' . $from;
        }
        if ($from === $to) {
            return (string) $from;
        }
        return $from . '–' . $to;
    }
}

if (!function_exists('reading_minutes')) {
    function reading_minutes(string $text): int
    {
        $words = str_word_count(strip_tags($text));
        return max(1, (int) round($words / 200));
    }
}

if (!function_exists('state_options')) {
    /** @return array<int, array{code: string, name: string, region: string}> */
    function state_options(): array
    {
        static $states = null;
        if ($states === null) {
            try {
                $rows = App\Core\Database::select('select code, name, region from states order by name');
                $states = array_map(static fn (array $row): array => [
                    'code' => (string) $row['code'],
                    'name' => (string) $row['name'],
                    'region' => (string) $row['region'],
                ], $rows);
            } catch (Throwable) {
                $states = [];
            }
        }
        return $states;
    }
}

if (!function_exists('state_name')) {
    function state_name(?string $code): string
    {
        foreach (state_options() as $state) {
            if ($state['code'] === $code) {
                return $state['name'];
            }
        }
        return 'México';
    }
}

if (!function_exists('location_label')) {
    function location_label(?string $city, ?string $stateCode): string
    {
        $parts = array_values(array_filter([trim((string) $city), $stateCode ? state_name($stateCode) : '']));
        return $parts === [] ? 'México' : implode(', ', $parts);
    }
}

if (!function_exists('workshop_status_tone')) {
    function workshop_status_tone(?string $status): string
    {
        return [
            'borrador' => 'neutral',
            'en_revision' => 'warning',
            'cambios_solicitados' => 'warning',
            'publicado' => 'success',
            'rechazado' => 'danger',
            'archivado' => 'neutral',
        ][(string) $status] ?? 'neutral';
    }
}

if (!function_exists('query_int')) {
    function query_int(string $key, int $default = 0): int
    {
        $value = $_GET[$key] ?? null;
        return is_numeric($value) ? (int) $value : $default;
    }
}

if (!function_exists('query_str')) {
    function query_str(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? null;
        return is_string($value) ? trim($value) : $default;
    }
}

if (!function_exists('filter_url')) {
    /** Conserva los filtros actuales y sustituye uno. */
    function filter_url(array $overrides = []): string
    {
        $query = $_GET;
        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($query[$key]);
            } else {
                $query[$key] = $value;
            }
        }
        $path = current_path();
        $qs = http_build_query($query);
        return url($path) . ($qs !== '' ? '?' . $qs : '');
    }
}

if (!function_exists('bookmark_type_label')) {
    function bookmark_type_label(?string $type): string
    {
        return [
            'maquina' => 'Máquina',
            'implemento' => 'Implemento',
            'falla' => 'Falla',
            'guia' => 'Guía',
            'taller' => 'Caso de taller',
            'recurso' => 'Tecnología',
        ][(string) $type] ?? 'Guardado';
    }
}

if (!function_exists('level_label')) {
    function level_label(?string $level): string
    {
        return [
            'introductorio' => 'Introductorio',
            'intermedio' => 'Intermedio',
            'avanzado' => 'Avanzado',
        ][(string) $level] ?? 'Introductorio';
    }
}

if (!function_exists('kind_icon')) {
    function kind_icon(?string $kind): string
    {
        return [
            'software' => 'code',
            'hardware' => 'chip',
            'dataset' => 'database',
            'modelo' => 'cpu',
            'espacio' => 'sparkles',
            'norma' => 'shield-check',
        ][(string) $kind] ?? 'cpu';
    }
}

if (!function_exists('crop_label')) {
    function crop_label(?string $code): string
    {
        return [
            'maiz' => 'Maíz', 'maiz-forrajero' => 'Maíz forrajero', 'frijol' => 'Frijol', 'trigo' => 'Trigo',
            'sorgo' => 'Sorgo', 'soya' => 'Soya', 'cana' => 'Caña de azúcar', 'agave' => 'Agave',
            'aguacate' => 'Aguacate', 'cafe' => 'Café', 'jitomate' => 'Jitomate', 'chile' => 'Chile',
            'papa' => 'Papa', 'cebolla' => 'Cebolla', 'algodon' => 'Algodón', 'calabaza' => 'Calabaza',
            'sandia' => 'Sandía', 'melón' => 'Melón', 'vid' => 'Vid', 'nogal' => 'Nogal',
            'limon' => 'Limón', 'naranja' => 'Naranja', 'mango' => 'Mango', 'platano' => 'Plátano',
            'fresa' => 'Fresa', 'guayaba' => 'Guayaba', 'coco' => 'Coco', 'pina' => 'Piña',
            'otros' => 'Otro cultivo',
        ][(string) $code] ?? 'Maíz';
    }
}

if (!function_exists('crop_options')) {
    /** @return array<string, string> */
    function crop_options(): array
    {
        return [
            'maiz' => 'Maíz', 'maiz-forrajero' => 'Maíz forrajero', 'frijol' => 'Frijol', 'trigo' => 'Trigo',
            'sorgo' => 'Sorgo', 'soya' => 'Soya', 'cana' => 'Caña de azúcar', 'agave' => 'Agave',
            'aguacate' => 'Aguacate', 'cafe' => 'Café', 'jitomate' => 'Jitomate', 'chile' => 'Chile',
            'papa' => 'Papa', 'cebolla' => 'Cebolla', 'algodon' => 'Algodón', 'calabaza' => 'Calabaza',
            'sandia' => 'Sandía', 'vid' => 'Vid', 'nogal' => 'Nogal', 'limon' => 'Limón',
            'naranja' => 'Naranja', 'mango' => 'Mango', 'platano' => 'Plátano', 'fresa' => 'Fresa',
            'otros' => 'Otro cultivo',
        ];
    }
}
