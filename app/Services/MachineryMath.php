<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Cálculos de ingeniería agrícola para tractores e implementos.
 *
 * Todas las estimaciones son orientativas y se basan en fórmulas de tiro
 * ampliamente usadas en mecanización agrícola:
 *
 *   Fuerza de tiro (kgf) = k · ancho(dm) · profundidad(dm) · factores
 *   Potencia (kW)        = Fuerza(N) · velocidad(m/s) / (1000 · eficiencia)
 *
 * donde k es la resistencia específica del suelo en kgf/dm² de sección.
 * Los valores de k y los factores de suelo provienen de rangos publicados
 * para labranza; el resultado sirve para dimensionar, no para garantizar
 * el desempeño en una parcela concreta.
 */
final class MachineryMath
{
    public const DISCLAIMER = 'Estimación orientativa de ingeniería. Confirma potencia, levante, caudal y lastre con el manual del fabricante y con una prueba en campo antes de comprar o acoplar.';

    /**
     * Resistencia específica de referencia (kgf/dm² de sección) por categoría
     * de implemento, en suelo franco y humedad óptima.
     *
     * @return array<string, float>
     */
    public static function draftFactors(): array
    {
        return [
            'labranza' => 55.0,
            'siembra' => 22.0,
            'fertilizacion' => 18.0,
            'proteccion' => 12.0,
            'cosecha' => 20.0,
            'forraje' => 25.0,
            'transporte' => 8.0,
            'riego' => 5.0,
            'postcosecha' => 10.0,
            'jardin' => 15.0,
        ];
    }

    /** @return array<string, float> */
    public static function soilFactors(): array
    {
        return [
            'arenoso' => 0.78,
            'franco' => 1.00,
            'limoso' => 0.92,
            'arcilloso' => 1.35,
            'pedregoso' => 1.25,
        ];
    }

    /** @return array<string, float> */
    public static function moistureFactors(): array
    {
        return [
            'seco' => 1.18,
            'optimo' => 1.00,
            'humedo' => 0.85,
        ];
    }

    /**
     * Coeficiente de resistencia específica del implemento concreto.
     * Usa el valor declarado en la ficha (draft_factor) o el de su categoría.
     *
     * @param array<string, mixed> $implement
     */
    public static function draftFactor(array $implement): float
    {
        $declared = (float) ($implement['draft_factor'] ?? 0);
        if ($declared > 0) {
            return $declared;
        }
        $category = (string) ($implement['category'] ?? 'labranza');
        return self::draftFactors()[$category] ?? 30.0;
    }

    /**
     * @param array<string, mixed> $tractor   claves: power_hp, pto_hp, weight_kg, lift_capacity_kg, hydraulic_flow_lpm, traction
     * @param array<string, mixed> $implement claves: category, working_width_m, working_depth_cm, weight_kg, hitch, required_flow_lpm, min_lift_kg, drive_required, draft_factor
     * @param array<string, mixed> $options   claves: soil, moisture, slope_pct, speed_kmh, efficiency, diesel_price
     * @return array<string, mixed>
     */
    public static function compatibility(array $tractor, array $implement, array $options = []): array
    {
        $soil = (string) ($options['soil'] ?? 'franco');
        $moisture = (string) ($options['moisture'] ?? 'optimo');
        $slope = max(0.0, min(35.0, (float) ($options['slope_pct'] ?? 0)));
        $speed = max(2.0, min(14.0, (float) ($options['speed_kmh'] ?? 6.5)));

        $widthM = max(0.3, min(12.0, (float) ($implement['working_width_m'] ?? 2.0)));
        $depthCm = max(0.0, min(90.0, (float) ($implement['working_depth_cm'] ?? 0.0)));
        $depthM = $depthCm / 100;

        $soilFactor = self::soilFactors()[$soil] ?? 1.0;
        $moistureFactor = self::moistureFactors()[$moisture] ?? 1.0;
        $slopeFactor = 1 + ($slope * 0.012);
        $k = self::draftFactor($implement);

        // Fuerza de tiro. El ancho en dm y la profundidad en dm dan la sección en dm².
        $draftKgf = $k * ($widthM * 10) * ($depthCm / 10) * $soilFactor * $moistureFactor * $slopeFactor;
        $draftN = $draftKgf * 9.80665;

        $traction = (string) ($tractor['traction'] ?? '4WD');
        $tractionEfficiency = match ($traction) {
            'oruga' => 0.86,
            '4WD' => 0.78,
            '2WD' => 0.70,
            default => 0.75,
        };

        $speedMs = $speed / 3.6;
        $powerKw = $draftN > 0 ? ($draftN * $speedMs) / (1000 * $tractionEfficiency) : 0.0;
        $powerHp = $powerKw / 0.7457;

        $tractorHp = max(0.0, (float) ($tractor['power_hp'] ?? 0));
        $ptoHp = (float) ($tractor['pto_hp'] ?? 0);
        $usableHp = $tractorHp * 0.9;
        $loadPct = $tractorHp > 0 ? $powerHp / $tractorHp : 999;

        $verdict = 'sin_datos';
        if ($tractorHp > 0) {
            $verdict = match (true) {
                $loadPct > 1.05 => 'insuficiente',
                $loadPct > 0.9 => 'al_limite',
                $loadPct >= 0.55 => 'adecuado',
                default => 'sobredimensionado',
            };
        }

        $warnings = [];
        $recommendations = [];

        if ($verdict === 'insuficiente') {
            $warnings[] = [
                'tone' => 'danger',
                'title' => 'Tractor subpotenciado',
                'text' => sprintf(
                    'El implemento exige cerca de %s HP en el motor a %s km/h y el tractor declara %s HP (%.0f %% de su potencia). Reduce el ancho de trabajo, la profundidad o la velocidad, o usa un tractor mayor.',
                    num($powerHp),
                    num($speed, 1),
                    num($tractorHp),
                    $loadPct * 100
                ),
            ];
        } elseif ($verdict === 'al_limite') {
            $warnings[] = [
                'tone' => 'warning',
                'title' => 'Al límite de potencia',
                'text' => sprintf(
                    'Trabajarás al %.0f %% de la potencia nominal. Es viable en suelo franco y seco, pero en temporada de lluvia o en pendiente el motor perderá reserva. Considera una marcha más corta o menor profundidad.',
                    $loadPct * 100
                ),
            ];
        } elseif ($verdict === 'sobredimensionado') {
            $warnings[] = [
                'tone' => 'warning',
                'title' => 'Tractor sobrado para este implemento',
                'text' => sprintf(
                    'Solo usarás cerca del %.0f %% de la potencia. Es seguro, pero el consumo por hectárea y la compactación por peso pueden subir. Un implemento más ancho (hasta ~%.1f m con este tractor) aprovecharía mejor la máquina.',
                    $loadPct * 100,
                    self::maxWidthForPower($tractorHp, $k, $depthCm, $speed, $soilFactor * $moistureFactor)
                ),
            ];
        }

        // Levante hidráulico
        $liftCapacity = (float) ($tractor['lift_capacity_kg'] ?? 0);
        $implementWeight = (float) ($implement['weight_kg'] ?? 0);
        $hitch = (string) ($implement['hitch'] ?? '3 puntos');
        $liftUse = 0;
        if ($hitch === '3 puntos' && $liftCapacity > 0 && $implementWeight > 0) {
            $liftUse = $implementWeight / $liftCapacity;
            if ($implementWeight > $liftCapacity) {
                $warnings[] = [
                    'tone' => 'danger',
                    'title' => 'Excede la capacidad de levante',
                    'text' => sprintf(
                        'El implemento pesa %s kg y el levante del tractor admite %s kg a 610 mm de los brazos. No es un acople seguro: usa un implemento más ligero, lastre el eje delantero o considera un equipo de arrastre.',
                        num($implementWeight),
                        num($liftCapacity)
                    ),
                ];
            } elseif ($liftUse > 0.8) {
                $warnings[] = [
                    'tone' => 'warning',
                    'title' => 'Levante muy exigido',
                    'text' => sprintf(
                        'Usarás el %.0f %% de la capacidad de levante (%s de %s kg). Deja margen para el peso del lastre, el suelo adherido y las maniobras en cabecera.',
                        $liftUse * 100,
                        num($implementWeight),
                        num($liftCapacity)
                    ),
                ];
            }
        }

        // Caudal hidráulico
        $requiredFlow = (float) ($implement['required_flow_lpm'] ?? 0);
        $tractorFlow = (float) ($tractor['hydraulic_flow_lpm'] ?? 0);
        if ($requiredFlow > 0 && $tractorFlow > 0 && $requiredFlow > $tractorFlow * 0.85) {
            $warnings[] = [
                'tone' => $requiredFlow > $tractorFlow ? 'danger' : 'warning',
                'title' => 'Caudal hidráulico justo',
                'text' => sprintf(
                    'El implemento pide %s L/min y el tractor entrega %s L/min. Con el aceite caliente el caudal baja: verifica el régimen de la bomba y usa mangueras del diámetro correcto.',
                    num($requiredFlow, 1),
                    num($tractorFlow, 1)
                ),
            ];
        }

        // Tracción
        $driveRequired = (string) ($implement['drive_required'] ?? 'cualquiera');
        if ($driveRequired === '4WD' && $traction === '2WD') {
            $warnings[] = [
                'tone' => 'danger',
                'title' => 'Requiere doble tracción',
                'text' => 'Este implemento necesita tracción a las cuatro ruedas para transmitir el tiro sin patinar, sobre todo en suelo arcilloso o con pendiente.',
            ];
        }
        if ($slope > 8 && $traction === '2WD') {
            $warnings[] = [
                'tone' => 'warning',
                'title' => 'Pendiente con tracción sencilla',
                'text' => 'En pendientes mayores a 8 % la tracción sencilla pierde adherencia. Baja la velocidad, trabaja en curvas de nivel y valora lastre adicional.',
            ];
        }

        // Capacidad de trabajo
        $fieldEfficiency = 0.78;
        $haPerHour = ($widthM * $speed * $fieldEfficiency) / 10;
        $hoursPerHa = $haPerHour > 0 ? 1 / $haPerHour : 0;

        // Consumo estimado de diésel: 0.23 L por HP·h a carga alta.
        $loadForFuel = min(1.0, max(0.35, $loadPct));
        $fuelPerHour = $tractorHp * 0.23 * $loadForFuel;
        $fuelPerHa = $haPerHour > 0 ? $fuelPerHour / $haPerHour : 0;
        $dieselPrice = (float) ($options['diesel_price'] ?? 27.00);
        $fuelCostPerHa = $fuelPerHa * $dieselPrice;

        // Lastre sugerido para levante montado
        $tractorWeight = (float) ($tractor['weight_kg'] ?? 0);
        $ballastKg = 0;
        if ($hitch === '3 puntos' && $tractorWeight > 0 && $implementWeight > 0) {
            $total = $tractorWeight + $implementWeight;
            $frontStatic = $tractorWeight * 0.42;
            $frontNeeded = $total * 0.20;
            $ballastKg = max(0, $frontNeeded - $frontStatic);
        }

        // Ancho máximo recomendado con la potencia disponible
        $maxWidth = $tractorHp > 0 ? self::maxWidthForPower($tractorHp, $k, $depthCm, $speed, $soilFactor * $moistureFactor) : 0;

        if ($verdict === 'adecuado') {
            $recommendations[] = sprintf(
                'Acople correcto para %s en suelo %s: trabaja a %s km/h con %s m de ancho y %s cm de profundidad.',
                mb_strtolower(machine_category_singular((string) ($tractor['category'] ?? 'tractor'))),
                $soil,
                num($speed, 1),
                num($widthM, 2),
                num($depthCm, 1)
            );
        }
        if ($maxWidth > 0 && $maxWidth < $widthM) {
            $recommendations[] = sprintf('Con este tractor, el ancho máximo sostenible en las mismas condiciones es de %.2f m.', $maxWidth);
        }
        if ($ballastKg > 20) {
            $recommendations[] = sprintf('Coloca alrededor de %s kg de lastre en el eje delantero para conservar dirección y estabilidad.', num($ballastKg));
        }
        if ($ptoHp > 0 && ($implement['category'] ?? '') === 'forraje') {
            $recommendations[] = sprintf('Este tipo de equipo suele tomar potencia de la TDF (%s HP disponibles en el tractor). Verifica el régimen de 540/1000 rpm.', num($ptoHp));
        }
        $recommendations[] = sprintf(
            'Rendimiento estimado: %.2f ha/h y %.1f h por hectárea, con un consumo aproximado de %.1f L/ha (%.0f $/ha a %.2f $/L).',
            $haPerHour,
            $hoursPerHa,
            $fuelPerHa,
            $fuelCostPerHa,
            $dieselPrice
        );

        return [
            'draft_kgf' => round($draftKgf, 0),
            'power_hp' => round($powerHp, 1),
            'power_kw' => round($powerKw, 1),
            'load_pct' => round($loadPct * 100, 0),
            'verdict' => $verdict,
            'traction_efficiency' => $tractionEfficiency,
            'field_capacity_ha_h' => round($haPerHour, 2),
            'hours_per_ha' => round($hoursPerHa, 2),
            'fuel_l_h' => round($fuelPerHour, 1),
            'fuel_l_ha' => round($fuelPerHa, 1),
            'fuel_cost_ha' => round($fuelCostPerHa, 0),
            'ballast_kg' => round($ballastKg, 0),
            'max_width_m' => round($maxWidth, 2),
            'lift_use_pct' => round($liftUse * 100, 0),
            'warnings' => $warnings,
            'recommendations' => $recommendations,
            'factors' => [
                'k' => $k,
                'soil' => $soilFactor,
                'moisture' => $moistureFactor,
                'slope' => $slopeFactor,
                'speed_kmh' => $speed,
                'depth_cm' => $depthCm,
                'width_m' => $widthM,
            ],
        ];
    }

    public static function maxWidthForPower(float $hp, float $k, float $depthCm, float $speedKmh, float $soilFactor): float
    {
        if ($hp <= 0 || $k <= 0 || $depthCm <= 0) {
            return 0.0;
        }
        $usableHp = $hp * 0.85;
        $speedMs = $speedKmh / 3.6;
        // Despeje de P = F·v/(1000·η) con F = k·w·d·9.80665
        $draftN = ($usableHp * 0.7457 * 1000 * 0.78) / max(0.1, $speedMs);
        $draftKgf = $draftN / 9.80665;
        $widthDm = $draftKgf / max(0.1, ($k * ($depthCm / 10) * $soilFactor));
        return max(0, $widthDm / 10);
    }

    /**
     * Costo por hora de un tractor o implemento.
     *
     * @param array<string, mixed> $data claves: price_mxn, hours_year, years_life, fuel_l_h, diesel_price,
     *                                   maintenance_pct, labor_hour, insurance_pct, interest_pct, utilization_pct
     * @return array<string, float>
     */
    public static function costPerHour(array $data): array
    {
        $price = max(0.0, (float) ($data['price_mxn'] ?? 0));
        $hoursYear = max(1.0, (float) ($data['hours_year'] ?? 600));
        $years = max(1.0, (float) ($data['years_life'] ?? 10));
        $salvagePct = max(0.0, min(50.0, (float) ($data['salvage_pct'] ?? 20)));
        $dieselPrice = max(0.0, (float) ($data['diesel_price'] ?? 27.0));
        $fuelLh = max(0.0, (float) ($data['fuel_l_h'] ?? 8.0));
        $maintenancePct = max(0.0, (float) ($data['maintenance_pct'] ?? 6));
        $laborHour = max(0.0, (float) ($data['labor_hour'] ?? 350));
        $insurancePct = max(0.0, (float) ($data['insurance_pct'] ?? 1.5));
        $interestPct = max(0.0, (float) ($data['interest_pct'] ?? 12));
        $utilization = max(1.0, min(100.0, (float) ($data['utilization_pct'] ?? 85)));

        $salvage = $price * ($salvagePct / 100);
        $depreciationHour = ($price - $salvage) / ($hoursYear * $years);
        $interestHour = (($price + $salvage) / 2) * ($interestPct / 100) / $hoursYear;
        $insuranceHour = $price * ($insurancePct / 100) / $hoursYear;
        $maintenanceHour = $price * ($maintenancePct / 100) / $hoursYear;
        $fuelHour = $fuelLh * $dieselPrice;
        $laborEffective = $laborHour / ($utilization / 100);
        $totalHour = $depreciationHour + $interestHour + $insuranceHour + $maintenanceHour + $fuelHour + $laborEffective;

        return [
            'depreciation_hour' => round($depreciationHour, 2),
            'interest_hour' => round($interestHour, 2),
            'insurance_hour' => round($insuranceHour, 2),
            'maintenance_hour' => round($maintenanceHour, 2),
            'fuel_hour' => round($fuelHour, 2),
            'labor_hour' => round($laborEffective, 2),
            'total_hour' => round($totalHour, 2),
            'hours_year' => $hoursYear,
        ];
    }

    /**
     * Secuencia de implementos sugerida según sistema de labranza.
     *
     * @param array<int, array<string, mixed>> $implements
     * @return array<int, array{implement: array<string, mixed>, width_m: float, passes: int, note: string}>
     */
    public static function recommendSequence(string $system, int $hp, array $implements, float $areaHa = 10): array
    {
        $categories = match ($system) {
            'cero' => ['siembra', 'proteccion', 'fertilizacion'],
            'minima' => ['labranza', 'siembra', 'proteccion'],
            'convencional' => ['labranza', 'labranza', 'siembra', 'proteccion'],
            default => ['labranza', 'siembra', 'proteccion'],
        };

        $byCategory = [];
        foreach ($implements as $implement) {
            $byCategory[(string) $implement['category']][] = $implement;
        }

        $sequence = [];
        $used = [];
        foreach ($categories as $category) {
            $candidates = $byCategory[$category] ?? [];
            foreach ($candidates as $candidate) {
                $min = (int) ($candidate['power_min_hp'] ?? 0);
                $max = (int) ($candidate['power_max_hp'] ?? 0);
                if ($min > $hp || ($max > 0 && $max < $hp)) {
                    continue;
                }
                $key = (string) $candidate['slug'];
                if (isset($used[$key])) {
                    continue;
                }
                $used[$key] = true;
                $depth = (float) ($candidate['working_depth_cm'] ?? 20);
                $note = match ($category) {
                    'labranza' => 'Primera pasada: rompe la capa compactada y entierra residuos.',
                    'siembra' => 'Coloca la semilla a profundidad uniforme; calibra dosis y velocidad.',
                    'proteccion' => 'Aplica protección con boquillas y presión verificadas.',
                    'fertilizacion' => 'Dosifica con base en el análisis de suelo, no en la costumbre.',
                    default => 'Verifica regulación y acople antes de entrar al lote.',
                };
                $sequence[] = [
                    'implement' => $candidate,
                    'width_m' => (float) ($candidate['working_width_m'] ?? 0),
                    'passes' => $category === 'labranza' ? 1 : 1,
                    'note' => $note,
                    'ha_per_hour' => (float) ($candidate['working_width_m'] ?? 0) > 0
                        ? round(((float) $candidate['working_width_m'] * 6.5 * 0.75) / 10, 2)
                        : 0,
                    'depth_cm' => $depth,
                ];
                break;
            }
        }

        return $sequence;
    }

    /**
     * Estimación de costo total por hectárea de un ciclo de labranza.
     *
     * @param array<int, array{ha_per_hour: float, cost_hour: float}> $passes
     */
    public static function costPerHa(array $passes, float $areaHa): float
    {
        $total = 0.0;
        foreach ($passes as $pass) {
            $rate = (float) ($pass['ha_per_hour'] ?? 0);
            if ($rate <= 0) {
                continue;
            }
            $total += ((float) $pass['cost_hour']) / $rate;
        }
        return round($total, 0);
    }
}
