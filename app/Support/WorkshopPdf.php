<?php

declare(strict_types=1);

namespace App\Support;

final class WorkshopPdf
{
    /**
     * @param array<string, mixed> $case
     * @param array<int, array<string, mixed>> $images
     */
    public function __construct(
        private readonly array $case,
        private readonly array $images = [],
    ) {
    }

    public function render(): string
    {
        $case = $this->case;
        $pdf = new SimplePdf('Caso de taller · ' . $case['folio']);

        $pdf->title((string) $case['title']);
        $pdf->small(sprintf(
            'Caso %s · %s · %s · %s',
            $case['folio'],
            machine_category_singular((string) $case['machine_category']),
            $case['machine_label'] ?: 'Modelo no especificado',
            $case['state_name'] ?? 'México'
        ));
        $pdf->space(4);
        $pdf->small('Autor: ' . $case['author_name'] . ($case['author_workshop'] ? ' · ' . $case['author_workshop'] : ''));
        $pdf->space(8);

        $pdf->heading('Resumen');
        $pdf->paragraph((string) ($case['summary'] ?: excerpt((string) $case['symptom'], 200)));
        $pdf->space(4);

        $pdf->heading('Ficha del caso');
        $pdf->label('Resultado');
        $pdf->paragraph(outcome_label((string) $case['outcome']) . ' · dificultad ' . difficulty_short((int) $case['difficulty']), 10, 13);
        if ($case['time_hours'] !== null) {
            $pdf->label('Tiempo de reparación');
            $pdf->paragraph(hours_label($case['time_hours']), 10, 13);
        }
        if ($case['cost_mxn'] !== null) {
            $pdf->label('Costo de refacciones');
            $pdf->paragraph(compact_money($case['cost_mxn']) . ' MXN', 10, 13);
        }
        if ((string) $case['parts'] !== '') {
            $pdf->label('Refacciones');
            $pdf->paragraph((string) $case['parts'], 10, 13);
        }

        $sections = [
            'Síntoma' => (string) $case['symptom'],
            'Diagnóstico' => (string) $case['diagnosis'],
            'Reparación' => (string) $case['repair'],
            'Seguridad' => (string) $case['safety'],
            'Consejos' => (string) $case['tips'],
        ];

        foreach ($sections as $title => $body) {
            if (trim($body) === '') {
                continue;
            }
            $pdf->addPage();
            $pdf->heading($title);
            foreach (preg_split("/\n{2,}/", $body) ?: [] as $paragraph) {
                $pdf->paragraph(trim($paragraph));
                $pdf->space(4);
            }
        }

        if ($this->images !== []) {
            $pdf->addPage();
            $pdf->heading('Imágenes del caso');
            foreach (array_slice($this->images, 0, 4) as $image) {
                $path = BASE_PATH . '/public' . (string) $image['url'];
                if (is_file($path)) {
                    try {
                        $pdf->image($path, (string) ($image['caption'] ?? ''));
                    } catch (\Throwable) {
                        // Una imagen ilegible no debe impedir la descarga del caso.
                    }
                }
            }
        }

        $pdf->space(6);
        $pdf->small('Documento generado por AgroMáquina MX. La reparación descrita corresponde a un caso particular; verifica procedimientos, torques y refacciones con el manual del fabricante.');

        return $pdf->output();
    }
}
