<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Ficha técnica imprimible de una máquina (una o dos páginas).
 */
final class MachineSheet
{
    /**
     * @param array<string, mixed> $machine
     * @param array<int, array{implement: array<string, mixed>, result: array<string, mixed>}>|null $compat
     */
    public function __construct(
        private readonly array $machine,
        private readonly ?array $compat = null,
    ) {
    }

    public function render(): string
    {
        $machine = $this->machine;
        $pdf = new SimplePdf('Ficha técnica · ' . $machine['brand_name'] . ' ' . $machine['model']);

        $pdf->title($machine['brand_name'] . ' ' . $machine['model']);
        $pdf->small(machine_category_label((string) $machine['category']) . ' · ' . years_label(
            $machine['year_from'] !== null ? (int) $machine['year_from'] : null,
            $machine['year_to'] !== null ? (int) $machine['year_to'] : null
        ) . ' · Ficha generada el ' . date('d/m/Y'));
        $pdf->space(6);

        if ((string) $machine['summary'] !== '') {
            $pdf->paragraph((string) $machine['summary']);
            $pdf->space(4);
        }

        $pdf->heading('Especificaciones principales');
        $rows = [
            'Potencia nominal' => hp_label($machine['power_hp']),
            'Potencia a la TDF' => hp_label($machine['pto_hp']),
            'Motor' => (string) $machine['engine'],
            'Cilindrada' => $machine['displacement_l'] !== null ? num($machine['displacement_l'], 2) . ' L' : '—',
            'Cilindros' => $machine['cylinders'] !== null ? (string) $machine['cylinders'] : '—',
            'Aspiración' => $machine['aspiration'] !== '' ? ucfirst(str_replace('_', ' ', (string) $machine['aspiration'])) : '—',
            'Transmisión' => (string) $machine['transmission'] . ($machine['gears'] ? ' (' . (int) $machine['gears'] . ' velocidades)' : ''),
            'Tracción' => (string) $machine['traction'],
            'TDF' => ((int) ($machine['pto_rpm'] ?? 540)) . ' rpm',
            'Caudal hidráulico' => $machine['hydraulic_flow_lpm'] !== null ? num($machine['hydraulic_flow_lpm'], 1) . ' L/min' : '—',
            'Capacidad de levante' => $machine['lift_capacity_kg'] !== null ? num($machine['lift_capacity_kg']) . ' kg' : '—',
            'Peso' => $machine['weight_kg'] !== null ? num($machine['weight_kg']) . ' kg' : '—',
            'Tanque de combustible' => $machine['fuel_tank_l'] !== null ? num($machine['fuel_tank_l'], 1) . ' L' : '—',
            'Radio de giro' => $machine['turning_radius_m'] !== null ? num($machine['turning_radius_m'], 2) . ' m' : '—',
            'Precio de referencia' => price_range($machine['price_min_mxn'], $machine['price_max_mxn']) . ' MXN',
            'Disponibilidad' => (string) $machine['availability'],
        ];

        foreach ($rows as $label => $value) {
            if ($value === '' || $value === '—' || $value === ' MXN') {
                if ($label !== 'Precio de referencia') {
                    continue;
                }
            }
            $pdf->label($label);
            $pdf->paragraph((string) $value, 10.5, 13);
        }

        if (!empty($machine['features']) && is_array($machine['features'])) {
            $pdf->heading('Equipamiento y características');
            foreach ($machine['features'] as $feature) {
                $pdf->paragraph('· ' . $feature, 10, 13);
            }
        }

        if ((string) ($machine['maintenance_tip'] ?? '') !== '') {
            $pdf->heading('Nota de mantenimiento');
            $pdf->paragraph((string) $machine['maintenance_tip']);
        }

        if ($this->compat !== null && $this->compat !== []) {
            $pdf->addPage();
            $pdf->heading('Compatibilidad orientativa con implementos');
            $pdf->small('Cálculo con suelo franco, humedad óptima y velocidad de 6.5 a 8 km/h. Verifica con el manual del fabricante.');
            $pdf->space(6);
            foreach ($this->compat as $item) {
                $implement = $item['implement'];
                $result = $item['result'];
                $pdf->label((string) $implement['name']);
                $pdf->paragraph(sprintf(
                    'Potencia requerida %s HP (%s %% de carga). Rendimiento %.2f ha/h. Consumo estimado %.1f L/ha. Veredicto: %s.',
                    num($result['power_hp'], 1),
                    num($result['load_pct']),
                    $result['field_capacity_ha_h'],
                    $result['fuel_l_ha'],
                    match ($result['verdict']) {
                        'adecuado' => 'acople adecuado',
                        'al_limite' => 'al límite de potencia',
                        'insuficiente' => 'tractor insuficiente',
                        'sobredimensionado' => 'tractor sobrado',
                        default => 'sin datos',
                    }
                ), 10, 13);
                $pdf->space(4);
            }
        }

        $pdf->space(8);
        $pdf->small('Documento informativo. AgroMáquina MX no vende maquinaria ni refacciones. Los precios son de referencia y cambian por región, tipo de cambio y disponibilidad.');

        return $pdf->output();
    }
}
