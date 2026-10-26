<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Upload;
use App\Core\Validator;
use App\Repositories\InteractionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkshopRepository;

final class ProfileController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        $user = Auth::requireLogin();
        $workshop = new WorkshopRepository();
        $interactions = new InteractionRepository();
        $repo = new UserRepository();
        $stats = $interactions->bookmarkStats((int) $user['id']);

        return $this->view('profile.dashboard', [
            'title' => 'Mi panel · AgroMáquina MX',
            'metaDescription' => 'Tus casos de taller, favoritos y estado de verificación.',
            'cases' => $workshop->userCases((int) $user['id']),
            'bookmarks' => $stats,
            'bookmarkList' => $this->bookmarkItems((int) $user['id']),
            'profile' => $repo->byIdWithStats((int) $user['id']),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function show(Request $request): Response
    {
        $user = Auth::requireLogin();
        $repo = new UserRepository();

        return $this->view('profile.show', [
            'title' => 'Mi perfil · AgroMáquina MX',
            'metaDescription' => 'Datos de tu cuenta, verificación y seguridad.',
            'profile' => $repo->byIdWithStats((int) $user['id']),
            'states' => state_options(),
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function update(Request $request): Response
    {
        $user = Auth::requireLogin();
        $data = [
            'name' => trim($request->string('name')),
            'headline' => trim($request->string('headline')),
            'workshop_name' => trim($request->string('workshop_name')),
            'city' => trim($request->string('city')),
            'state_code' => $request->string('state_code'),
            'phone' => trim($request->string('phone')),
            'bio' => trim($request->string('bio')),
        ];
        $validator = $this->validator($data, ['name' => 'nombre', 'state_code' => 'estado']);
        $validator->validate([
            'name' => 'required|min:3|max:120',
            'headline' => 'nullable|max:120',
            'workshop_name' => 'nullable|max:120',
            'city' => 'nullable|max:80',
            'state_code' => 'required|min:2|max:5',
            'phone' => 'nullable|max:30',
            'bio' => 'nullable|max:1200',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors());
            $this->flashOld($data);
            return $this->redirect('/perfil');
        }

        (new UserRepository())->updateProfile((int) $user['id'], $data);
        $this->audit('perfil.actualizado', 'usuario', (int) $user['id']);
        Flash::success('Perfil actualizado.');
        return $this->redirect('/perfil');
    }

    public function avatar(Request $request): Response
    {
        $user = Auth::requireLogin();
        $file = $request->file('avatar');
        if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            Flash::error('Selecciona una imagen.');
            return $this->redirect('/perfil');
        }

        try {
            $url = Upload::image($file, 'avatares', (string) config('app.uploads.avatars_path'), (string) config('app.uploads.avatars_url'), 600);
            (new UserRepository())->setAvatar((int) $user['id'], $url['url']);
            $this->audit('perfil.avatar', 'usuario', (int) $user['id']);
            Flash::success('Foto actualizada.');
        } catch (\Throwable $e) {
            Flash::error('No se pudo subir la imagen: ' . $e->getMessage());
        }

        return $this->redirect('/perfil');
    }

    public function removeAvatar(Request $request): Response
    {
        $user = Auth::requireLogin();
        (new UserRepository())->setAvatar((int) $user['id'], null);
        Flash::success('Foto eliminada.');
        return $this->redirect('/perfil');
    }

    public function password(Request $request): Response
    {
        $user = Auth::requireLogin();
        $current = $request->string('current_password');
        $new = $request->string('password');

        if (!password_verify($current, (string) $user['password_hash'])) {
            Flash::error('La contraseña actual no coincide.');
            return $this->redirect('/perfil#seguridad');
        }
        if (mb_strlen($new) < 10) {
            Flash::error('La nueva contraseña debe tener al menos 10 caracteres.');
            return $this->redirect('/perfil#seguridad');
        }
        if ($new !== $request->string('password_confirmation')) {
            Flash::error('La confirmación no coincide.');
            return $this->redirect('/perfil#seguridad');
        }

        (new UserRepository())->updatePassword((int) $user['id'], password_hash($new, PASSWORD_DEFAULT));
        $this->audit('perfil.password', 'usuario', (int) $user['id']);
        Flash::success('Contraseña actualizada.');
        return $this->redirect('/perfil#seguridad');
    }

    public function deleteAccount(Request $request): Response
    {
        $user = Auth::requireLogin();
        $confirmation = (string) $request->post('confirm', '');
        if (!password_verify($confirmation, (string) $user['password_hash'])) {
            Flash::error('Escribe tu contraseña para confirmar la eliminación.');
            return $this->redirect('/perfil#eliminar');
        }

        (new UserRepository())->deleteAccount((int) $user['id']);
        Auth::logout();
        Flash::info('Tu cuenta se eliminó y tus datos personales se anonimizaron. Los casos publicados quedan sin datos de autor.');
        return $this->redirect('/');
    }

    public function publicProfile(Request $request, string $id): Response
    {
        $userId = ctype_digit($id) ? (int) $id : 0;
        $repo = new UserRepository();
        $profile = $repo->byIdWithStats($userId);
        if ($profile === null || $profile['deleted_at'] !== null) {
            $this->notFound('No encontramos ese perfil.');
        }
        $workshop = new WorkshopRepository();

        return $this->view('profile.public', [
            'title' => $profile['name'] . ' · perfil de taller | AgroMáquina MX',
            'metaDescription' => excerpt($profile['bio'] ?: $profile['headline'], 200),
            'profile' => $profile,
            'cases' => array_values(array_filter($workshop->userCases($userId), static fn (array $case): bool => $case['status'] === 'publicado')),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function bookmarkItems(int $userId): array
    {
        $rows = (new InteractionRepository())->bookmarks($userId, 20);
        $items = [];
        foreach ($rows as $row) {
            $type = (string) $row['target_type'];
            $id = (int) $row['target_id'];
            $table = match ($type) {
                'taller' => 'workshop_cases',
                'maquina' => 'machines',
                'falla' => 'faults',
                'guia' => 'guides',
                'implemento' => 'implements',
                'recurso' => 'tech_resources',
                default => null,
            };
            if ($table === null) {
                continue;
            }
            $item = \App\Core\Database::first('select * from ' . $table . ' where id = $1', [$id]);
            if ($item === null) {
                continue;
            }
            $base = match ($type) {
                'taller' => '/taller/',
                'maquina' => '/maquinas/',
                'falla' => '/fallas/',
                'guia' => '/guias/',
                'implemento' => '/implementos/',
                'recurso' => '/tecnologia/',
                default => '/',
            };
            $items[] = [
                'type' => $type,
                'title' => $item['name'] ?? $item['title'] ?? 'Elemento guardado',
                'url' => url($base . ($item['slug'] ?? '')),
                'date' => $row['created_at'],
            ];
        }
        return $items;
    }
}
