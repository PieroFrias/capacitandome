<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin = Role::create(['name' => 'Admin']);
        $docente = Role::create(['name' => 'Docente']);
        $estudiante = Role::create(['name' => 'Estudiante']);
        
        // Permisos para los Usuarios
        Permission::create(['name'=>'admin_personas', 'description'=>'Ver listado de usuarios'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin_personas_create','description'=>'Crear nuevos usuarios'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin_personas_edit', 'description'=>'Editar usuarios'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin_personas_cambiarEstado', 'description'=>'Cambiar estado de los usuarios'])->syncRoles([$admin]);

        // Permisos para los cursos
        Permission::create(['name'=>'admin_course_list', 'description'=>'Ver listado de cursos'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'admin_course_nuevo','description'=>'Crear nuevos cursos'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'admin_course_edit', 'description'=>'Editar cursos'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'change_status_course', 'description'=>'Cambiar estado de los cursos'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Secciones
        Permission::create(['name'=>'seccion_listar_agregar', 'description'=>'Ver/Crear/Editar nuevas secciones'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'seccion_cambiar_estado', 'description'=>'Cambiar estado de las secciones'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Docente
        Permission::create(['name'=>'seccion_listar_agregar', 'description'=>'Ver/Crear/Editar nuevas secciones'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'seccion_cambiar_estado', 'description'=>'Cambiar estado de las secciones'])->syncRoles([$admin, $docente]);

        // Permisos para los roles
        Permission::create(['name'=>'admin.listar.roles', 'description'=>'Ver listado de roles'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin.crearEditar.roles','description'=>'Crear/Editar nuevos roles'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin.cambiarEstado.roles', 'description'=>'Cambiar estado de los roles'])->syncRoles([$admin]);

        $administrador = User::find('1');
        $administrador->assignRole('Admin');

        // Solo pa pruebas
        $docentes = User::get();

        foreach ($docentes as $key => $docente) {
            if ($docente->idrol == '1') {
                $docente->assignRole('Docente');
            }
        }
    }
}