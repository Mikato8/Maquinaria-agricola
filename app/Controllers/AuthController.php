<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Str;
use App\Core\Upload;
use App\Repositories\UserRepository;

final class AuthController extends BaseController
{
    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/mi-panel');
        }
        return $this->view('auth/login', [
            'title' => 'Acceder · AgroMáquina MX',
            'metaDescription' => 'Inicia sesión para documentar reparaciones, guardar fichas y comentar en el taller.',
            'robots' => 'noindex,follow',
        ]);
    }

    public function login(Request $request): Response
    {
        $email = trim($request->string('email'));
        $password = $request->string('password');
        $remember = (bool) $request->post('remember');

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Escribe un correo electrónico válido.';
        }
        if ($password === '') {
            $errors['password'] = 'Escribe tu contraseña.';
        }
        if ($errors !== []) {
            $this->withErrors($errors);
            $this->flashOld(['email' => $email]);
            return $this->redirect('/acceder');
        }

        $result = Auth::attempt($email, $password, $remember);
        if (!$result['ok']) {
            $this->withErrors(['login' => $result['message']]);
            $this->flashOld(['email' => $email]);
            return $this->redirect('/acceder');
        }

        $this->audit('sesion.inicio', 'usuario', Auth::id());
        Flash::success('Bienvenido de nuevo. Tu panel está listo.');
        $intended = Auth::intendedUrl();
        return $this->redirect($intended === '/' ? '/mi-panel' : $intended);
    }

    public function showRegister(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/mi-panel');
        }
        return $this->view('auth/register', [
            'title' => 'Crear cuenta · AgroMáquina MX',
            'metaDescription' => 'Regístrate como productor, técnico o taller. Consulta libre; para publicar casos se verifica cédula o registro de taller.',
            'states' => state_options(),
            'robots' => 'noindex,follow',
        ]);
    }

    public function register(Request $request): Response
    {
        $data = [
            'name' => trim($request->string('name')),
            'email' => Str::normalizeEmail($request->string('email')),
            'role' => $request->string('role', 'productor'),
            'headline' => trim($request->string('headline')),
            'workshop_name' => trim($request->string('workshop_name')),
            'city' => trim($request->string('city')),
            'state_code' => $request->string('state_code'),
            'phone' => trim($request->string('phone')),
            'bio' => trim($request->string('bio')),
        ];

        $validator = $this->validator($data, [
            'name' => 'nombre completo',
            'email' => 'correo electrónico',
            'role' => 'tipo de cuenta',
            'state_code' => 'estado',
        ]);
        $validator->validate([
            'name' => 'required|min:3|max:120',
            'email' => 'required|email|max:180',
            'role' => 'required|in:productor,tecnico,taller',
            'headline' => 'nullable|max:120',
            'workshop_name' => 'nullable|max:120',
            'city' => 'nullable|max:80',
            'state_code' => 'required|min:2|max:5',
            'phone' => 'nullable|max:30',
            'bio' => 'nullable|max:1200',
        ]);

        $errors = $validator->errors();
        if ((new UserRepository())->emailExists($data['email'])) {
            $errors['email'] = 'Ya existe una cuenta con este correo. ¿Quieres iniciar sesión?';
        }
        if ($data['role'] === 'taller' && $data['workshop_name'] === '') {
            $errors['workshop_name'] = 'Escribe el nombre del taller o de la empresa.';
        }

        $password = $request->string('password');
        if (mb_strlen($password) < 10) {
            $errors['password'] = 'La contraseña debe tener al menos 10 caracteres.';
        }
        if ($password !== $request->string('password_confirmation')) {
            $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
        }
        if (!$request->post('terms')) {
            $errors['terms'] = 'Debes aceptar los términos y el aviso de privacidad.';
        }

        if ($errors !== []) {
            $this->withErrors($errors);
            $this->flashOld($data);
            return $this->redirect('/registro');
        }

        $autoVerify = (bool) config('app.auto_verify_users', false);
        $status = $autoVerify ? 'verificado' : 'pendiente';
        $repo = new UserRepository();
        $userId = $repo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
            'role' => $data['role'],
            'status' => $status,
            'headline' => $data['headline'],
            'workshop_name' => $data['workshop_name'],
            'city' => $data['city'],
            'state_code' => $data['state_code'],
            'phone' => $data['phone'],
            'bio' => $data['bio'],
            'verification_method' => $autoVerify ? 'admin' : 'manual',
            'email_verified' => false,
        ]);

        $proof = $request->file('proof');
        if ($proof !== null && (int) ($proof['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            try {
                $path = Upload::proof($proof, $userId);
                $repo->setProof($userId, $path);
            } catch (\Throwable $e) {
                Flash::error('No pudimos guardar tu comprobante: ' . $e->getMessage());
            }
        }

        $user = $repo->byId($userId);
        if ($user !== null) {
            Auth::login($user, false);
        }
        $this->audit('cuenta.registro', 'usuario', $userId, ['rol' => $data['role']]);

        if ($status === 'verificado') {
            Flash::success('Cuenta creada y verificada. Ya puedes documentar tu primera reparación.');
        } else {
            Flash::info('Cuenta creada. Revisaremos tu cédula o registro de taller; mientras tanto puedes guardar borradores y favoritos.');
        }

        return $this->redirect('/mi-panel');
    }

    public function logout(Request $request): Response
    {
        $this->audit('sesion.cierre', 'usuario', Auth::id());
        Auth::logout();
        Flash::info('Cerraste sesión correctamente.');
        return $this->redirect('/');
    }

    public function showForgot(Request $request): Response
    {
        return $this->view('auth/forgot', [
            'title' => 'Recuperar contraseña · AgroMáquina MX',
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function sendReset(Request $request): Response
    {
        $email = Str::normalizeEmail($request->string('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->withErrors(['email' => 'Escribe un correo válido.']);
            $this->flashOld(['email' => $email]);
            return $this->redirect('/recuperar');
        }

        $repo = new UserRepository();
        $user = $repo->byEmail($email);
        if ($user !== null) {
            $token = $repo->createPasswordReset((int) $user['id']);
            $sent = Mailer::passwordReset($user, $token);
            if (!$sent) {
                Logger::info('Enlace de recuperación generado sin correo', [
                    'email' => $email,
                    'link' => url('/recuperar/' . $token),
                ]);
            }
        }

        Flash::success('Si el correo existe, enviamos un enlace para restablecer la contraseña. Revisa también la carpeta de spam.');
        return $this->redirect('/recuperar');
    }

    public function showReset(Request $request, string $token): Response
    {
        $user = (new UserRepository())->findByResetToken($token);
        if ($user === null) {
            Flash::error('El enlace no es válido o ya venció. Solicita uno nuevo.');
            return $this->redirect('/recuperar');
        }
        return $this->view('auth/reset', [
            'title' => 'Nueva contraseña · AgroMáquina MX',
            'token' => $token,
            'robots' => 'noindex,nofollow',
        ]);
    }

    public function reset(Request $request, string $token): Response
    {
        $repo = new UserRepository();
        $user = $repo->findByResetToken($token);
        if ($user === null) {
            Flash::error('El enlace no es válido o ya venció. Solicita uno nuevo.');
            return $this->redirect('/recuperar');
        }

        $password = $request->string('password');
        $errors = [];
        if (mb_strlen($password) < 10) {
            $errors['password'] = 'La contraseña debe tener al menos 10 caracteres.';
        }
        if ($password !== $request->string('password_confirmation')) {
            $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
        }
        if ($errors !== []) {
            $this->withErrors($errors);
            return $this->redirect('/recuperar/' . $token);
        }

        $repo->updatePassword((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        $repo->consumeResetToken($token);
        $this->audit('cuenta.password_restablecida', 'usuario', (int) $user['id']);
        Flash::success('Tu contraseña se actualizó. Ya puedes iniciar sesión.');
        return $this->redirect('/acceder');
    }
}
