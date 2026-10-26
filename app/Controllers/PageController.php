<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\Database;

final class PageController extends BaseController
{
    public function about(Request $request): Response
    {
        return $this->view('pages/about', [
            'title' => 'Nosotros · AgroMáquina MX',
            'metaDescription' => 'Qué es AgroMáquina MX, cómo se construye el catálogo técnico y con qué criterios se publica la información de maquinaria agrícola para México.',
        ]);
    }

    public function sources(Request $request): Response
    {
        return $this->view('pages/sources', [
            'title' => 'Fuentes, método y verificación · AgroMáquina MX',
            'metaDescription' => 'Cómo verificamos especificaciones, precios, fallas y recursos de tecnología: fuentes, fechas de revisión, límites y política de correcciones.',
            'techResources' => Database::select("select name, kind, vendor, license, url, verified_on from tech_resources where kind in ('dataset', 'modelo', 'espacio', 'software') order by kind, name"),
        ]);
    }

    public function privacy(Request $request): Response
    {
        return $this->view('pages/privacy', [
            'title' => 'Aviso de privacidad · AgroMáquina MX',
            'metaDescription' => 'Qué datos personales recabamos, para qué los usamos y cómo ejercer tus derechos ARCO.',
        ]);
    }

    public function terms(Request $request): Response
    {
        return $this->view('pages/terms', [
            'title' => 'Términos de uso · AgroMáquina MX',
            'metaDescription' => 'Condiciones de uso del portal, límites de responsabilidad y reglas de publicación de casos de taller.',
        ]);
    }

    public function license(Request $request): Response
    {
        return $this->view('pages/license', [
            'title' => 'Licencia de contenido · AgroMáquina MX',
            'metaDescription' => 'Licencia del código, del contenido editorial y de los recursos de terceros enlazados.',
        ]);
    }

    public function contact(Request $request): Response
    {
        $kind = (string) ($request->query('motivo') ?: 'contacto');
        if (!in_array($kind, ['contacto', 'taller', 'proveedor', 'sugerencia'], true)) {
            $kind = 'contacto';
        }

        return $this->view('pages/contact', [
            'title' => 'Contacto · AgroMáquina MX',
            'metaDescription' => 'Escríbenos para corregir una ficha, proponer un proveedor, reportar una falla o pedir información.',
            'kind' => $kind,
            'states' => state_options(),
        ]);
    }

    public function contactPost(Request $request): Response
    {
        $data = [
            'name' => trim($request->string('name')),
            'email' => trim($request->string('email')),
            'phone' => trim($request->string('phone')),
            'state_code' => $request->string('state_code'),
            'subject' => trim($request->string('subject')),
            'message' => trim($request->string('message')),
            'kind' => $request->string('kind', 'contacto'),
        ];

        $validator = new Validator($data, ['name' => 'nombre', 'email' => 'correo', 'message' => 'mensaje']);
        $validator->validate([
            'name' => 'required|min:3|max:120',
            'email' => 'required|email|max:180',
            'phone' => 'nullable|max:30',
            'subject' => 'nullable|max:140',
            'message' => 'required|min:20|max:3000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors());
            $this->flashOld($data);
            return $this->redirect('/contacto?motivo=' . urlencode((string) $data['kind']));
        }

        Database::execute(
            'insert into inquiries (name, email, phone, state_code, subject, message, kind) values ($1, $2, $3, $4, $5, $6, $7)',
            [
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['state_code'] !== '' ? $data['state_code'] : null,
                $data['subject'] !== '' ? $data['subject'] : 'Contacto desde el portal',
                $data['message'],
                in_array($data['kind'], ['contacto', 'taller', 'proveedor', 'sugerencia'], true) ? $data['kind'] : 'contacto',
            ]
        );
        $this->audit('contacto.recibido', 'mensaje', null, ['kind' => $data['kind']]);

        return $this->view('pages/contact-thanks', [
            'title' => 'Mensaje recibido · AgroMáquina MX',
            'metaDescription' => 'Recibimos tu mensaje.',
            'name' => $data['name'],
            'robots' => 'noindex,nofollow',
        ]);
    }
}
