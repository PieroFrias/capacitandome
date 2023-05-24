<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Utiles\FilesController;
use App\Http\Requests\CertificadoRequest;
use App\Http\Requests\ClaseRequest;
use App\Http\Requests\CoursesEditRequest;
use App\Http\Requests\CoursesRequest;
use App\Http\Requests\ExamenRequest;
use App\Http\Requests\PreguntasRequest;
use App\Http\Requests\SeccionRequest;
use App\Models\Alternativa;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Clase;
use App\Models\ComunidadEstudiante;
use App\Models\Curso;
use App\Models\CursoTema;
use App\Models\Examen;
use App\Models\Pregunta;
use App\Models\Requisito;
use App\Models\Seccion;
use App\Models\Docente;
use App\Models\Persona;
use App\Models\ResolverExamen;
use App\Models\Respuesta;
use App\Models\Venta;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RequisitosRequest;
use App\Http\Requests\TemasRequest;
use App\Http\Requests\ComunidadRequest;
use App\Http\Requests\DocentesRequest;
use Illuminate\Support\Facades\Auth;

class CoursesController extends Controller 
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin_course_list')->only('getAdmin', 'getListarCuorsesPaginate');
        $this->middleware('can:admin_course_nuevo')->only('getNuevo', 'postGuardarSeccion');
        $this->middleware('can:admin_course_edit')->only('getEditar', 'postEditarCurso');
        $this->middleware('can:change_status_course')->only('getcambiarEstadoSeccion');

        $this->middleware('can:seccion_listar_agregar')->only('getAgregarSecciones', 'postGuardarSeccion', 'getMostrarSeccion', 'getEditar', 'postEditarCurso');
        $this->middleware('can:seccion_cambiar_estado')->only('getcambiarEstadoSeccion', 'obtenerSecciones');

        $this->middleware('can:docentesCursoId')->only('docentesIndex', 'mostrarDocente', 'guardarEditarDocentes');
        $this->middleware('can:cambiarEstadoDocente')->only('cambiarEstadoDocente', 'obtenerDocentes');

        $this->middleware('can:requisitosCursoId')->only('requisitosIndex', 'mostrarRequisitos', 'guardarEditarRequisitos');
        $this->middleware('can:cambiarEstadoRequisitosId')->only('cambiarEstadoRequisitos', 'obtenerRequisitos');

        $this->middleware('can:temasCursoId')->only('temasIndex', 'mostrarTemas', 'guardarEditarTemas');
        $this->middleware('can:cambiarEstadoTemas')->only('cambiarEstadoTemas', 'obtenerTemas');

        $this->middleware('can:comunidadCursoId')->only('comunidadIndex', 'mostrarComunidad', 'guardarEditarComunidad');
        $this->middleware('can:cambiarEstadoComunidadId')->only('cambiarEstadoComunidad', 'obtenerComunidad');

        $this->middleware('can:clase_agregar')->only('getAgregarClases', 'postGuardarClase', 'getMostrarClase');
        $this->middleware('can:clase_cambiar_estado')->only('getCambiarEstadoClase', 'obtenerClases');

        $this->middleware('can:admin.estudiantes.calificacion.listar')->only('listarEstudiantes', 'listarEstudiantesCurso');
        $this->middleware('can:admin.estudiantes.calificacion.listar.notas')->only('listarNotasUsuario');
        $this->middleware('can:admin.estudiantes.calificacion.listar.resoluciones')->only('listarEstudianteResoluciones');

        $this->middleware('can:examen_listar')->only('listarExamen');
        $this->middleware('can:examen_agregar')->only('verFormExamen', 'mostrarExamen', 'agregarExamen');
        $this->middleware('can:examen_listar_notas_estudiantes')->only('estudianteNotaExamen', 'listaEstudianteNotaExamen');
        $this->middleware('can:examen_cambiar_estado')->only('cambiarEstadoExamen', 'obtenerExamen');

        $this->middleware('can:preguntas_listar')->only('preguntasExamen');
        $this->middleware('can:preguntas_guardar')->only('alternativasPregunta', 'agregarPregunta', 'mostrarPregunta');
        $this->middleware('can:preguntas_eliminar')->only('eliminarPregunta');

        // $this->middleware([
        //     'permission:roles.index',
        //     'permission:roles.show'
        // ]);
    }

    /*************************************************************/
    /****************** CRUSOS CURSOS CURSOS *********************/
    /*************************************************************/
    /*************************************************************/

    public function getAdmin(Request $request)
    {
        return view('admin.course.list');
    }

    public function getListarCuorsesPaginate(Request $request, $estado)
    {
        $user = Auth::user();

        $cursos = Curso::where([
            ['titulo', 'like', "%{$request->filtro_search}%"],
            ['tipo', '=', 1]
        ]);

        if ($user->hasRole('Admin')) {
            if ($estado == 0) {
                $cursos = $cursos->orderBy('updated_at', 'desc')->paginate(10);
            } else {
                $cursos = $cursos->whereIn('estado', [1, 2])->orderBy('updated_at', 'desc')->paginate(10);
            }
        } else if ($user->hasRole('Docente')) {
            // Muestra los cursos según el docente que esté autenticado
            $cursos = Curso::join('curso_docente_usuario', 'curso.idcurso', '=', 'curso_docente_usuario.idcurso')
                ->join('users', 'users.idusuario', '=', 'curso_docente_usuario.idusuario')
                ->select('curso.idcurso', 'curso.titulo', 'curso.total_clases', 'curso.hora_duracion', 'curso.precio', 'curso.plan', 'curso.estado')
                ->where([['curso_docente_usuario.idusuario', $user->idusuario],['curso.titulo', 'LIKE', '%' . $request->filtro_search . '%']])->whereIn('curso.estado', [1, 2])->orderBy('curso.updated_at', 'desc')->paginate(10);
        }

        return view('admin.course.paginate_cursos', ['cursos' => $cursos])->render();
    }

    public function getNuevo() {
        $categorias = Categoria::select('idcategoria as id', 'categoria as nombre')->where('estado', 1)->get();
        /*$docentes   = DB::table('users')
                        ->join('persona as p', 'p.idpersona', '=', 'users.idpersona')
                        ->select('users.idusuario','p.nombre', 'p.apellidos')->distinct()->where([['p.tipo_persona','Docente']])->get();*/
        return view('admin.course.create.curso', compact('categorias'));
    }

    public function getEditar($idcurso)
    {
        $categorias = Categoria::select('idcategoria as id', 'categoria as nombre')->where('estado', 1)->get();
        $curso      = Curso::where('idcurso', $idcurso)->first();

        if ($curso) {
            return view('admin.course.create.edit', compact('categorias', 'curso'));
        } else {
            return redirect('/admin/courses');
        }
        
    }

    public function getCambiarEstadoCurso($idcurso, $estado)
    {
        $curso = Curso::where('idcurso', $idcurso)->first();
        $curso->estado = $estado;
        $curso->save();

        //return redirect()->back();
        return json_encode(["status" => true, "message" => "Se cambió el estado del registro"]);
    }

    ### CoursesRequest ###
    public function postGuardarCurso(CoursesRequest $request) {
        // return json_encode($request);

        $curso = new Curso;
        $curso->titulo                      = $request->input('titulo');
        $curso->idcategoria                 = $request->input('idcategoria');
        $curso->plan                        = $request->input('plan');
        $curso->precio                      = $request->input('precio');
        $curso->url_video_intro             = $request->input('url_video_intro');
        $curso->fecha_inicio                = $request->input('fecha_inicio');
        $curso->fecha_final                 = $request->input('fecha_final');
        $curso->hora_duracion               = $request->input('hora_duracion');
        $curso->total_clases                = $request->input('total_clases');

        $curso->portada                     = $this->guardarArchivo($request,'prin_', 'portada', 'cursos');
        $curso->url_portada_det             = $this->guardarArchivo($request,'sec_', 'url_portada_det', 'cursos');

        $curso->descripcion                 = $request->input('descripcion');
        $curso->descripcion_larga           = $request->input('descripcion_larga');
        $curso->recursos                    = $request->input('recursos');
        $curso->modalidad                   = $request->input('modalidad');
        $curso->plataforma                  = $request->input('plataforma');
        $curso->cetificado                  = $request->input('certificado');
        $curso->nom_certificado             = $request->input('nom_certificado');
        $curso->brochure                    = $this->guardarArchivo($request,'bro_', 'brochure', 'cursos');      

        $curso->save();

        $user = Auth::user();

        if ($user->hasRole('Docente')) {
            $docente            = new Docente();
            $docente->idcurso   = $curso->idcurso;
            $docente->idusuario = $user->idusuario;
            $docente->save();
        }

        return redirect('/admin/courses')->with('success','Curso creado satisfactoriamente');
    }

    public function postEditarCurso(CoursesEditRequest $request)
    {
        $curso = Curso::where('idcurso', $request->idcurso)->first();

        if ($curso) {

            $curso->titulo                      = $request->input('titulo');
            $curso->idcategoria                 = $request->input('idcategoria');
            $curso->plan                        = $request->input('plan');
            $curso->precio                      = $request->input('precio');
            $curso->url_video_intro             = $request->input('url_video_intro');
            $curso->fecha_inicio                = $request->input('fecha_inicio');
            $curso->fecha_final                 = $request->input('fecha_final');
            $curso->hora_duracion               = $request->input('hora_duracion');
            $curso->total_clases                = $request->input('total_clases');

            if ($request->hasFile('portada')) {
                $curso->portada                 = $this->editarArchivo($request,'prin_','portada_actual', 'portada');
            }

            if ($request->hasFile('url_portada_det')) {
                $curso->url_portada_det         = $this->editarArchivo($request,'sec_', 'url_portada_det_actual', 'url_portada_det');
            }

            $curso->descripcion                 = $request->input('descripcion');
            $curso->descripcion_larga           = $request->input('descripcion_larga');
            $curso->recursos                    = $request->input('recursos');
            $curso->modalidad                   = $request->input('modalidad');
            $curso->plataforma                  = $request->input('plataforma');
            $curso->cetificado                  = $request->input('certificado');
            $curso->nom_certificado             = $request->input('nom_certificado');

            if ($request->hasFile('brochure')) {
                $curso->brochure                = $this->editarArchivo($request,'bro_', 'brochure_actual', 'brochure');
            }

            $curso->save();
            return redirect('/admin/courses')->with('success','Curso editado satisfactoriamente');
        } else {
            return redirect('/admin/courses')->with('error','No se encontró el curso a editar.');
        }
    }

    public function guardarArchivo($request,$uid, $nombre_archivo, $url_carpeta) {
        $archivo_guardado = null;
        if ($request->hasFile($nombre_archivo)) {
            $archivo            = $request->file($nombre_archivo);
            $nombre             = $archivo->getClientOriginalName();
            $extension          = explode(".", $nombre);
            $nuevo_nombre       = $uid."".round(microtime(true)) . '.' . end($extension);

            if (!Storage::disk('public')->exists($url_carpeta)) {
                Storage::makeDirectory('public/'.$url_carpeta.'', 0775, true);
            }
            Storage::disk('public')->put($url_carpeta."/".$nuevo_nombre, \File::get($archivo));
            $archivo_guardado = $nuevo_nombre;
        } else {
            $archivo_guardado = null;
        }
        return $archivo_guardado;
    }

    public function editarArchivo($request, $uid, $actual, $nombre_archivo) {
            $archivo            = $request->file($nombre_archivo);
            $nombre             = $archivo->getClientOriginalName();
            $extension          = explode(".", $nombre);
            $nuevo_nombre       = $uid."".round(microtime(true)) . '.' . end($extension);
            Storage::delete('public/cursos/'.$request->$actual);
            Storage::disk('public')->put('cursos/'.$nuevo_nombre, \File::get($archivo));
            return $nuevo_nombre;
    }

    /*************************************************************/
    /************** SECCIONES SECCIONES SECCIONES ****************/
    /*************************************************************/
    /*************************************************************/

    public function getAgregarSecciones($idcurso = 1)
    {
        $curso = Curso::with(['Secciones' => function($query) {
            return $query;
        }])->activos()->where('idcurso', $idcurso)->first();     

        if ($curso) {
            return view('admin.course.create.secciones', compact('curso'));
        }else{
            // abort(404);
            return back();
        }
    }

    // Para actualizar la tabla dinámicamente cuando se cambie el estado de alguna sección
    public function obtenerSecciones($idcurso = 1)
    {
        $curso = Curso::with(['Secciones' => function($query) {
            return $query;
        }])->activos()->where('idcurso', $idcurso)->first();     

        if ($curso) {
            return view('admin.course.create.secciones_table', compact('curso'));
        }else{
            // abort(404);
            return back();
        }
    }

    public function postGuardarSeccion(SeccionRequest $request)
    {
        $idseccion = $request->input('idseccion');

        if ($idseccion != null || $idseccion != '') {
            $seccion = Seccion::where('idseccion', $idseccion)->first();

            if ($seccion) {
                $seccion->titulo            = $request->input('titulo');
                $seccion->nombre_seccion    =  $request->input('nombre_seccion');
                $seccion->descripcion       = $request->input('descripcion');
                $seccion->entregable        = $request->input('entregable');
                $seccion->save();

                return redirect()->back()->with('success','Registro editado satisfactoriamente');
            }else{
                return redirect()->back()->with('error','No existe esta seccion');
            }
        }else{
            $seccion = Seccion::firstOrCreate(
                [
                    'idcurso' => $request->input('idcurso'),
                    'titulo'  => $request->input('titulo')
                ],
                [
                    'nombre_seccion'=>  $request->input('nombre_seccion'),
                    'descripcion'   =>  $request->input('descripcion'),
                    'url_video'     => $request->input('url_video'),
                    'entregable'    => $request->input('entregable')
                ]
            );

            return redirect()->back()->with('success','Registro creado satisfactoriamente');
        }
    }

    public function getMostrarSeccion($idseccion)
    {
        $seccion = Seccion::where('idseccion', $idseccion)->first();

        return json_encode($seccion);
    }

    public function getcambiarEstadoSeccion($idseccion, $estado)
    {
        $seccion = Seccion::where('idseccion', $idseccion)->first();
        $seccion->estado = $estado;
        $seccion->save();

        return json_encode(["status" => true, "message" => "Se eliminó el registro"]);
    }

    /************************************************************/
    /************** CLASE CLASE CLASE ***************************/
    /************************************************************/
    /************************************************************/

    public function getAgregarClases($idseccion = 1)
    {
        $seccion = Seccion::with(['Clases' => function($query) {
           return $query;
        }])->activos()->where('idseccion', $idseccion)->first();

        if ($seccion) {
            return view('admin.course.create.clases', compact('seccion'));
        }else{
            // abort(404);
            return back();
        }
    }

    public function obtenerClases($idseccion = 1)
    {
        $seccion = Seccion::with(['Clases' => function($query) {
           return $query;
        }])->activos()->where('idseccion', $idseccion)->first();

        if ($seccion) {
            return view('admin.course.create.clases_table', compact('seccion'));
        }else{
            // abort(404);
            return back();
        }
    }

    public function postGuardarClase(ClaseRequest $request)
    {
        $idclase = $request->input('idclase');

        if ($idclase != null || $idclase != '') {

            $clase = Clase::where('idclase', $idclase)->first();

            if ($clase) {
                $clase->titulo        = $request->input('titulo');
                $clase->descripcion   = $request->input('descripcion');
                $clase->url_video     = $request->input('url_video');
                $clase->minutos_video = $request->input('minutos_video');
                $clase->save();

                return redirect()->back()->with('success','Registro editado satisfactoriamente');
            }else{
                return redirect()->back()->with('error','No existe esta clase');
            }
        }else{
            $clase = Clase::firstOrCreate(
                [
                    'idseccion' => $request->input('idseccion'),
                    'titulo'    => $request->input('titulo')
                ],
                [
                    'descripcion'   => $request->input('descripcion'),
                    'url_video'     => $request->input('url_video'),
                    'minutos_video' => $request->input('minutos_video')
                ]
            );

            return redirect()->back()->with('success','Registro creado satisfactoriamente');
        }
    }

    public function getMostrarClase($idclase)
    {
        $clase = Clase::where('idclase', $idclase)->first();

        return json_encode($clase);
    }

    public function getCambiarEstadoClase($idclase, $estado)
    {
        $clase = Clase::where('idclase', $idclase)->first();

        $clase->estado = $estado;

        $clase->save();

        // return redirect()->back();
        return json_encode(["status" => true, "message" => "Se eliminó el registro"]);
    }

    /************************************************************/
    /************* REQUISITOS REQUISITOS REQUISITOS *************/
    /************************************************************/
    /************************************************************/

    public function requisitosIndex($id) {
        $curso = Curso::where('idcurso', $id)->first();

        if ($curso) {
            $requisitos = Requisito::where([['idcurso', $id]])->distinct()->get();

            return view('admin.course.recursos.requisitos.crud', compact('curso','requisitos'));
        } else {
            return redirect('/admin/courses');
        }        
    }

    // Para actualizar la tabla dinámicamente cuando se cambie el estado de algún requisito
    public function obtenerRequisitos($id) {
        $curso = Curso::where('idcurso', $id)->first();

        if ($curso) {
            $requisitos = Requisito::where([['idcurso', $id]])->distinct()->get();

            return view('admin.course.recursos.requisitos.crud_requisitos_table', compact('curso','requisitos'));
        } else {
            //return redirect('/admin/courses');
        }        
    }

    public function guardarEditarRequisitos(RequisitosRequest $request) {        
        $idrequisitos = $request->input('idrequisitos');

        if ($idrequisitos == NULL || $idrequisitos == "") { 
            $requisitos              = new Requisito;
            $requisitos->idcurso     = $request->input('idcurso');
            $requisitos->requisitos  = $request->input('titulo');
            $requisitos->save();
            return redirect()->back()->with('success','Requisito registrado satisfactoriamente');
        } else {
            $requisitos              = Requisito::where('idrequisitos', $idrequisitos)->first();
            $requisitos->idcurso     = $request->input('idcurso');
            $requisitos->requisitos  = $request->input('titulo');
            $requisitos->save();
            return redirect()->back()->with('success','Requisito actualizado satisfactoriamente');
        }
    }

    public function mostrarRequisitos($idrequisitos) {
        $requisitos = Requisito::where('idrequisitos',$idrequisitos)->first();
        return \json_encode($requisitos);
    }

    public function cambiarEstadoRequisitos($idrequisitos, $estado) {
        //$requisitos = Requisito::find($idrequisitos);
        //$requisitos->delete();

        $requisitos = Requisito::where('idrequisitos', $idrequisitos)->first();
        $requisitos->estado = $estado;
        $requisitos->save();

        //return redirect()->back();
        return json_encode(["status" => true, "message" => "Estado del requisito cambiado."]);
    }
    
    /************************************************************/
    /******************** TEMAS TEMAS TEMAS *********************/
    /************************************************************/
    /************************************************************/

    public function temasIndex($id) {
        $curso = Curso::where('idcurso',$id)->first();
        if ($curso) {
            $temas = CursoTema::where([['idcurso',$id]])->distinct()->get();
            
            return view('admin.course.recursos.temas.crud', compact('curso','temas'));
        } else {
            return redirect('/admin/courses');
        }        
    }

    // Para actualizar la tabla dinámicamente cuando se cambie el estado de algún tema
    public function obtenerTemas($id) {
        $curso = Curso::where('idcurso', $id)->first();

        if ($curso) {
            $temas = CursoTema::where([['idcurso',$id]])->distinct()->get();

            return view('admin.course.recursos.temas.crud_temas_table', compact('curso','temas'));
        } else {
            //return redirect('/admin/courses');
        }        
    }

    public function guardarEditarTemas(TemasRequest $request) {
        
        $idcurso_tema = $request->input('idaprenderas');

        if ($idcurso_tema == NULL || $idcurso_tema == "") { 
            $temas              = new CursoTema;
            $temas->idcurso     = $request->input('idcurso');
            $temas->temas       = $request->input('titulo');
            $temas->save();
            return redirect()->back()->with('success','Tema registrado satisfactoriamente');
        } else {
            $temas              = CursoTema::where('idcurso_tema', $idcurso_tema)->first();
            $temas->idcurso     = $request->input('idcurso');
            $temas->temas       = $request->input('titulo');
            $temas->save();
            return redirect()->back()->with('success','Tema actualizado satisfactoriamente');
        }
    }

    public function mostrarTemas($idtemas) {
        $temas = CursoTema::where('idcurso_tema',$idtemas)->first();
        return \json_encode($temas);
    }

    public function cambiarEstadoTemas($idtemas, $estado) {
        $tema = CursoTema::where('idcurso_tema', $idtemas)->first();
        $tema->estado = $estado;
        $tema->save();

        return json_encode(["status" => true, "message" => "Estado cambiado"]);
    }
    
    /************************************************************/
    /************** COMUNIDAD COMUNIDAD COMUNIDAD ***************/
    /************************************************************/
    /************************************************************/

    public function comunidadIndex($id) {
        $curso = Curso::where('idcurso',$id)->first();

        if ($curso) {
            $comunidad = ComunidadEstudiante::where('idcurso',$id)->get();
            return view('admin.course.recursos.comunidad.crud', compact('curso','comunidad'));
        } else {
            return redirect('/admin/courses');
        }        
    }

    // Para actualizar la tabla dinámicamente cuando se cambie el estado de alguna comunidad
    public function obtenerComunidad($id) {
        $curso = Curso::where('idcurso', $id)->first();

        if ($curso) {
            $comunidad = ComunidadEstudiante::where('idcurso',$id)->get();
            return view('admin.course.recursos.comunidad.crud_comunidad_table', compact('curso','comunidad'));
        } else {
            //return redirect('/admin/courses');
        }        
    }

    public function guardarEditarComunidad(ComunidadRequest $request) {        
        $idcomunidad_estudiantil = $request->input('idcomunidad_estudiantil');

        if ($idcomunidad_estudiantil == NULL || $idcomunidad_estudiantil == "") { 
            $ComunidadEst                        = new ComunidadEstudiante();
            $ComunidadEst->idcurso               = $request->input('idcurso');
            $ComunidadEst->comunidad             = $request->input('comunidad_estudiantil');
            $ComunidadEst->save();
            return redirect()->back()->with('success','Comunidad registrada satisfactoriamente.');
        } else {
            $ComunidadEst                        = ComunidadEstudiante::where('idcomunidad', $idcomunidad_estudiantil)->first();
            $ComunidadEst->idcurso               = $request->input('idcurso');
            $ComunidadEst->comunidad             = $request->input('comunidad_estudiantil');
            $ComunidadEst->save();
            return redirect()->back()->with('success','Certificación actualizado satisfactoriamente');
        }
    }

    public function mostrarComunidad($idcomunidad_estudiantil) {
        $comunidad = ComunidadEstudiante::where('idcomunidad',$idcomunidad_estudiantil)->first();
        return \json_encode($comunidad);
    }

    public function cambiarEstadoComunidad($idcomunidad_estudiantil, $estado) {
        // $comunidad = ComunidadEstudiante::find($idcomunidad_estudiantil);
        // $comunidad->delete();

        $comunidad = ComunidadEstudiante::where('idcomunidad', $idcomunidad_estudiantil)->first();

        $comunidad->estado = $estado;

        $comunidad->save();

        // return redirect()->back();
        return json_encode(["status" => true, "message" => "Comunidad eliminada"]);
    }

    /************************************************************/
    /**************** DOCENTES DOCENTES DOCENTES ****************/
    /************************************************************/
    /************************************************************/

    public function docentesIndex($id) {
        $user = Auth::user();
        
        if ($user->hasRole('Docente')) {
            $user = $user->idusuario;
        }

        $curso          = Curso::where('idcurso', $id)->first();

        if ($curso) {
            $personas       = User::join('persona', 'persona.idpersona', '=', 'users.idpersona')
                            ->select('users.idusuario','persona.nombre','persona.apellidos')
                            ->where([['persona.estado','1'],['persona.tipo_persona','=','Docente']])->get();
            $docentes       = Docente::join('users', 'users.idusuario', '=', 'curso_docente_usuario.idusuario')
                            ->join('persona', 'persona.idpersona', '=', 'users.idpersona')
                            ->join('curso', 'curso.idcurso', '=', 'curso_docente_usuario.idcurso')
                            ->select('curso_docente_usuario.iddocente','curso_docente_usuario.idcurso','curso_docente_usuario.estado','persona.nombre','persona.apellidos')
                            ->where('curso_docente_usuario.idcurso',$id)
                            ->distinct()->get();

            return view('admin.course.recursos.docentes.crud', compact('curso','personas','docentes','user'));
        } else {
            return redirect('/admin/courses');
        }
        
        //return view('admin.course.recursos.docentes.crud', compact('curso','personas','docentes'));
    }

    // Para actualizar la tabla dinámicamente cuando se cambie el estado de algún docente
    public function obtenerDocentes($id) {
        $curso          = Curso::where('idcurso', $id)->first();

        if ($curso) {
            $personas       = User::join('persona', 'persona.idpersona', '=', 'users.idpersona')
                            ->select('users.idusuario','persona.nombre','persona.apellidos')
                            ->where([['persona.estado','1'],['persona.tipo_persona','=','Docente']])->get();
            $docentes       = Docente::join('users', 'users.idusuario', '=', 'curso_docente_usuario.idusuario')
                            ->join('persona', 'persona.idpersona', '=', 'users.idpersona')
                            ->join('curso', 'curso.idcurso', '=', 'curso_docente_usuario.idcurso')
                            ->select('curso_docente_usuario.iddocente','curso_docente_usuario.idcurso','curso_docente_usuario.estado','persona.nombre','persona.apellidos')
                            ->where('curso_docente_usuario.idcurso',$id)
                            ->distinct()->get();

            return view('admin.course.recursos.docentes.crud_docentes_table', compact('curso','personas','docentes'));
        } else {
            return redirect('/admin/courses');
        }
        
        //return view('admin.course.recursos.docentes.crud', compact('curso','personas','docentes'));
    }

    public function guardarEditarDocentes(DocentesRequest $request) {        
        $iddocentes = $request->input('iddocentes');

        if ($iddocentes == NULL || $iddocentes == "") { 
            $totalDocente = Docente::where([['idcurso',$request->input('idcurso')],['idusuario',$request->input('idpersona')]])->count();

            if ($totalDocente == 0) {
                $docente            = new Docente();
                $docente->idcurso   = $request->input('idcurso');
                $docente->idusuario = $request->input('idpersona');
                $docente->save();
                return redirect()->back()->with('success','Docente registrado satisfactoriamente,');
            } else {
                return redirect()->back()->with('error','Docente ya se encuentra registrado.');
            }
        } else {
            DB::table('curso_docente_usuario')
                ->where('iddocente',$iddocentes)
                ->update(['idcurso' => $request->input('idcurso'),'idusuario' => $request->input('idpersona')]);
            return redirect()->back()->with('success', "Actualizado");
        }
    }

    public function mostrarDocente($iddocentes) {
        $docentes = DB::table('curso_docente_usuario')->where('iddocente',$iddocentes)->first();
        return \json_encode($docentes);
    }
    
    public function cambiarEstadoDocente($iddocente, $estado) {
        DB::table('curso_docente_usuario')
            ->where('iddocente',$iddocente)
            ->update(['estado' => $estado]);

        return json_encode(["status" => true, "message" => "Eliminado"]);
    }    

    /************************************************************/
    /************ ESTUDIANTES ESTUDIANTES ESTUDIANTES ***********/
    /************************************************************/
    /************************************************************/

    // Lista los estudiantes y sus calificaciones
    public function listarEstudiantes($idcurso)
    {
        $curso = Curso::where('idcurso', $idcurso)->first();

        if ($curso) {
            return view('admin.course.estudiantes.lista_estudiantes', compact('curso'));
        } else {
            return redirect('/admin/courses');
        }      
    }

    public function listarEstudiantesCurso(Request $request)
    {
        $idcurso = $request->idcurso;
        $searh_estudiante = $request->searh_estudiante;

        $estudiantes = User::with(['Ventas', 'Persona' => function ($query){
                                // return $query->orderBy('apellidos', 'DESC');
                            }, 'Certificados', 'ResolverExamenes', 'ResolverExamenes.Examen'])
                            ->whereHas('Persona', function ($query) use ($searh_estudiante){
                                return $query->where('nombre', 'like', "%{$searh_estudiante}%")
                                             ->orWhere('apellidos', 'like', "%{$searh_estudiante}%")
                                             ->orderBy('apellidos', 'DESC');
                            })
                            ->whereHas('Ventas' , function ($query) use ($idcurso){
                                return $query->where('idcurso', $idcurso)->where('estado', '!=', 2);
                            })
                            // ->get();
                            ->paginate(500);

        $curso = Curso::with('Secciones', 'Secciones.Examenes')->where('idcurso', $idcurso)->first();

        return view('admin.course.estudiantes.table_lista_estudiantes', compact('estudiantes', 'curso'))->render();
    }

    public function listarNotasUsuario($idUser, $idCurso){
        // $notas = User::with('Persona', 'ResolverExamenes', 'ResolverExamenes.Examen', 'ResolverExamenes.Examen.Seccion', 'ResolverExamenes.Examen.Curso')->where('idusuario', $idUser)->first();

        $user = User::with('Persona')->where('idusuario', $idUser)->first();

        $examenes = Seccion::with(['Examenes', 'Examenes.ResolverExamen' => function($query) use($idUser){
                                                    return $query->where('idusuario', $idUser);
                                                }])->where('idcurso', $idCurso)
                                                ->where('estado', '1')
                                                ->get();

        // return json_encode($notas);
        return view('admin.course.estudiantes.notas_estudiante', compact('user', 'examenes')); //'notas',
    }

    public function listarEstudianteResoluciones($idCurso, $idUser){
        $estudiante = User::with('Persona')->where('idusuario', $idUser)->first();

        $examenes = Examen::with('Seccion')->where('idcurso', $idCurso)->where('estado', '1')->get();

        return view('admin.course.estudiantes.estudiante_examen', compact('estudiante', 'examenes', 'idCurso'));
        // return json_encode($examenes);
    }

    /************************************************************ */
    /************** EXAMENES DEL CURSO *********************** */
    /************************************************************ */
    /************************************************************ */

    public function listarExamen($idCurso){
        $examen = Curso::with('Examenes', 'Examenes.Seccion')->where('idcurso', $idCurso)->first();
        $secciones = Seccion::where('idcurso', $idCurso)->where('estado', '1')->get();

        if ($examen) {
            return view('admin.course.examen.examen', compact('examen', 'secciones'));
        }else{
            return back();
        }

        // return json_encode($examen);
    }

    // Para actualizar la tabla dinámicamente cuando se cambie el estado de algún examen
    public function obtenerExamen($idCurso){
        $examen = Curso::with('Examenes', 'Examenes.Seccion')->where('idcurso', $idCurso)->first();
        $secciones = Seccion::where('idcurso', $idCurso)->where('estado', '1')->get();

        if ($examen) {
            return view('admin.course.examen.examen_table', compact('examen', 'secciones'));
        }else{
            return back();
        }
    }

    public function verFormExamen($idCurso){
        $exam = [];
        $curso = Curso::where('idcurso', $idCurso)->first();
        $secciones = Seccion::where('idcurso', $idCurso)->where('estado', '1')->get();

        return view('admin.course.examen.agregar_modificar_examen', compact('exam', 'curso', 'secciones'));
    }

    public function mostrarExamen($idExam){
        $exam = Examen::where('idexamen', $idExam)->first();
        $curso = Curso::where('idcurso', $exam->idcurso)->first();
        $secciones = Seccion::where('idcurso', $exam->idcurso)->where('estado', '1')->get();

        // return json_encode($exam);
        return view('admin.course.examen.agregar_modificar_examen', compact('exam', 'curso', 'secciones'));
    }

    public function agregarExamen(ExamenRequest $request){
        $idexamen = $request->input('idexamen');

        if ($idexamen != null || $idexamen != '') {
            $examen = Examen::where('idexamen', $idexamen)->first();

            if ($examen) {
                $examen->titulo         = $request->input('titulo');
                $examen->idseccion      = $request->input('idseccion');
                $examen->descripcion    = $request->input('descripcion');
                $examen->fecha_final    = $request->input('fecha_fin');
                $examen->save();

                // return redirect()->back()->with('success','Registro modificado satisfactoriamente'.$request->input('fecha_fin'));
                return redirect('/admin/course/examen/'.$request->input('idcurso'))->with('success','Registro modificado satisfactoriamente'.$request->input('fecha_fin'));
            }else{
                // return redirect()->back()->with('error','No existe este examen');
                return redirect('/admin/course/examen/'.$request->input('idcurso'))->with('error','No existe este examen');
            }
        }else{
            $fecha = \Carbon\Carbon::parse($request->input('fecha_fin'))->format('Y-m-d H:i:s');

            $examen = Examen::firstOrCreate(
                [
                    'idcurso'               => $request->input('idcurso'),
                    'titulo'                => $request->input('titulo'),
                    'idseccion'             => $request->input('idseccion'),
                ],
                [
                    'titulo'                => $request->input('titulo'),
                    'idseccion'             => $request->input('idseccion'),
                    'fecha_final'           => $fecha,
                    'descripcion'           => $request->input('descripcion'),
                    'idcurso'               => $request->input('idcurso'),
                    'estado'                => 1
                ]
            );

            // return redirect()->back()->with('success','Registro creado satisfactoriamente'.$fecha);
            return redirect('/admin/course/examen/'.$request->input('idcurso'))->with('success','Registro creado satisfactoriamente'.$fecha);
        }
    }

    // Previsualización del examen
    public function mostrarExamenCompleto($idExam){
        $exam = Examen::with('Preguntas', 'Preguntas.Alternativas', 'Preguntas.Correcta')->where('idexamen', $idExam)->first();

        // return json_encode($exam);
        return view('admin.course.examen.examen_completo', compact('exam'));
    }

    public function estudianteNotaExamen($idExamen){
        $examen = Examen::where('idexamen', $idExamen)->first();
        $estudiantes = Venta::with('User', 'User.Persona')->where('idcurso', $examen->idcurso)->get();
        $resoluciones = ResolverExamen::where('idexamen', $examen->idexamen)->get();

        return view('admin.course.examen.notas_estudiantes', compact('examen', 'estudiantes', 'resoluciones'));
    }

    public function listaEstudianteNotaExamen(Request $request){
        $searh_estudiante = $request->searh_estudiante;
        $idExamen = $request->idexamen;

        $examen = Examen::where('idexamen', $idExamen)->first();

        $idcurso = $examen->idcurso;

        $estudiantes = User::with(['Ventas', 'Persona'])
                        ->whereHas('Persona', function ($query) use ($searh_estudiante){
                            return $query->where('nombre', 'like', "%{$searh_estudiante}%")
                                        ->orWhere('apellidos', 'like', "%{$searh_estudiante}%")
                                        ->orderBy('apellidos', 'DESC');
                        })
                        ->whereHas('Ventas' , function ($query) use ($idcurso){
                            return $query->where('idcurso', $idcurso);
                        })
                        ->get();
                        // ->paginate(500);

        $resoluciones = ResolverExamen::where('idexamen', $examen->idexamen)->get();

        return view('admin.course.examen.table_notas_estudiantes', compact('examen', 'estudiantes', 'resoluciones'));
    }

    // public function listarExamenUsuario($idExam, $idUser = null){
    //     $exam = Examen::where('idexamen', $idExam)->first();

    //     $estudiantes = Venta::with('User', 'User.Persona')->where('idcurso', $exam->idcurso)->get();

    //     return view('admin.course.estudiantes.examen_estudiante', compact('exam', 'estudiantes', 'idUser'));
    // }

    public function getVerExamenResuelto($idusuario, $idexamen)
    {
        $examen = Examen::with(['Curso','Seccion', 'Preguntas', 'Preguntas.Alternativas'])
        ->where('idexamen', $idexamen)
        ->first();

        // return json_encode($examen);

        if ($examen) {
            $resolver_examen = ResolverExamen::with('DetalleResolverExamen')->where('idusuario', $idusuario)->where('idexamen', $idexamen)->first();

            if ($resolver_examen) {
                # code...
            }

            return view('admin.course.examen.ver_resolucion', compact('examen' , 'resolver_examen'));

        }else{
            return 'HA OCURRIDO UN ERROR';
        }
    }

    public function cambiarEstadoExamen($idExam, $estado) {
        $examen = Examen::where('idexamen', $idExam)->first();
        $examen->estado = $estado;
        $examen->save();

        return json_encode(["status" => true, "message" => "Se cambió el estado del registro"]);
    }

    public function preguntasExamen($idExam) {
        $preguntas = Examen::with('Preguntas')->where('idexamen', $idExam)->first();

        if ($preguntas) {
            return view('admin.course.examen.preguntas', compact('preguntas'));
        }else{
            return back();
        }
    }

    public function alternativasPregunta($idPreg) {
        $alternativas = Pregunta::with('Alternativas')->where('idpregunta', $idPreg)->first();

        return json_encode($alternativas);
    }

    public function agregarPregunta(PreguntasRequest $request) {
        $idpreg = $request->input('idpregunta');

        if($idpreg != null || $idpreg != ''){
            $pregunta = Pregunta::where('idpregunta', $idpreg)->first();

            if($pregunta){
                $pregunta->nombre = $request->input('pregunta');
                $pregunta->puntos = $request->input('puntos');
                $pregunta->save();

                $alternativas = $request->input('alternativas');
                $correctas    = $request->input('correctas');

                $count_alternativa = count((array) $alternativas);

                if($pregunta){
                    try {
                        $alternat = Alternativa::where('idpregunta', $pregunta->idpregunta)->delete();
                    }
                    catch (\Illuminate\Database\QueryException $e) {
                        if ($e->getCode() == 23000)
                        {
                            //SQLSTATE[23000]: Integrity constraint violation
                            return redirect()->back()->with('error','Este regritro esta siendo usado, no pueden eliminar las alternativas');
                        }
                    }
                    
                    for ($i=0; $i < $count_alternativa ; $i++) {
                        $alternativa = Alternativa::updateOrCreate([
                            'idpregunta'            => $pregunta->idpregunta,
                            'nombre'                => $alternativas[$i],
                        ],
                        [
                            'correcta'              => $correctas[$i],
                        ]
                    );

                    }

                }
            } else {
                return back();
            }

            return redirect()->back()->with('success','Registro modificado satisfactoriamente');
        } else {
            $pregunta = Pregunta::firstOrCreate(
                [

                    'idexamen'              => $request->input('idexamen'),
                    'nombre'                => $request->input('pregunta'),

                ], [

                    'idexamen'              => $request->input('idexamen'),
                    'nombre'                => $request->input('pregunta'),
                    'puntos'                => $request->input('puntos'),
                    'estado'                => '1'

                ]
            );

            $alternativas = $request->input('alternativas');
            $correctas    = $request->input('correctas');

            $count_alternativa = count((array) $alternativas);

            if($pregunta){
                for ($i=0; $i < $count_alternativa ; $i++) {
                    $alternativa = Alternativa::firstOrCreate([
                        'idpregunta'            => $pregunta->idpregunta,
                        'nombre'                => $alternativas[$i],
                        'correcta'              => $correctas[$i],
                    ]);
                }
            }

            return redirect()->back()->with('success','Registro creado satisfactoriamente');
        }
    }

    public function mostrarPregunta($idPreg) {
        $alternativas = Pregunta::with('Alternativas')->where('idpregunta', $idPreg)->first();

        return json_encode($alternativas);
    }

    public function eliminarPregunta($idPreg) {
        $alternat = Alternativa::where('idpregunta', $idPreg)->delete();
        $pregunta = Pregunta::where('idpregunta', $idPreg)->delete();

        return json_encode(["status" => true, "message" => "Pregunta eliminada"]);
    }
}