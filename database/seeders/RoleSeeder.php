<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        
        /***** Permisos para PersonaController *****/
        // Permisos para los Usuarios
        Permission::create(['name'=>'admin_personas', 'description'=>'Ver listado de usuarios'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin_personas_create','description'=>'Crear nuevos usuarios'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin_personas_edit', 'description'=>'Editar usuarios'])->syncRoles([$admin]);
        Permission::create(['name'=>'admin_personas_cambiarEstado', 'description'=>'Cambiar estado de los usuarios'])->syncRoles([$admin]);

        /***** Permisos para CoursesController *****/
        // Permisos para los cursos
        Permission::create(['name'=>'admin_course_list', 'description'=>'Ver listado de cursos'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'admin_course_nuevo','description'=>'Crear nuevos cursos'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'admin_course_edit', 'description'=>'Editar cursos'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'change_status_course', 'description'=>'Cambiar estado de los cursos'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Docente
        Permission::create(['name'=>'docentesCursoId', 'description'=>'Ver/Agregar/Editar nuevos docentes del curso'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'cambiarEstadoDocente', 'description'=>'Cambiar estado de los docentes del curso'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Requisitos
        Permission::create(['name'=>'requisitosCursoId', 'description'=>'Ver/Agregar/Editar nuevos requisitos del curso'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'cambiarEstadoRequisitosId', 'description'=>'Cambiar estado de los requisitos del del curso'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Temas
        Permission::create(['name'=>'temasCursoId', 'description'=>'Ver/Agregar/Editar nuevos temas del curso'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'cambiarEstadoTemas', 'description'=>'Cambiar estado de los temas del del curso'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Comunidad estudiantil
        Permission::create(['name'=>'comunidadCursoId', 'description'=>'Ver/Agregar/Editar nueva comunidad estudiantil del curso'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'cambiarEstadoComunidadId', 'description'=>'Cambiar estado de la comunidad estudiantil del del curso'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Secciones
        Permission::create(['name'=>'seccion_listar_agregar', 'description'=>'Ver/Crear/Editar nuevas secciones del curso'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'seccion_cambiar_estado', 'description'=>'Cambiar estado de las secciones del curso'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Clases de las secciones
        Permission::create(['name'=>'clase_agregar', 'description'=>'Ver/Crear/Editar nuevas clases de las secciones'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'clase_cambiar_estado', 'description'=>'Cambiar estado de las clases de las secciones'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Calificaciones de los estudiantes
        Permission::create(['name'=>'admin.estudiantes.calificacion.listar', 'description'=>'Ver listado de los estudiantes y sus promedios'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'admin.estudiantes.calificacion.listar.notas', 'description'=>'Ver listado de notas por cada examen de los estudiante'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'admin.estudiantes.calificacion.listar.resoluciones', 'description'=>'Ver listado de exámenes resueltos de los estudiantes'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Exámenes
        Permission::create(['name'=>'examen_listar', 'description'=>'Ver listado de los exámenes del curso'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'examen_agregar', 'description'=>'Crear/Editar exámenes'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'examen_listar_notas_estudiantes', 'description'=>'Ver listado de estudiantes que rindieron un exámen'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'examen_cambiar_estado', 'description'=>'Cambiar estado de los exámenes'])->syncRoles([$admin, $docente]);

        // Permisos para los cursos: Preguntas y alternativas de los exámenes
        Permission::create(['name'=>'preguntas_listar', 'description'=>'Ver listado de preguntas de los exámenes'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'preguntas_guardar', 'description'=>'Crear/Editar preguntas de los exámenes'])->syncRoles([$admin, $docente]);
        Permission::create(['name'=>'preguntas_eliminar', 'description'=>'Eliminar preguntas de los exámenes'])->syncRoles([$admin, $docente]);

        /***** Permisos para RoleController *****/
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