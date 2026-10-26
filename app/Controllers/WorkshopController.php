<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CatalogRepository;
use App\Repositories\InteractionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkshopRepository;
use App\Support\WorkshopPdf;

final class WorkshopController extends BaseController
{
    public function index(Request $request): Response
    {
        $workshop = new WorkshopRepository();
        $users = new UserRepository();
        $filters = [
            'q' => $request->string('q'),
            'category' => $request->string('category'),
            'outcome' => $request->string('outcome'),
            'state' => $request->string('state'),
            'sort' => $request->string('sort', 'recientes'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = (int) config('app.pagination.workshop', 9);
        $result = $workshop->published($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        return $this->view('workshop.index', [
            'title' => 'Casos de taller · reparaciones de maquinaria agrícola documentadas | AgroMáquina MX',
            'metaDescription' => 'Reparaciones reales de tractores, cosechadoras e implementos: síntoma, diagnóstico, refacciones, costo y resultado, contados por técnicos y talleres de México.',
            'cases' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'categoryCounts' => $workshop->categoryCounts(),
            'contributors' => $users->topContributors(5),
            'states' => state_options(),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $workshop = new WorkshopRepository();
        $interactions = new InteractionRepository();
        $case = $workshop->bySlug($slug);
        if ($case === null) {
            $this->notFound('No encontramos ese caso de taller o aún no está publicado.');
        }

        \App\Core\Database::execute('update workshop_cases set views = views + 1 where id = $1', [(int) $case['id']]);

        $user = Auth::user();

        return $this->view('workshop.show', [
            'title' => $case['title'] . ' · caso de taller | AgroMáquina MX',
            'metaDescription' => mb_substr((string) ($case['summary'] ?: $case['symptom']), 0, 300),
            'case' => $case,
            'images' => $workshop->images((int) $case['id']),
            'comments' => $workshop->comments((int) $case['id']),
            'hasReacted' => $user !== null && $workshop->hasReacted((int) $case['id'], (int) $user['id']),
            'isBookmarked' => $interactions->isBookmarked('taller', (int) $case['id'], $user['id'] ?? null),
        ]);
    }

    public function create(Request $request): Response
    {
        $user = Auth::requireLogin();
        $catalog = new CatalogRepository();
        $tractors = $catalog->machines(['sort' => 'potencia_desc'], 1, 40)['items'];

        return $this->view('workshop.form', [
            'title' => 'Documentar una reparación · nuevo caso de taller | AgroMáquina MX',
            'metaDescription' => 'Comparte una reparación de maquinaria agrícola: síntoma, diagnóstico, refacciones, costo y resultado.',
            'case' => null,
            'images' => [],
            'machinesList' => $tractors,
            'states' => state_options(),
            'action' => url('/taller'),
        ]);
    }

    public function store(Request $request): Response
    {
        $user = Auth::requireLogin();
        $data = $this->payload($request);
        $validator = $this->validator($data, $this->caseRules(), [
            'title' => 'Título del caso',
            'symptom' => 'Síntoma',
            'diagnosis' => 'Diagnóstico',
            'repair' => 'Reparación',
        ]);

        if (!$validator->validate($this->caseRules())) {
            $this->withErrors($validator->errors());
            $this->flashOld($data);
            return $this->back('/taller/nuevo');
        }

        $workshop = new WorkshopRepository();
        $caseId = $workshop->create((int) $user['id'], $data);
        $this->saveWorkshopImages($caseId, $request);
        $this->audit('taller.crear', 'taller', $caseId, ['title' => $data['title']]);

        Flash::success('Tu caso se guardó como borrador. Revísalo y envíalo a revisión cuando esté completo.');
        return $this->redirect('/taller/' . $workshop->byId($caseId)['slug'] . '/editar');
    }

    public function edit(Request $request, string $slug): Response
    {
        $user = Auth::requireLogin();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId($this->caseId($slug));
        if ($case === null || (int) $case['author_id'] !== (int) $user['id']) {
            $this->forbidden('Solo el autor puede editar este caso.');
        }

        $catalog = new CatalogRepository();

        return $this->view('workshop.form', [
            'title' => 'Editar caso de taller · ' . $case['title'],
            'metaDescription' => 'Edita tu caso de taller.',
            'case' => $case,
            'images' => $workshop->images((int) $case['id']),
            'machinesList' => $catalog->machines(['sort' => 'potencia_desc'], 1, 40)['items'],
            'states' => state_options(),
            'action' => url('/taller/' . $case['slug']),
        ]);
    }

    public function update(Request $request, string $slug): Response
    {
        $user = Auth::requireLogin();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId($this->caseId($slug));
        if ($case === null || (int) $case['author_id'] !== (int) $user['id']) {
            $this->forbidden('Solo el autor puede editar este caso.');
        }

        $data = $this->payload($request);
        $validator = $this->validator($data, $this->caseRules(), [
            'title' => 'Título', 'symptom' => 'Síntoma', 'diagnosis' => 'Diagnóstico', 'repair' => 'Reparación',
        ]);

        if (!$validator->validate($this->caseRules())) {
            $this->withErrors($validator->errors());
            $this->flashOld($data);
            return $this->back('/taller/' . $case['slug'] . '/editar');
        }

        $workshop->update((int) $case['id'], (int) $user['id'], $data);
        $this->saveWorkshopImages((int) $case['id'], $request);
        $this->audit('taller.editar', 'taller', (int) $case['id']);

        Flash::success('Cambios guardados.');
        return $this->redirect('/taller/' . $case['slug'] . '/editar');
    }

    public function submit(Request $request, string $slug): Response
    {
        $user = Auth::requireVerified();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId($this->caseId($slug));
        if ($case === null || (int) $case['author_id'] !== (int) $user['id']) {
            $this->forbidden();
        }

        $workshop->submit((int) $case['id'], (int) $user['id']);
        $this->audit('taller.enviar', 'taller', (int) $case['id']);
        Flash::success('Caso enviado a revisión editorial. Te avisaremos por correo cuando se publique.');
        return $this->redirect('/mi-panel');
    }

    public function destroyImage(Request $request, string $slug, string $imageId): Response
    {
        $user = Auth::requireLogin();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId($this->caseId($slug));
        if ($case === null || (int) $case['author_id'] !== (int) $user['id']) {
            $this->forbidden();
        }
        $workshop->deleteImage((int) $imageId, (int) $case['id'], (int) $user['id']);
        Flash::success('Imagen eliminada.');
        return $this->redirect('/taller/' . $case['slug'] . '/editar');
    }

    public function comment(Request $request, string $slug): Response
    {
        $user = Auth::requireVerified();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId($this->caseId($slug));
        if ($case === null) {
            $this->notFound();
        }

        $body = trim((string) $request->post('body', ''));
        if (mb_strlen($body) < 10 || mb_strlen($body) > 2000) {
            Flash::error('El comentario debe tener entre 10 y 2000 caracteres.');
            return $this->redirect('/taller/' . $case['slug'] . '#comentarios');
        }

        $workshop->addComment((int) $case['id'], (int) $user['id'], $body);
        $this->audit('taller.comentar', 'taller', (int) $case['id']);
        Flash::success('Comentario publicado.');
        return $this->redirect('/taller/' . $case['slug'] . '#comentarios');
    }

    public function react(Request $request, string $slug): Response
    {
        $user = Auth::requireLogin();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId($this->caseId($slug));
        if ($case === null) {
            $this->notFound();
        }

        $active = $workshop->toggleReaction((int) $case['id'], (int) $user['id']);
        Flash::info($active ? 'Marcaste este caso como útil.' : 'Quitaste tu marca de utilidad.');
        return $this->redirect('/taller/' . $case['slug'] . '#reacciones');
    }

    public function pdf(Request $request, string $slug): Response
    {
        $workshop = new WorkshopRepository();
        $case = $workshop->bySlug($slug);
        if ($case === null) {
            $this->notFound();
        }

        $pdf = new WorkshopPdf($case, $workshop->images((int) $case['id']));
        $binary = $pdf->render();

        return Response::html('', 200)
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="caso-' . $case['slug'] . '.pdf"')
            ->withHeader('Content-Length', (string) strlen($binary))
            ->withBody($binary);
    }

    private function caseId(string $slug): int
    {
        $id = \App\Core\Database::value('select id from workshop_cases where slug = $1', [$slug]);
        return $id !== null ? (int) $id : 0;
    }

    /** @return array<string, string> */
    private function caseRules(): array
    {
        return [
            'title' => 'required|min:10|max:140',
            'symptom' => 'required|min:20',
            'diagnosis' => 'required|min:20',
            'repair' => 'required|min:20',
        ];
    }

    /** @return array<string, mixed> */
    private function payload(Request $request): array
    {
        $machineId = $request->int('machine_id');
        return [
            'title' => trim((string) $request->post('title', '')),
            'summary' => trim((string) $request->post('summary', '')),
            'machine_category' => (string) $request->post('machine_category', 'tractor'),
            'machine_label' => trim((string) $request->post('machine_label', '')),
            'machine_id' => $machineId > 0 ? $machineId : null,
            'state_code' => (string) $request->post('state_code', ''),
            'symptom' => trim((string) $request->post('symptom', '')),
            'diagnosis' => trim((string) $request->post('diagnosis', '')),
            'repair' => trim((string) $request->post('repair', '')),
            'parts' => trim((string) $request->post('parts', '')),
            'cost_mxn' => $request->post('cost_mxn') !== null && $request->post('cost_mxn') !== '' ? (float) $request->post('cost_mxn') : null,
            'time_hours' => $request->post('time_hours') !== null && $request->post('time_hours') !== '' ? (float) $request->post('time_hours') : null,
            'difficulty' => max(1, min(5, $request->int('difficulty', 3))),
            'outcome' => (string) $request->post('outcome', 'resuelto'),
            'safety' => trim((string) $request->post('safety', '')),
            'tips' => trim((string) $request->post('tips', '')),
        ];
    }
}
