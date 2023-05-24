<?php

namespace App\Http\Controllers;

use App\calificacion_curso;
use Illuminate\Http\Request;
use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CursoRequest;
use App\Models\Persona;
use App\Models\Usuario;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;

class CursoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth', ['only' => ['ListCursosComprados']]);
    }

    #
    #
    #-----------------¿METODOS Y MODELOS PARA LA WEB----------------------
    #
    #
    #

    #------ INICIO
    public function listInicio()
    {
        // dd(auth()->user());
        $total_alumnos = DB::table('persona')->where('tipo_persona', '=', 'estudiante')->distinct()->count();
        $total_docente = DB::table('persona')->where('tipo_persona', '=', 'Docente')->distinct()->count();
        $docentes = DB::table('persona')->where('tipo_persona', '=', 'Docente')->get();
        $total_cursos  = DB::table('curso')->where('estado', '=', '2')->distinct()->count();
        $cursos        = $this->listCursosWeb();
        $categorias    = $this->listCategoriasWeb();

        return view('inicio')->with([
            'cursos'        => $cursos,
            'categorias'    => $categorias,
            'total_alumnos' => $total_alumnos,
            'total_docente' => $total_docente,
            'total_cursos'  => $total_cursos,
            'docentes'     => $docentes,
        ]);
    }

    public function listCursosWeb()
    {
        $cursos = DB::table('curso')
            ->join('categoria as c', 'c.idcategoria', '=', 'curso.idcategoria')
            ->select('curso.idcurso', 'curso.titulo', 'curso.portada', 'curso.plan', 'curso.descripcion', 'curso.hora_duracion', 'curso.total_clases', 'curso.precio', 'c.categoria', 'curso.fecha_inicio', 'curso.fecha_final', 'curso.modalidad', 'curso.plataforma')
            ->where('curso.estado', '=', 2)
            ->where('curso.tipo', '=', 1)
            ->orderBy('idcurso', 'desc')
            ->limit(6)
            ->get();
        return $cursos;
    }

    public function listCategoriasWeb()
    {
        $categorias = DB::table('categoria')
            ->orderBy('idcategoria', 'desc')
            ->where('estado', '=', 1)
            ->get();
        
        return $categorias;
    }

    public function buscarCurso(Request $request)
    {
        $cursos = Curso::where('titulo', 'like', "{$request->input('search')}%")->limit(5)->get();
        $data   = array();
        foreach ($cursos as $curso) {
            $data[] = array(
                "idcurso" => $curso->idcurso,
                "curso" => $curso->titulo
            );
        }
        return \json_encode($data);
    }

    #------ CURSO DETALLE :
    public function showCursoDetalleId($id)
    {
        $cursoId     = $this->showCursoId($id);
        $cursoTema   = $this->listCursoTemas($id);
        $requisitos  = $this->listCursoRequitos($id);
        $estudiantes = $this->listCursoComunidad($id);
        $secioClase  = $this->listSeccionClases($id);
        $docentes    = $this->listDocentesCurso($id);
        $cursos      =  $this->listCursosWeb();
        $categorias  =  $this->listCategoriasWeb();

        if (!empty($cursoId) && !empty($cursoTema) && !empty($requisitos) && (!empty($estudiantes) || !empty($docentes))) {
            return view('cursoDetalle')->with([
                'cursoId'     => $cursoId,
                'cursoTemas'  => $cursoTema,
                'requisitos'  => $requisitos,
                'estudiantes' => $estudiantes,
                'secioClase'  => $secioClase,
                'docentes'    => $docentes,
                'cursos'      => $cursos,
                'categorias'  => $categorias,
            ]);
        } else {
            return redirect('/');
        }
    }

    public function showCursoId($id)
    {
        $cursoId = DB::table('curso')
            ->join('categoria as c', 'c.idcategoria', '=', 'curso.idcategoria')
            ->select('curso.idcurso', 'curso.titulo', 'curso.portada', 'curso.url_portada_det', 'curso.plan', 'curso.descripcion', 'curso.descripcion_larga', 'curso.hora_duracion', 'curso.total_clases', 'curso.precio', 'c.categoria', 'curso.fecha_inicio', 'curso.fecha_final', 'curso.modalidad', 'curso.plataforma', 'curso.brochure', 'curso.url_video_intro')
            ->where('curso.idcurso', '=', $id)
            ->first();

        return $cursoId;
    }

    #¿Qué aprenderás?
    public function listCursoTemas($id)
    {
        $cursoTema = DB::table('curso_temas')->where('idcurso', '=', $id)->where('estado', '=', 1)->get();
        return $cursoTema;
    }

    #Requisitos
    public function listCursoRequitos($id)
    {
        $requisitos = DB::table('requisitos')->where('idcurso', '=', $id)->where('estado','=',1)->get();
        return $requisitos;
    }

    #¿Para quien va dirigido este curso?
    public function listCursoComunidad($id)
    {
        $estudiantes = DB::table('comunidad_estudiante')->where('idcurso', '=', $id)->where('estado','=',1)->get();
        return $estudiantes;
    }

    #Plan de estudios | Clases por ID SECCIÓN con estado 1 (habilitado)
    public function listSeccionClases($id)
    {
        $secciones = DB::table('seccion')->where('idcurso', '=', $id)->where('estado', '=', 1)->get();
        
        $data = array();

        foreach ($secciones as $seccion) {
            $clases = DB::table('clase')->where('idseccion', '=', $seccion->idseccion)
                ->where('estado', '=', 1)->get();

            $data[] = array(
                "idseccion" => $seccion->idseccion,
                "titulo"    => $seccion->titulo,
                "cantidad"  => count($clases),
                "clases"    => $clases,
            );
        }

        return ($data);
    }

    #Docentes del curso
    public function listDocentesCurso($id)
    {
        $docentes = DB::table('curso_docente_usuario')
            ->join('users as u', 'u.idusuario', '=', 'curso_docente_usuario.idusuario')
            ->join('curso as c', 'c.idcurso', '=', 'curso_docente_usuario.idcurso')
            ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
            ->select('p.nombre', 'p.apellidos', 'p.tipo_persona', 'p.telefono', 'p.foto', 'p.direccion', 'p.carrera', 'p.perfil', 'p.experiencia_laboral')
            ->where('c.idcurso', '=', $id)
            ->where('curso_docente_usuario.estado', '=', 1)
            ->get();
        return $docentes;
    }

    public function listPreciosWeb()
    {
        $precios = DB::table('curso')
            ->orderBy('idcurso', 'asc')
            ->select('precio')
            ->where('estado', '=', 1)
            ->where('tipo', '=', 1)
            ->distinct()
            ->get();
        return view('admin')->with([
            'categorias' => $categorias
        ]);
    }

    public function listDocentesWeb()
    {
        $docentes = DB::table('persona')
            ->orderBy('idpersona', 'desc')
            ->where('tipo_persona', '=', 'docente')
            ->limit(10)
            ->get();
        return view('admin')->with([
            'cursos' => $docentes
        ]);
    }

    #------ CURSO - WEB -------#
    public function listCursoMain($idcategoria = '')
    {
        $cursos     = $this->listCurso();
        $categorias = $this->listCategoriasWeb();

        return view('curso')->with([
            'cursos'     => $cursos,
            'categorias' => $categorias,
        ]);
    }

    public function listCurso()
    {
        $cursos = DB::table('curso')
            ->join('categoria as c', 'c.idcategoria', '=', 'curso.idcategoria')
            ->select('curso.idcurso', 'curso.titulo', 'curso.portada', 'curso.plan', 'curso.descripcion', 'curso.hora_duracion', 'curso.total_clases', 'curso.precio', 'c.categoria', 'curso.fecha_inicio', 'curso.fecha_final', 'curso.modalidad', 'curso.plataforma')
            ->where('curso.estado', '=', 2)
            ->where('curso.tipo', '=', 1)
            ->orderBy('idcurso', 'desc')
            ->paginate(6);
        return $cursos;
    }
    
    #
    #
    #-------------- METODOS Y FUNCIONES PARA LA WEB ESTUDIANTE - LOGUEADO -------------------------
    #
    #
    
    public function ListCursosComprados(Request $request)
    {
        $dataResponse   = [];
        $data           = array();
        $query          = trim($request->get('search'));
        $idrol          = Auth::user()->idrol;
        $idusuario      = Auth::user()->idusuario;

        if ($idrol == 2) {
            #LISTA PARA EL DESPEGABLE DEL HEADER
            $miscursos_header = DB::table('venta')
                ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
                ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
                ->select('c.idcurso', 'c.titulo', 'c.portada', 'c.descripcion', 'c.hora_duracion', 'c.total_clases')
                ->where([['venta.idusuario', '=', $idusuario], ['venta.estado', '=', 1]])
                ->distinct()->limit(3)->get();

            if ($query != "") {

                #LISTA DE CURSO SI EN CASO ENVIAN PARAMETROS DE POR EL BUSCADOR
                $miscursos = DB::table('venta')
                    ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
                    ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
                    ->select('c.idcurso', 'c.titulo', 'c.portada', 'c.descripcion', 'c.hora_duracion', 'c.total_clases')
                    ->where([['venta.idusuario', '=', $idusuario], ['venta.estado', '=', 1]])
                    ->where('c.titulo', 'LIKE', '%' . $query . '%')
                    ->distinct()->get();

                foreach ($miscursos as $micurso) {
                    #LISTA DE DOCENTES QUE ENSEÑARÁN UN DETERMINADO CURSO
                    $docentes = DB::table('curso_docente_usuario')
                        ->join('users as u', 'u.idusuario', '=', 'curso_docente_usuario.idusuario')
                        ->join('curso as c', 'c.idcurso', '=', 'curso_docente_usuario.idcurso')
                        ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
                        ->select('p.nombre', 'p.apellidos', 'p.tipo_persona')->where('c.idcurso', '=', $micurso->idcurso)->get();

                    $certificado = DB::table('certificados')->where([['idcurso', '=', $micurso->idcurso], ['idusuario', '=', $idusuario]])->first();
                    if ($certificado) {
                        $certificado = $certificado->url;
                    } else {
                        $certificado = 0;
                    }

                    $data[] = array(
                        "idcurso"       => $micurso->idcurso,
                        "titulo"        => $micurso->titulo,
                        "portada"       => $micurso->portada,
                        "descripcion"   => $micurso->descripcion,
                        "hora_duracion" => $micurso->hora_duracion,
                        "total_clases"  => $micurso->total_clases,
                        "docentes"      => $docentes,
                        "certificado"   => $certificado
                    );
                }
                return view('web.misCursos')->with([
                    'miscursos'         => $data,
                    'miscursos_header'  => $miscursos_header,
                    'search'            => $query
                ]);
            } else {
                #CURSOS COMPRADOS
                $miscursos = DB::table('venta')
                    ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
                    ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
                    ->select('c.idcurso', 'c.titulo', 'c.portada', 'c.descripcion', 'c.hora_duracion', 'c.total_clases')
                    ->where([['venta.idusuario', '=', $idusuario], ['venta.estado', '=', 1]])->distinct()->get();

                //dd($miscursos);
                foreach ($miscursos as $micurso) {

                    $docentes = DB::table('curso_docente_usuario')
                        ->join('users as u', 'u.idusuario', '=', 'curso_docente_usuario.idusuario')
                        ->join('curso as c', 'c.idcurso', '=', 'curso_docente_usuario.idcurso')
                        ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
                        ->select('p.nombre', 'p.apellidos', 'p.tipo_persona')->where('c.idcurso', '=', $micurso->idcurso)->get();

                    $certificado = DB::table('certificados')->where([['idcurso', '=', $micurso->idcurso], ['idusuario', '=', $idusuario]])->first();
                    if ($certificado) {
                        $certificado = $certificado->url;
                    } else {
                        $certificado = 0;
                    }

                    $data[] = array(
                        "idcurso"       => $micurso->idcurso,
                        "titulo"        => $micurso->titulo,
                        "portada"       => $micurso->portada,
                        "descripcion"   => $micurso->descripcion,
                        "hora_duracion" => $micurso->hora_duracion,
                        "total_clases"  => $micurso->total_clases,
                        "docentes"      => $docentes,
                        "certificado"   => $certificado
                    );
                }
                return view('web.misCursos')->with([
                    'miscursos'         => $data,
                    'miscursos_header'  => $miscursos_header,
                    'search'            => $query
                ]);
            }
        } else if ($idrol == 1) {
            $cursosDocente_header = DB::table('curso_docente_usuario')
                ->join('curso as c', 'c.idcurso', '=', 'curso_docente_usuario.idcurso')
                ->join('users as u', 'u.idusuario', '=', 'curso_docente_usuario.idusuario')
                ->select('c.idcurso', 'c.titulo', 'c.portada')
                ->where('curso_docente_usuario.idusuario', '=', $idusuario)->groupBy('c.idcurso', 'c.titulo', 'c.portada')->limit(3)->get();

            if ($query != "") {
                $cursosDocente_search = DB::table('curso_docente_usuario')
                    ->join('curso as c', 'c.idcurso', '=', 'curso_docente_usuario.idcurso')
                    ->join('users as u', 'u.idusuario', '=', 'curso_docente_usuario.idusuario')
                    ->select('c.idcurso', 'c.titulo', 'c.portada')
                    ->where([['curso_docente_usuario.idusuario', '=', $idusuario], ['c.titulo', 'LIKE', '%' . $query . '%']])->groupBy('curso_docente_usuario.idcurso')->get();
            } else {
                $cursosDocente_search = DB::table('curso_docente_usuario')
                    ->join('curso as c', 'c.idcurso', '=', 'curso_docente_usuario.idcurso')
                    ->join('users as u', 'u.idusuario', '=', 'curso_docente_usuario.idusuario')
                    ->select('c.idcurso', 'c.titulo', 'c.portada')
                    ->where('curso_docente_usuario.idusuario', '=', $idusuario)->groupBy('c.idcurso', 'c.titulo', 'c.portada')->limit(3)->get();
            }

            return view('web.misCursos')->with([
                'miscursos'         => $cursosDocente_search,
                'miscursos_header'  => $cursosDocente_header,
                'src'               => count($cursosDocente_search),
                'search'            => $query,
            ]);
        } else {
            return redirect()->route('cerrarSesion');
        }
    }

    public function listMiAprendizaje($idcurso)
    {
        $venta = DB::table('venta')->where([['idcurso', '=', $idcurso], ['idusuario', '=', Auth::user()->idusuario]])->first();
        if (isset($venta)) {
            $idcurso    = $idcurso;
            $idusuario  = Auth::user()->idusuario;
            $curso      = DB::table('curso')->where('idcurso', '=', $idcurso)->first();
            $secciones  = DB::table('seccion')
            ->join('curso as c', 'c.idcurso', '=', 'seccion.idcurso')
            ->select('seccion.idseccion', 'c.idcurso', 'seccion.nombre_seccion', 'seccion.titulo', 'seccion.entregable')
            ->where([['seccion.idcurso', '=', $idcurso], ['seccion.estado', '=', 1]])->distinct()->get();
            
            $clases     = array();
            $claseVista = DB::table('clases_vistas')->where('idusuario', '=', $idusuario)->get();
            $usuario_curso_calificado = calificacion_curso::where('idcurso',$idcurso)->where('idusuario',$idusuario)->first();
            

            foreach ($secciones as $seccion) { //['url_video', '!=', NULL]
                $clase = DB::table('clase')->where([['idseccion', '=', $seccion->idseccion], ['estado', '=', 1]])->get();
                $examen = DB::table('examen')
                    ->join('seccion as s', 's.idseccion', '=', 'examen.idseccion')
                    ->select('examen.idexamen', 'examen.idseccion', 'examen.titulo')
                    ->where([['examen.idcurso', '=', $idcurso], ['examen.idseccion', '=', $seccion->idseccion], ['examen.estado', '=', '1']])->get();

                $dataVista = array();
                foreach ($clase as $claseView) {
                    $var_visto = 0;
                    foreach ($claseVista as $value) {
                        if ($value->idclase == $claseView->idclase) {
                            $var_visto = 1;
                        }
                    }
                    $dataVista[] = array(
                        "idclase"       => $claseView->idclase,
                        "idseccion"     => $claseView->idseccion,
                        "entregable"    => $seccion->entregable,
                        "titulo"        => $claseView->titulo,
                        "minutos_video" => $claseView->minutos_video,
                        "url_video"     => $claseView->url_video,
                        "visto"         => $var_visto,
                    );
                }

                $varExamenEntreg = 0;
                $idexamen        = 0;
                foreach ($examen as $examenItem) {
                    if ($examenItem->idseccion == $seccion->idseccion) {
                        $varExamenEntreg = 1;
                        $idexamen   = $examenItem->idexamen;
                    }
                }

                $varProyFinal   = 0;
                if ($seccion->entregable == 3) {
                    $varProyFinal = 3;
                }

                //dd($dataVista);
                $clases[] = array(
                    "idseccion"     => $seccion->idseccion,
                    "titulo"        => $seccion->titulo,
                    "examen"        => $varExamenEntreg,
                    "idexamen"      => $idexamen,
                    "nom_modulo"    => $seccion->nombre_seccion,
                    "proyectoFinal" => $varProyFinal,
                    "cant_clases"   => count($clase),
                    "clases"        => $dataVista,
                );
            }
            //return \json_encode($clases);
            return view('web.miAprendizaje', compact('clases', 'curso', 'idcurso', 'usuario_curso_calificado'))->render();
        } else {
            return redirect()->route('miscursos');
        }
    }

    public function listRecursosSeccion($idcurso, $idclase)
    {
        $idcurso    = $idcurso;
        $idusuario  = Auth::user()->idusuario;
        $curso      = DB::table('curso')->where('idcurso', '=', $idcurso)->first();
        $miclase    = DB::table('clase')->where('idclase', '=', $idclase)->first();
        $usuario_curso_calificado = calificacion_curso::where('idcurso',$idcurso)->where('idusuario',$idusuario)->first();

        $secciones  = DB::table('seccion')
            ->join('curso as c', 'c.idcurso', '=', 'seccion.idcurso')
            ->select('seccion.idseccion', 'c.idcurso', 'seccion.nombre_seccion', 'seccion.titulo', 'seccion.entregable')
            ->where('seccion.idcurso', '=', $idcurso)->distinct()->get();

        $comentarios = DB::table('comentario')
            ->join('users as u', 'u.idusuario', '=', 'comentario.idusuario')
            ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
            ->select('comentario.idcomentario', 'comentario.comentario', 'comentario.fecha', 'p.nombre', 'p.apellidos')
            ->where([['idclase', '=', $idclase], ['idrespuesta', '=', NULL]])->get();
        $recursos = DB::table('recurso')->where('idclase', '=', $idclase)->get();

        $clases     = array();

        $claseVista = DB::table('clases_vistas')->where('idusuario', '=', $idusuario)->get();

        foreach ($secciones as $seccion) { //['url_video', '!=', NULL]
            $clase = DB::table('clase')->where([['idseccion', '=', $seccion->idseccion], ['estado', '=', 1]])->get();
            $examen = DB::table('examen')
                ->join('seccion as s', 's.idseccion', '=', 'examen.idseccion')
                ->select('examen.idexamen', 'examen.idseccion', 'examen.titulo')
                ->where([['examen.idcurso', '=', $idcurso], ['examen.idseccion', '=', $seccion->idseccion], ['examen.estado', '=', '1']])->get();
            $dataVista = array();
            foreach ($clase as $claseView) {
                $var_visto = 0;
                foreach ($claseVista as $value) {
                    if ($value->idclase == $claseView->idclase) {
                        $var_visto      = 1;
                    }
                }
                $dataVista[] = array(
                    "idclase"       => $claseView->idclase,
                    "idseccion"     => $claseView->idseccion,
                    "entregable"    => $seccion->entregable,
                    "titulo"        => $claseView->titulo,
                    "minutos_video" => $claseView->minutos_video,
                    "url_video"     => $claseView->url_video,
                    "visto"         => $var_visto,
                );
            }

            //dd($dataVista);
            $varExamenEntreg = 0;
            $idexamen        = 0;
            foreach ($examen as $examenItem) {
                if ($examenItem->idseccion == $seccion->idseccion) {
                    $varExamenEntreg = 1;
                    $idexamen   = $examenItem->idexamen;
                }
            }

            //dd($dataVista);
            $clases[] = array(
                "idseccion"     => $seccion->idseccion,
                "titulo"        => $seccion->titulo,
                "examen"        => $varExamenEntreg,
                "idexamen"      => $idexamen,
                "nom_modulo"    => $seccion->nombre_seccion,
                "cant_clases"   => count($clase),
                "clases"        => $dataVista,
            );
        }
        //return \json_encode($clases);
        return view('web.htmlDetCurso', compact('clases', 'curso', 'miclase', 'comentarios', 'idcurso', 'recursos', 'usuario_curso_calificado'))->render();
    }

    public function listRespuestasPreg($idcomentario)
    {
        $comentario = DB::table('comentario')
            ->join('users as u', 'u.idusuario', '=', 'comentario.idusuario')
            ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
            ->select('comentario.idcomentario', 'comentario.comentario', 'comentario.idclase', 'comentario.fecha', 'p.nombre', 'p.apellidos', 'p.tipo_persona')
            ->where([['idcomentario', '=', $idcomentario], ['idrespuesta', '=', NULL]])->first();

        $respuestas = DB::table('comentario')
            ->join('users as u', 'u.idusuario', '=', 'comentario.idusuario')
            ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
            ->select('comentario.idcomentario', 'comentario.comentario', 'comentario.fecha', 'p.nombre', 'p.apellidos', 'p.tipo_persona')
            ->where([['idrespuesta', '=', $idcomentario]])->get();

        return view('web.htmlRespuestas', compact('comentario', 'respuestas'))->render();
    }

    public function nuevoComentario(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $idusuario = Auth::user()->idusuario;
        $coment = DB::table('comentario')->insert([
            'fecha'      => date("Y/m/d H:i:s"),
            'comentario' => $request->input('newpregunta'),
            'idclase'    => $request->input('idclase'),
            'idusuario'  => $idusuario,
        ]);
        return json_encode($coment);
    }

    public function nuevaRespuesta(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $idusuario  = Auth::user()->idusuario;
        $respt      = DB::table('comentario')->insert([
            'fecha'       => date("Y/m/d H:i:s"),
            'comentario'  => $request->input('nuevarespuesta'),
            'idclase'     => $request->input('idclase'),
            'idusuario'   => $idusuario,
            'idrespuesta' => $request->input('idcomentario'),
        ]);
        return json_encode($respt);
    }

    public function indexRecursoClase()
    {
        $cursos     = DB::table('curso')->whereIn('estado', [1,2])->distinct()->get();
        $secciones  = DB::table('seccion')->where('estado', '=', 1)->distinct()->get();
        $clases     = DB::table('clase')->where('estado', '=', 1)->distinct()->get();

        return view('admin.arch-clases.list', compact('cursos', 'secciones', 'clases'));
    }

    public function listmodulos($idcurso)
    {
        $secciones  = DB::table('seccion')->where([['estado', '=', 1], ['idcurso', '=', $idcurso]])->distinct()->get();
        return \json_encode($secciones);
    }

    public function listclases($idseccion)
    {
        $clases  = DB::table('clase')->where([['estado', '=', 1], ['idseccion', '=', $idseccion]])->distinct()->get();
        return \json_encode($clases);
    }

    public function listrecursosclases($idseccion, $idclase)
    {
        $recursoClase  = DB::table('recurso')->where([['idclase', '=', $idclase], ['idusuario', '=', null]])->distinct()->get();
        $autoi = 1;
        $data = array();
        foreach ($recursoClase as $clase) {
            $archivo = ($clase->archivo == null) ? $clase->url : '/storage/archivos/' . $clase->archivo;
            $data[] = array(
                "autoi"     => $autoi++,
                "nombre"    => $clase->nombre,
                "archivo"   => $archivo,
                "idrecurso" => $clase->idrecurso,
            );
        }
        return \json_encode(array('data' => $data));
    }

    public function nuevoArchivo(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $idrecurso          = $request->input('idrecurso');

        if ($idrecurso == "" || $idrecurso == null) {
            $nvo_nombre_archivo = null;
            if ($request->hasFile('archivo')) {
                $archivo            = $request->file('archivo');
                $nombre_archivo     = $archivo->getClientOriginalName();
                $extension          = explode(".", $nombre_archivo);
                $nvo_nombre_archivo = round(microtime(true)) . '.' . end($extension);

                if (!Storage::disk('public')->exists('archivos')) {
                    Storage::makeDirectory('public/archivos', 0775, true);
                }
                \Storage::disk('public')->put("archivos/" . $nvo_nombre_archivo, \File::get($archivo));
            }

            $respt = DB::table('recurso')->insert([
                'fecha'     => date("Y/m/d H:i:s"),
                'idclase'   => $request->input('idclase'),
                'nombre'    => $request->input('titulo_archivo'),
                'url'       => $request->input('url'),
                'archivo'   => $nvo_nombre_archivo,
            ]);
            return json_encode($respt);
        } else {
            $nvo_archivo = $request->input('archivo_antiguo');
            if ($request->hasFile('archivo')) {
                $archivo            = $request->file('archivo');
                $nombre_archivo     = $archivo->getClientOriginalName();
                $extension          = explode(".", $nombre_archivo);
                $nvo_archivo        = round(microtime(true)) . '.' . end($extension);

                if (!Storage::disk('public')->exists('archivos')) {
                    Storage::makeDirectory('public/archivos', 0775, true);
                }
                \Storage::disk('public')->put("archivos/" . $nvo_archivo, \File::get($archivo));
            }
            $respt = DB::table('recurso')->where('idrecurso', $idrecurso)->update([
                'fecha'     => date("Y/m/d H:i:s"),
                'idclase'   => $request->input('idclase'),
                'nombre'    => $request->input('titulo_archivo'),
                'url'       => $request->input('url'),
                'archivo'   => $nvo_archivo,
            ]);
            return json_encode(($respt == 1) ? true : false);
        }
    }

    public function mostrararchivo($idrecurso)
    {
        $archivo = DB::table('recurso')->where('idrecurso', '=', $idrecurso)->first();
        return \json_encode($archivo);
    }

    public function elimararch($idrecurso)
    {
        $recurso = DB::table('recurso')->where([['idrecurso', '=', $idrecurso]])->delete();
        return \json_encode($recurso);
    }

    public function elimarArchivo($idrecurso, $idclase)
    {
        $archivo  = DB::table('recurso')->where([['idrecurso', '=', $idrecurso], ['idclase', '=', $idclase]])->delete();
        return $archivo;
    }

    public function checkSesionVista(Request $request)
    {
        $idusuario  = Auth::user()->idusuario;
        $idclase    = $request->input('idclase');

        //if ($this->validarCheck( $idusuario, $idclase) == 'false') {
        DB::table('clases_vistas')->where([['idusuario', '=', $idusuario], ['idclase', '=', $idclase]])->delete();
        $respt          = DB::table('clases_vistas')->insert([
            'idusuario' => $idusuario,
            'idclase'   => $request->input('idclase')
        ]);
        return json_encode($respt);
        //}        
    }

    public function validarCheck($idusuario, $idclase)
    {
        $check = DB::table('clases_vistas')->where([['idusuario', '=', $idusuario], ['idclase', '=', $idclase]])->first();
        if (!empty($check)) {
            $check = 'true';
        } else {
            $check = 'false';
        }
        return $check;
    }

    public function ultimaClaseVista($id)
    {
        $idusuario  = Auth::user()->idusuario;
        $claseVista = DB::table('clases_vistas')->where([['idusuario', '=', $idusuario]])->orderBy('idclases_vistas', 'DESC')->first();
        if (!empty($claseVista)) {
            return \json_encode(array("visto" => $claseVista));
        } else {
            //VALIDAR SI VIENE ALGUNA SECCION, SINO REDIRIGIR AL INICIO MIS CURSOS DICIENDO QUE AUN NO ESTA CONFUGUEAFI
            $curso    = DB::table('venta')->where([['idusuario', '=', $idusuario], ['idcurso', '=', $id]])->first();
            $seccion  = DB::table('seccion')->where([['estado', '=', 1], ['idcurso', '=', $id]])->orderBy('idseccion', 'ASC')->first();

            if ($seccion) {
                $clase    = DB::table('clase')->where([['estado', '=', 1], ['idseccion', '=', $seccion->idseccion]])->orderBy('idclase', 'ASC')->first();
                $response = [
                    "estado"  => "true",
                    "idclase" => $clase->idclase
                ];
            } else {
                $response = [
                    "estado"  => "false",
                    "texto"   => "Aún no se tiene clases registradas en este curso, por favor comunicarse con la administración."
                ];
            }

            return \json_encode($response);
        }
    }

    public function calificacion_curso($idcurso)
    {
        $idcurso    = $idcurso;
        $idusuario  = Auth::user()->idusuario;
        $idpersona  = Auth::user()->idpersona;
        $curso      = DB::table('curso')->where('idcurso', '=', $idcurso)->first();
        
        $nombre   = Persona::select(DB::raw('CONCAT(nombre, apellidos) AS nombreusuario'))->where('idpersona', '=', $idpersona)->first();
        return view('web.calificacion_curso', compact('curso', 'nombre', 'idusuario'))->render();
    }
    public function store_calificacion_curso(Request $request)
    {
        $request->validate([
            'idusuario' => 'required',
            'idcurso' => 'required',
            'q1value' => 'required',
            'q2value' => 'required',
            'q3value' => 'required',
            'q4value' => 'required',
            'q5value' => 'required',
            'q6value' => 'required',
            'q7value' => 'required',
            'q8value' => 'required',
            'q9value' => 'required',
            'q10value' => 'required',
        ]);
        $data = new calificacion_curso;
        $data->idcurso = $request->input('idcurso');
        $data->idusuario = $request->input('idusuario');
        $data->pregunta1 = $request->input('q1value');
        $data->pregunta2 = $request->input('q2value');
        $data->pregunta3 = $request->input('q3value');
        $data->pregunta4 = $request->input('q4value');
        $data->pregunta5 = $request->input('q5value');
        $data->pregunta6 = $request->input('q6value');
        $data->pregunta7 = $request->input('q7value');
        $data->pregunta8 = $request->input('q8value');
        $data->pregunta9 = $request->input('q9value');
        $data->pregunta10 = $request->input('q10value');
        $data->promedioCalificacion = ($request->input('q1value')+
        $request->input('q2value')+$request->input('q3value')+$request->input('q4value')
        +$request->input('q5value')+$request->input('q7value')+$request->input('q8value')
        +$request->input('q9value')+$request->input('q10value'))/9;
        $data->save();
        return redirect()->route('miscursos');
    }
}