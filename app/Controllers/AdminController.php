<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkshopRepository;

final class AdminController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        Auth::requireModerator();
        $admin = new AdminRepository();
        $workshop = new WorkshopRepository();
        $users = new UserRepository();

        return $this->view('admin/dashboard', [
            'title' => 'Panel editorial · AgroMáquina MX',
            'metaDescription' => 'Moderación de casos de taller, verificación de cuentas y métricas del portal.',
            'stats' => $admin->dashboardStats(),
            'pending' => $workshop->pending(8),
            'verification' => $users->pendingForVerification(6),
            'comments' => array_slice($admin->comments(6), 0, 6),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function cases(Request $request): Response
    {
        Auth::requireModerator();
        $workshop = new WorkshopRepository();
        $status = $request->string('status', 'en_revision');
        $allowed = ['en_revision', 'cambios_solicitados', 'publicado', 'rechazado', 'borrador'];
        if (!in_array($status, $allowed, true)) {
            $status = 'en_revision';
        }

        $cases = \App\Core\Database::select(
            'select c.*, u.name as author_name from workshop_cases c join users u on u.id = c.author_id
             where c.status = $1 order by c.updated_at asc limit 100',
            [$status]
        );

        $counts = [];
        foreach (\App\Core\Database::select('select status, count(*)::int as total from workshop_cases group by status') as $row) {
            $counts[(string) $row['status']] = (int) $row['total'];
        }

        return $this->view('admin/cases', [
            'title' => 'Casos de taller · panel editorial',
            'cases' => $cases,
            'status' => $status,
            'counts' => $counts,
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function review(Request $request, string $id): Response
    {
        Auth::requireModerator();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId((int) $id);
        if ($case === null) {
            $this->notFound();
        }

        return $this->view('admin/review', [
            'title' => 'Revisar caso · ' . $case['title'],
            'case' => $case,
            'images' => $workshop->images((int) $case['id']),
            'history' => $workshop->reviewHistory((int) $case['id']),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function reviewPost(Request $request, string $id): Response
    {
        $moderator = Auth::requireModerator();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId((int) $id);
        if ($case === null) {
            $this->notFound();
        }

        $decision = (string) $request->post('decision', '');
        if (!in_array($decision, ['publicar', 'cambios', 'rechazar'], true)) {
            Flash::error('Decisión no válida.');
            return $this->back('/admin/casos');
        }
        $notes = trim((string) $request->post('notes', ''));
        if ($decision !== 'publicar' && mb_strlen($notes) < 10) {
            Flash::error('Explica al autor qué debe corregir (mínimo 10 caracteres).');
            return $this->back('/admin/revision/' . $case['id']);
        }

        $workshop->decide((int) $case['id'], (int) $moderator['id'], $decision, $notes);
        $this->audit('taller.' . $decision, 'taller', (int) $case['id'], ['notas' => $notes]);
        Flash::success('Decisión registrada.');
        return $this->redirect('/admin/casos');
    }

    public function feature(Request $request, string $id): Response
    {
        Auth::requireModerator();
        $workshop = new WorkshopRepository();
        $case = $workshop->byId((int) $id);
        if ($case === null) {
            $this->notFound();
        }
        $workshop->feature((int) $case['id'], !(bool) $case['featured']);
        Flash::success('Visibilidad actualizada.');
        return $this->back('/admin/casos');
    }

    public function users(Request $request): Response
    {
        Auth::requireModerator();
        $repo = new UserRepository();
        $filters = [
            'q' => $request->string('q'),
            'status' => $request->string('status'),
            'role' => $request->string('role'),
        ];
        $page = max(1, $request->int('page', 1));
        $result = $repo->adminList($filters, $page, (int) config('app.pagination.admin', 20));

        return $this->view('admin/users', [
            'title' => 'Cuentas · panel editorial',
            'users' => $result['items'],
            'total' => $result['total'],
            'filters' => $filters,
            'counts' => $repo->counts(),
            'paginator' => new \App\Core\Paginator($result['total'], (int) config('app.pagination.admin', 20), $page),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function user(Request $request, string $id): Response
    {
        Auth::requireModerator();
        $repo = new UserRepository();
        $user = $repo->byIdWithStats((int) $id);
        if ($user === null) {
            $this->notFound();
        }
        $workshop = new WorkshopRepository();

        return $this->view('admin/user', [
            'title' => 'Cuenta · ' . $user['name'],
            'user' => $user,
            'cases' => $workshop->userCases((int) $user['id']),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function userAction(Request $request, string $id): Response
    {
        $moderator = Auth::requireModerator();
        $repo = new UserRepository();
        $user = $repo->byId((int) $id);
        if ($user === null) {
            $this->notFound();
        }

        $action = (string) $request->post('action', '');
        $isAdmin = $moderator['role'] === 'admin';

        switch ($action) {
            case 'verificar':
                $method = (string) $request->post('method', 'manual');
                if (!in_array($method, ['cedula', 'taller', 'manual', 'admin'], true)) {
                    $method = 'manual';
                }
                $repo->verify((int) $user['id'], $method, trim((string) $request->post('note', '')));
                $this->audit('usuario.verificar', 'usuario', (int) $user['id'], ['metodo' => $method]);
                Flash::success('Cuenta verificada.');
                break;
            case 'suspender':
                $repo->suspend((int) $user['id'], true);
                $this->audit('usuario.suspender', 'usuario', (int) $user['id']);
                Flash::success('Cuenta suspendida.');
                break;
            case 'reactivar':
                $repo->suspend((int) $user['id'], false);
                $this->audit('usuario.reactivar', 'usuario', (int) $user['id']);
                Flash::success('Cuenta reactivada como pendiente de verificación.');
                break;
            case 'rol':
                if (!$isAdmin) {
                    $this->forbidden('Solo un administrador puede cambiar roles.');
                }
                $role = (string) $request->post('role', 'productor');
                if (!in_array($role, ['productor', 'tecnico', 'taller', 'editor', 'admin'], true)) {
                    Flash::error('Rol no válido.');
                    break;
                }
                $repo->setRole((int) $user['id'], $role);
                $this->audit('usuario.rol', 'usuario', (int) $user['id'], ['rol' => $role]);
                Flash::success('Rol actualizado.');
                break;
            default:
                Flash::error('Acción no reconocida.');
        }

        return $this->back('/admin/usuarios/' . $user['id']);
    }

    public function comments(Request $request): Response
    {
        Auth::requireModerator();
        $admin = new AdminRepository();

        return $this->view('admin/comments', [
            'title' => 'Comentarios · panel editorial',
            'comments' => $admin->comments(80),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function commentAction(Request $request, string $id): Response
    {
        Auth::requireModerator();
        $workshop = new WorkshopRepository();
        $comment = $workshop->commentById((int) $id);
        if ($comment === null) {
            $this->notFound();
        }
        if ((string) $request->post('action') === 'ocultar') {
            $workshop->hideComment((int) $comment['id']);
            $this->audit('comentario.ocultar', 'comentario', (int) $comment['id']);
            Flash::success('Comentario oculto.');
        }
        return $this->back('/admin/comentarios');
    }

    public function inquiries(Request $request): Response
    {
        Auth::requireModerator();
        $admin = new AdminRepository();

        return $this->view('admin/inquiries', [
            'title' => 'Mensajes · panel editorial',
            'inquiries' => $admin->inquiries(false, 100),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function inquiryAction(Request $request, string $id): Response
    {
        Auth::requireModerator();
        $admin = new AdminRepository();
        $admin->markInquiry((int) $id, (string) $request->post('action') !== 'reabrir');
        Flash::success('Mensaje actualizado.');
        return $this->back('/admin/mensajes');
    }

    public function auditLog(Request $request): Response
    {
        Auth::requireModerator();
        $admin = new AdminRepository();

        return $this->view('admin/audit', [
            'title' => 'Auditoría · panel editorial',
            'entries' => $admin->audit(150),
            'robots' => 'noindex,nofollow',
        ]);
    }
}
