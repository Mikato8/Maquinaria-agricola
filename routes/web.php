<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CodeController;
use App\Controllers\FaultController;
use App\Controllers\GlossaryController;
use App\Controllers\GuideController;
use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Controllers\ImplementController;
use App\Controllers\InteractionController;
use App\Controllers\MachineController;
use App\Controllers\MaintenanceController;
use App\Controllers\PageController;
use App\Controllers\ProfileController;
use App\Controllers\ProviderController;
use App\Controllers\SearchController;
use App\Controllers\SitemapController;
use App\Controllers\TechController;
use App\Controllers\ToolsController;
use App\Controllers\WorkshopController;
use App\Core\Router;

return static function (Router $router): void {
    // Portada y búsqueda
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/buscar', [SearchController::class, 'index']);

    // Catálogo
    $router->get('/maquinas', [MachineController::class, 'index']);
    $router->get('/maquinas/comparar', [MachineController::class, 'compare']);
    $router->get('/maquinas/{slug}/pdf', [MachineController::class, 'pdf']);
    $router->get('/maquinas/{slug}', [MachineController::class, 'show']);

    $router->get('/implementos', [ImplementController::class, 'index']);
    $router->get('/implementos/{slug}', [ImplementController::class, 'show']);

    // Herramientas
    $router->get('/compatibilidad', [ToolsController::class, 'compatibility']);
    $router->get('/recomendador', [ToolsController::class, 'recommend']);
    $router->get('/costos', [ToolsController::class, 'costs']);

    // Taller técnico
    $router->get('/fallas', [FaultController::class, 'index']);
    $router->get('/fallas/{slug}', [FaultController::class, 'show']);
    $router->get('/mantenimiento', [MaintenanceController::class, 'index']);
    $router->get('/codigos', [CodeController::class, 'index']);

    // Tecnología y directorio
    $router->get('/tecnologia', [TechController::class, 'index']);
    $router->get('/tecnologia/{slug}', [TechController::class, 'show']);
    $router->get('/proveedores', [ProviderController::class, 'index']);
    $router->get('/proveedores/{slug}', [ProviderController::class, 'show']);

    // Guías y glosario
    $router->get('/guias', [GuideController::class, 'index']);
    $router->get('/guias/{slug}', [GuideController::class, 'show']);
    $router->get('/glosario', [GlossaryController::class, 'index']);

    // Comunidad de taller
    $router->get('/taller', [WorkshopController::class, 'index']);
    $router->get('/taller/nuevo', [WorkshopController::class, 'create']);
    $router->post('/taller', [WorkshopController::class, 'store']);
    $router->get('/taller/{slug}/editar', [WorkshopController::class, 'edit']);
    $router->post('/taller/{slug}', [WorkshopController::class, 'update']);
    $router->post('/taller/{slug}/enviar', [WorkshopController::class, 'submit']);
    $router->get('/taller/{slug}/pdf', [WorkshopController::class, 'pdf']);
    $router->post('/taller/{slug}/comentarios', [WorkshopController::class, 'comment']);
    $router->post('/taller/{slug}/util', [WorkshopController::class, 'react']);
    $router->post('/taller/{slug}/imagenes/{imageId}/eliminar', [WorkshopController::class, 'destroyImage']);
    $router->get('/taller/{slug}', [WorkshopController::class, 'show']);

    // Guardar en panel
    $router->post('/guardar/{type}/{id}', [InteractionController::class, 'bookmark']);

    // Páginas
    $router->get('/nosotros', [PageController::class, 'about']);
    $router->get('/fuentes', [PageController::class, 'sources']);
    $router->get('/privacidad', [PageController::class, 'privacy']);
    $router->get('/terminos', [PageController::class, 'terms']);
    $router->get('/licencia', [PageController::class, 'license']);
    $router->get('/contacto', [PageController::class, 'contact']);
    $router->post('/contacto', [PageController::class, 'contactPost']);

    // Cuenta
    $router->get('/acceder', [AuthController::class, 'showLogin']);
    $router->post('/acceder', [AuthController::class, 'login']);
    $router->get('/registro', [AuthController::class, 'showRegister']);
    $router->post('/registro', [AuthController::class, 'register']);
    $router->post('/salir', [AuthController::class, 'logout']);
    $router->get('/recuperar', [AuthController::class, 'showForgot']);
    $router->post('/recuperar', [AuthController::class, 'sendReset']);
    $router->get('/recuperar/{token}', [AuthController::class, 'showReset']);
    $router->post('/recuperar/{token}', [AuthController::class, 'reset']);

    // Perfil y panel
    $router->get('/mi-panel', [ProfileController::class, 'dashboard']);
    $router->get('/perfil', [ProfileController::class, 'show']);
    $router->post('/perfil', [ProfileController::class, 'update']);
    $router->post('/perfil/avatar', [ProfileController::class, 'avatar']);
    $router->post('/perfil/avatar/eliminar', [ProfileController::class, 'removeAvatar']);
    $router->post('/perfil/password', [ProfileController::class, 'password']);
    $router->post('/perfil/eliminar', [ProfileController::class, 'deleteAccount']);
    $router->get('/perfil-publico/{id}', [ProfileController::class, 'publicProfile']);

    // Panel editorial
    $router->get('/admin', [AdminController::class, 'dashboard']);
    $router->get('/admin/casos', [AdminController::class, 'cases']);
    $router->get('/admin/revision/{id}', [AdminController::class, 'review']);
    $router->post('/admin/revision/{id}', [AdminController::class, 'reviewPost']);
    $router->post('/admin/destacar/{id}', [AdminController::class, 'feature']);
    $router->get('/admin/usuarios', [AdminController::class, 'users']);
    $router->get('/admin/usuarios/{id}', [AdminController::class, 'user']);
    $router->post('/admin/usuarios/{id}/accion', [AdminController::class, 'userAction']);
    $router->get('/admin/comentarios', [AdminController::class, 'comments']);
    $router->post('/admin/comentarios/{id}/accion', [AdminController::class, 'commentAction']);
    $router->get('/admin/mensajes', [AdminController::class, 'inquiries']);
    $router->post('/admin/mensajes/{id}/accion', [AdminController::class, 'inquiryAction']);
    $router->get('/admin/auditoria', [AdminController::class, 'auditLog']);

    // Infraestructura
    $router->get('/salud', [HealthController::class, 'index']);
    $router->get('/sitemap.xml', [SitemapController::class, 'index']);
    $router->get('/robots.txt', [SitemapController::class, 'robots']);
};
