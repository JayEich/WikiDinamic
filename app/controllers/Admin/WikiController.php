<?php
namespace App\Controllers\Admin;

use App\Models\Wiki;
use App\Helpers\Auth;
use App\Helpers\View;
use App\Helpers\Redirect;

class WikiController {
    public function index()
    {
        $user = Auth::user();
        if ($user['role'] === 'superadmin') {
            $wikis = Wiki::getAll();
        } else {
            $wikis = Wiki::findByClientUuid($user['client_uuid']);
        }

        View::render('admin/wikis/index', [
            'wikis' => $wikis,
        ]);
    }

    // Mostrar el formulario de creación
    public function create()
    {
        View::render('admin/wikis/create');
    }

    // Procesar la creación
    public function store()
    {
        $data = $_POST;
        $data['client_uuid'] = Auth::user()['client_uuid'];

        $uuid = Wiki::create($data);

        if ($uuid) {
            Redirect::to('/admin/wikis')->with('success', 'Wiki creada exitosamente.');
        } else {
            Redirect::back()->with('error', 'Hubo un error creando la wiki.');
        }
    }

    // Mostrar formulario de edición
    public function edit($uuid)
    {
        $wiki = Wiki::findByUuid($uuid);

        if (!$wiki) {
            Redirect::to('/admin/wikis')->with('error', 'Wiki no encontrada.');
        }

        View::render('admin/wikis/edit', [
            'wiki' => $wiki,
        ]);
    }

    // Procesar actualización
    public function update($uuid)
    {
        $data = $_POST;

        if (Wiki::update($uuid, $data)) {
            Redirect::to('/admin/wikis')->with('success', 'Wiki actualizada.');
        } else {
            Redirect::back()->with('error', 'Error actualizando la wiki.');
        }
    }

    // Eliminar wiki (soft delete)
    public function delete($uuid)
    {
        if (Wiki::delete($uuid)) {
            Redirect::to('/admin/wikis')->with('success', 'Wiki eliminada.');
        } else {
            Redirect::back()->with('error', 'Error al eliminar.');
        }
    }

    // Restaurar wiki
    public function restore($uuid)
    {
        if (Wiki::restore($uuid)) {
            Redirect::to('/admin/wikis')->with('success', 'Wiki restaurada.');
        } else {
            Redirect::back()->with('error', 'Error al restaurar.');
        }
    }
}

