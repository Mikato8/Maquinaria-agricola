<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\InteractionRepository;

final class InteractionController extends BaseController
{
    public function bookmark(Request $request, string $type, string $id): Response
    {
        $user = Auth::requireLogin();
        $repo = new InteractionRepository();
        $active = $repo->toggleBookmark($type, (int) $id, (int) $user['id']);

        if ($active) {
            Flash::success('Guardado en tu panel.');
        } else {
            Flash::info('Quitado de tus favoritos.');
        }

        return $this->back('/mi-panel');
    }
}
