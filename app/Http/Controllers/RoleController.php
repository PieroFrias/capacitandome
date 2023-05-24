<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.listar.roles')->only('listarRoles', 'listarPermisos');
        $this->middleware('can:admin.crearEditar.roles')->only('mistrarRoles', 'guardarEditarRol');
        $this->middleware('can:admin.cambiarEstado.roles')->only('cambiarEstadoRol', 'obtenerRoles');

        // $this->middleware([
        //     'permission:roles.index',
        //     'permission:roles.show'
        // ]);
    }

    public function listarRoles()
    {
        $roles = Role::get();
        $permissions = Permission::get();

        return view('admin.rol.list', compact('roles', 'permissions'));
    }

    // Para actualizar la tabla dinámicamente cuando se actualice algún rol
    public function obtenerRoles()
    {
        $roles = Role::get();
        $permissions = Permission::get();

        return view('admin.rol.table_rol', compact('roles', 'permissions'));
    }

    public function guardarEditarRol(Request $request) {        
        $idrol = $request->input('idrol');

        if ($request->input('name') == NULL || $request->input('name') == "") {
            return redirect()->back()->with('error', 'El nombre del rol es requerido');
        }
        else {
            if ($idrol == NULL || $idrol == "") { 
                $rol        = new Role;
                $rol->name  = $request->input('name');
                $rol->save();
                $rol->permissions()->sync($request->get('permissions')); // Asignamos los permisos al rol
    
                return redirect()->back()->with('success', 'Rol registrado satisfactoriamente');
            } else {
                $rol        = Role::where('id', $idrol)->first();
                $rol->name  = $request->input('name');
                $rol->permissions()->sync($request->get('permissions')); // Asignamos los permisos al rol
                $rol->save();
                
                return redirect()->back()->with('success', 'Rol actualizado satisfactoriamente');
            }
        }
    }

    public function mostrarRoles($idrol) {
        $rol = Role::where('id', $idrol)->first();
        return \json_encode($rol);
    }

    public function listarPermisos($idrol) {
        $rol = Role::where('id', $idrol)->first();
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $permission->checked = $rol->hasPermissionTo($permission);
        }
        
        return view('admin.rol.lista_permisos', compact('permissions'));
    }    

    public function cambiarEstadoRol($idrol, $estado) {
        $rol = Role::where('id', $idrol)->first();
        $rol->estado = $estado;
        $rol->save();

        return json_encode(["status" => true, "message" => "Estado del rol cambiado."]);
    }
}