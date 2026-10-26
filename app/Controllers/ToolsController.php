<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\CatalogRepository;
use App\Services\MachineryMath;

final class ToolsController extends BaseController
{
    public function compatibility(Request $request): Response
    {
        $catalog = new CatalogRepository();
        $tractors = $catalog->machines(['category' => 'tractor', 'sort' => 'potencia_desc'], 1, 60)['items'];
        $implements = $catalog->implements(['sort' => 'relevancia'], 1, 48)['items'];

        $tractor = null;
        $implement = null;

        $tractorId = $request->int('tractor_id');
        if ($tractorId > 0) {
            $found = $catalog->machineByIds([$tractorId]);
            $tractor = $found[0] ?? null;
        }
        $implementId = $request->int('implemento_id');
        if ($implementId > 0) {
            $found = $catalog->implementsByIds([$implementId]);
            $implement = $found[0] ?? null;
        }

        $input = [
            'power_hp' => $request->query('tractor_power_hp') !== null ? (float) $request->query('tractor_power_hp') : (float) ($tractor['power_hp'] ?? 0),
            'pto_hp' => (float) ($tractor['pto_hp'] ?? 0),
            'weight_kg' => $request->query('tractor_weight_kg') !== null ? (float) $request->query('tractor_weight_kg') : (float) ($tractor['weight_kg'] ?? 0),
            'lift_capacity_kg' => $request->query('tractor_lift_kg') !== null ? (float) $request->query('tractor_lift_kg') : (float) ($tractor['lift_capacity_kg'] ?? 0),
            'hydraulic_flow_lpm' => $request->query('tractor_flow_lpm') !== null ? (float) $request->query('tractor_flow_lpm') : (float) ($tractor['hydraulic_flow_lpm'] ?? 0),
            'traction' => (string) ($request->query('tractor_traction') ?: ($tractor['traction'] ?? '4WD')),
            'category' => 'tractor',
        ];

        $implementInput = [
            'category' => (string) ($request->query('implement_category') ?: ($implement['category'] ?? 'labranza')),
            'name' => (string) ($implement['name'] ?? 'Implemento personalizado'),
            'working_width_m' => $request->query('implement_width_m') !== null ? (float) $request->query('implement_width_m') : (float) ($implement['working_width_m'] ?? 0),
            'working_depth_cm' => $request->query('implement_depth_cm') !== null ? (float) $request->query('implement_depth_cm') : (float) ($implement['working_depth_cm'] ?? 0),
            'weight_kg' => $request->query('implement_weight_kg') !== null ? (float) $request->query('implement_weight_kg') : (float) ($implement['weight_kg'] ?? 0),
            'hitch' => (string) ($implement['hitch'] ?? '3 puntos'),
            'required_flow_lpm' => $request->query('implement_flow_lpm') !== null ? (float) $request->query('implement_flow_lpm') : (float) ($implement['required_flow_lpm'] ?? 0),
            'drive_required' => (string) ($implement['drive_required'] ?? 'cualquiera'),
            'draft_factor' => (float) ($implement['draft_factor'] ?? 0),
        ];

        $options = [
            'soil' => (string) ($request->query('soil') ?: 'franco'),
            'moisture' => (string) ($request->query('moisture') ?: 'optimo'),
            'slope_pct' => (float) ($request->query('slope_pct') ?? 0),
            'speed_kmh' => (float) ($request->query('speed_kmh') ?? 6.5),
            'diesel_price' => (float) ($request->query('diesel_price') ?? 27.0),
        ];

        $result = null;
        if ($input['power_hp'] > 0 && $implementInput['working_width_m'] > 0) {
            $result = MachineryMath::compatibility($input, $implementInput, $options);
        }

        // Si el usuario cambia manualmente los campos, el select debe reflejarlo.
        return $this->view('tools.compatibility', [
            'title' => 'Compatibilidad tractor–implemento · calculadora de potencia y tiro | AgroMáquina MX',
            'metaDescription' => 'Calcula si tu tractor puede con el implemento: fuerza de tiro, potencia requerida, capacidad de levante, caudal hidráulico, lastre, rendimiento y consumo de diésel.',
            'tractors' => $tractors,
            'implementsList' => $implements,
            'tractor' => $tractor,
            'implement' => $implement,
            'input' => $input,
            'implementInput' => $implementInput,
            'options' => $options,
            'result' => $result,
        ]);
    }

    public function recommend(Request $request): Response
    {
        $catalog = new CatalogRepository();
        $input = [
            'area_ha' => max(0.5, (float) ($request->query('area_ha') ?? 20)),
            'crop' => (string) ($request->query('crop') ?: 'maiz'),
            'soil' => (string) ($request->query('soil') ?: 'franco'),
            'slope_pct' => max(0.0, (float) ($request->query('slope_pct') ?? 0)),
            'hp' => max(15, (int) ($request->query('hp') ?? 75)),
            'system' => (string) ($request->query('system') ?: 'minima'),
            'budget' => max(0, (int) ($request->query('budget') ?? 0)),
        ];

        $implements = $catalog->implementsForPower($input['hp'], 40);
        $sequence = MachineryMath::recommendSequence($input['system'], $input['hp'], $implements, $input['area_ha']);

        $tractorCost = MachineryMath::costPerHour([
            'price_mxn' => max(200000, $input['hp'] * 14000),
            'hours_year' => max(200, $input['area_ha'] * 22),
            'years_life' => 10,
            'fuel_l_h' => $input['hp'] * 0.20,
            'diesel_price' => 27.0,
            'maintenance_pct' => 6,
            'labor_hour' => 350,
            'utilization_pct' => 85,
        ]);

        $passes = [];
        $totalCost = 0.0;
        $totalHours = 0.0;
        foreach ($sequence as $step) {
            $width = (float) $step['width_m'];
            $speed = $step['implement']['category'] === 'labranza' ? 6.5 : 8.0;
            $haHour = $width > 0 ? ($width * $speed * 0.75) / 10 : 0.0;
            $hours = $haHour > 0 ? $input['area_ha'] / $haHour : 0.0;
            $costHa = $haHour > 0 ? $tractorCost['total_hour'] / $haHour : 0.0;
            $passes[] = [
                'step' => $step,
                'ha_hour' => round($haHour, 2),
                'hours_total' => round($hours, 1),
                'cost_ha' => round($costHa, 0),
            ];
            $totalCost += $costHa;
            $totalHours += $hours;
        }

        return $this->view('tools.recommend', [
            'title' => 'Recomendador de implementos por cultivo, suelo y potencia | AgroMáquina MX',
            'metaDescription' => 'Arma una secuencia de labranza, siembra y protección según superficie, cultivo, suelo, pendiente y potencia del tractor, con costo estimado por hectárea.',
            'input' => $input,
            'sequence' => $passes,
            'tractorCost' => $tractorCost,
            'totalCostHa' => round($totalCost, 0),
            'totalHours' => round($totalHours, 1),
            'cropLabel' => crop_label($input['crop']),
        ]);
    }

    public function costs(Request $request): Response
    {
        $input = [
            'price_mxn' => max(0, (float) ($request->query('price_mxn') ?? 900000)),
            'hours_year' => max(1, (float) ($request->query('hours_year') ?? 600)),
            'years_life' => max(1, (float) ($request->query('years_life') ?? 10)),
            'salvage_pct' => max(0, min(60, (float) ($request->query('salvage_pct') ?? 20))),
            'diesel_price' => max(0, (float) ($request->query('diesel_price') ?? 27)),
            'fuel_l_h' => max(0, (float) ($request->query('fuel_l_h') ?? 9)),
            'maintenance_pct' => max(0, (float) ($request->query('maintenance_pct') ?? 6)),
            'labor_hour' => max(0, (float) ($request->query('labor_hour') ?? 350)),
            'insurance_pct' => max(0, (float) ($request->query('insurance_pct') ?? 1.5)),
            'interest_pct' => max(0, (float) ($request->query('interest_pct') ?? 12)),
            'utilization_pct' => max(1, min(100, (float) ($request->query('utilization_pct') ?? 85))),
            'width_m' => max(0, (float) ($request->query('width_m') ?? 2.0)),
            'speed_kmh' => max(0, (float) ($request->query('speed_kmh') ?? 7)),
        ];

        $result = MachineryMath::costPerHour($input);
        $haHour = $input['width_m'] > 0 ? ($input['width_m'] * $input['speed_kmh'] * 0.75) / 10 : 0.0;
        $costHa = $haHour > 0 ? $result['total_hour'] / $haHour : 0.0;

        return $this->view('tools.costs', [
            'title' => 'Costo por hora y por hectárea de un tractor | AgroMáquina MX',
            'metaDescription' => 'Calcula el costo real de operar un tractor: depreciación, interés, seguro, combustible, mantenimiento y mano de obra, por hora y por hectárea.',
            'input' => $input,
            'result' => $result,
            'haHour' => round($haHour, 2),
            'costHa' => round($costHa, 0),
        ]);
    }
}
