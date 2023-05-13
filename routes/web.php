<?php

use App\Http\Controllers\CoursesController;
use Illuminate\Support\Facades\Route;

use App\Mail\ConfirmacionCursoMailable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Mailable;

use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('/main', function () {
    $msg = ["curso"=>"Redacción de articulos cientificos"];
    $correo = new ConfirmacionCursoMailable($msg);
    Mail::to('jeisonelisanchez@gmail.com')->send($correo);
    return "ok";
    //return view('admin.inicio.email_confirmacion');
});

/** Inicio **/
Route::get('/', 'CursoController@listInicio')->name('inicio');
Route::get('buscarCurso/', 'CursoController@buscarCurso')->name('buscar_curso');
Route::post('sendmensaje/', 'MensajesController@registrarMensaje')->name('sendmensaje');

/** Curso detalle **/
Route::get('curso/{id}/', 'CursoController@showCursoDetalleId')->name('cursoid');

/*Pagos Online*/
Route::get('checkout/{id}/', 'PagarOnlineController@cursoPagarId')->name('checkout');
Route::post('registrarcliente/', 'PagarOnlineController@registrarCliente')->name('registrarcliente');
Route::post('validarCheckout/', 'PagarOnlineController@validarLoginCheckout')->name('validarLoginCheckout');
Route::get('registrarme/{id}', 'PagarOnlineController@registrarmeCheckout')->name('registrarmeCheckout');

Route::get('pasarelapago/', 'PagarOnlineController@pasarelaPagoRegistro')->name('pasarelapago');
Route::post('registrarpreventa/', 'PagarOnlineController@registrarpreventa')->name('registrarpreventa');
Route::get('pasarelapagocheckout/', 'PagarOnlineController@pasarelaPagoCheckout')->name('pasarelapagocheckout');

Route::get('subirVoucher/', 'PagarOnlineController@indexSubirVoucher')->name('subirVoucher')->middleware('auth');
Route::get('purchaseCompleted/', 'PagarOnlineController@purchaseCompleted')->name('purchaseCompleted');
Route::post('purchaseRefused/', 'PagarOnlineController@purchaseRefused')->name('purchaseRefused');

//Route::post('registrarpago', 'PagarOnlineController@registrarPago')->name('registrarpago');
Route::post('registrarVoucher', 'PagarOnlineController@registrarVoucher')->name('registrarVoucher')->middleware('auth');
Route::post('registrarCard', 'PagarOnlineController@registrarCard')->name('registrarCard');

/** Lista de todos los cursos **/
Route::get('cursos/{idcategoria?}/', 'CursoController@listCursoMain')->name('cursos');

/** Nosotros **/
Route::get('contactanos/', function () {
    return view('contactanos');
});

/* Certificados */
//Route::get('certificados', 'CertificacionController@indexCertificadosWeb')->name('certificados');
Route::get('certificados/', function () {
    return view('certificados');
});

/** USUARIOS LOGUEADOS EN EL SISTEMA **/
Route::post('login', 'LoginController@login')->name('login');
Route::get('login/cerrarSesion', 'LoginController@logout')->name('cerrarSesion');
Route::post('login/cerrarSesion', 'LoginController@logout')->name('cerrarSesionPost');

Route::post('persona/cambiarclave', 'PersonaController@cambiarContrasenia')->name('cambiarclave')->middleware('auth');
Route::get('perfil/', 'PersonaController@verperfil')->name('perfil')->middleware('auth');
Route::post('persona/actualizarperfil', 'PersonaController@actualizarperfil')->name('actualizarperfil')->middleware('auth');

Route::get('miscursos/', 'CursoController@ListCursosComprados')->name('miscursos')->middleware('auth');
Route::get('miaprendizaje/{id}/', 'CursoController@listMiAprendizaje')->name('miaprendizaje')->middleware('auth');
Route::get('miaprendizaje/calificacion/{id}/', 'CursoController@calificacion_curso')->name('calificacion_curso')->middleware('auth');
Route::post('store_calificacion_curso/', 'CursoController@store_calificacion_curso')->name('store_calificacion_curso')->middleware('auth');

/** TAREAS DEL ESTUDIANTE, SUBIR TAREAS Y VER SUS NOTAS **/
Route::get('mistareas/{idcurso}/{idseccion}', 'RevisarTareaController@misTareas')->name('misTareas')->middleware('auth');
Route::post('mistareas', 'RevisarTareaController@registrarTarea')->name('registrarTarea')->middleware('auth');
Route::get('eliminartarea/{identregable}', 'RevisarTareaController@elimarTarea')->name('elimarTarea')->middleware('auth');

/** Proyecto final del alumno **/ 
Route::get('proyectofinal/{idcurso}/{idseccion}', 'RevisarTareaController@proyectoFinal')->name('proyectofinal')->middleware('auth');
Route::post('proyectofinal', 'RevisarTareaController@registrarProyFinal')->name('registrarProyFinal')->middleware('auth');
Route::get('elimarProyFinal/{identregable}', 'RevisarTareaController@elimarTarea')->name('elimarProyFinal')->middleware('auth');
/** FIN TARREAS **/

/** REVISAR TAREAS PROFESOR **/
Route::get('revisartarea/{idcurso}', 'RevisarTareaController@indexRevisarTarea')->name('revisarTarea')->middleware('auth');
Route::get('estudiantes/{idcurso}/{idseccion}/{idusuario?}', 'RevisarTareaController@listaEstudiantes')->name('listaEstudiantes')->middleware('auth');
Route::get('estpaginate/{idcurso}/{idseccion}', 'RevisarTareaController@listEstudiantesPaginate')->name('listaEstPag')->middleware('auth');
Route::get('listartareasest/{idcurso}/{idseccion}/{idusuario}', 'RevisarTareaController@ListaTareaEstudiante')->middleware('auth');
Route::post('evaluarTarea', 'RevisarTareaController@evaluarTarea')->name('evaluarTarea')->middleware('auth');

/** Revisión del proyecto final del curso **/
Route::get('revisarproyecto/{idcurso}', 'RevisarTareaController@indexProyFinal')->name('revisarproyecto')->middleware('auth');
Route::get('listaproyectopag/{idcurso}', 'RevisarTareaController@listaProyFinalPaginate')->middleware('auth');
Route::post('revisarproyecto', 'RevisarTareaController@revisarProyCurso')->middleware('auth');

/* -------------------------------------------------------------------------------------------------------- */

/** DESCARGAR ARCHIVOS POR NOMBRE Y APELIDOS **/
Route::get('descargar/', 'RevisarTareaController@DescargarArchivo')->name('DescargarArchivo')->middleware('auth');

/* CERTIFICACIONES - SUBIR PDF */
Route::get('admin/certificacion/', 'CertificacionController@index')->name('admin_index_certificacion')->middleware('auth');
Route::get('admin/certificacion/{idcurso}', 'CertificacionController@indexCertificado')->name('admin_certificacionId')->middleware('auth');
Route::get('admin/tablaPagCertificados/', 'CertificacionController@tablaPagCertificados')->middleware('auth');
Route::post('guardarcertificado', 'CertificacionController@agregarCertificado')->name('certificado_guardar')->middleware('auth');
/* FIN */

/* INICIO MALLA CURRICULAR */
Route::get('admin/macurricular/create', 'MaCurricularController@create')->name('admin_create_macurricular')->middleware('auth');
Route::post('admin/macurricular/store', 'MaCurricularController@store')->name('admin_store_macurricular')->middleware('auth');
Route::get('admin/macurricular/show/{id}', 'MaCurricularController@show')->name('admin_show_macurricular')->middleware('auth');
Route::post('admin/macurricular/update/{id}', 'MaCurricularController@update')->name('admin_update_macurricular')->middleware('auth');
Route::get('admin/macurricular/delete/{id}', 'MaCurricularController@delete')->middleware('auth');
Route::get('admin/macurricular/', 'MaCurricularController@index')->name('admin_index_macurricular')->middleware('auth');
Route::get('admin/mallaCurPaginate', 'MaCurricularController@mallaCurPaginate')->middleware('auth');
/* FIN MALLA CURRICULAR */

/* SUSCRIPCIÓN AL CURSO : GRATIS */
Route::get('suscribirme/{idcurso}', 'SuscripcionCursoController@index')->name('index_suscribirme');
Route::post('suscribirme/{idcurso}', 'SuscripcionCursoController@suscribirme')->name('suscribirme');
Route::post('suscrbirnuevo/{idcurso}', 'SuscripcionCursoController@suscribirNuevo')->name('suscribir_nuevo');

Route::get('recursoseccion/{idcurso}/{idseccion}/', 'CursoController@listRecursosSeccion')->name('recursoseccion')->middleware('auth');
Route::get('respuestapreg/{idcomentario}/', 'CursoController@listRespuestasPreg')->name('respuestapreg')->middleware('auth');

Route::post('nuevoComentario/', 'CursoController@nuevoComentario')->name('nuevoComentario')->middleware('auth');
Route::post('nuevarespuesta/', 'CursoController@nuevaRespuesta')->name('nuevarespuesta')->middleware('auth');

Route::post('nuevoarchivo/', 'CursoController@nuevoArchivo')->name('nuevoarchivo')->middleware('auth');
Route::get('mostrararchivo/{idrecurso}', 'CursoController@mostrararchivo')->name('mostrararchivo')->middleware('auth');
Route::get('elimararch/{idrecurso}', 'CursoController@elimararch')->name('elimararch')->middleware('auth');
Route::get('elimarArchivo/{idrecurso}/{idclase}/', 'CursoController@elimarArchivo')->name('elimarArchivo')->middleware('auth');

Route::post('checkSesionVista/', 'CursoController@checkSesionVista')->name('checkSesionVista')->middleware('auth');
#
Route::get('ultimaClaseVista/{id?}', 'CursoController@ultimaClaseVista')->name('ultimaClaseVista')->middleware('auth');

/** SECCIÓN ADMINISTRATIVA **/
//->middleware('auth');

/** Inicio CURSO **/
Route::get('admin/courses', 'CoursesController@getAdmin')->name('admin_course_list')->middleware('auth');
Route::get('admin/courses/listar/{estado}', 'CoursesController@getListarCuorsesPaginate')->name('admin_course_list_paginate')->middleware('auth');
Route::get('admin/course/nuevo', 'CoursesController@getNuevo')->name('admin_course_nuevo')->middleware('auth');
Route::post('admin/course/nuevo/guardar', 'CoursesController@postGuardarCurso')->name('admin_course_nuevo_add')->middleware('auth');
Route::get('admin/course/editar/{id}', 'CoursesController@getEditar')->name('admin_course_edit')->middleware('auth');
Route::post('admin/course/nuevo/editar', 'CoursesController@postEditarCurso')->name('admin_course_nuevo_edit')->middleware('auth');
Route::get('admin/course/cambiarEstado/{id}/{estado}', 'CoursesController@getCambiarEstadoCurso')->name('change_status_course')->middleware('auth');
/** Fin CURSO **/

/** Inicio CURSO: Secciones **/
Route::get('admin/course/secciones/{idcurso}', 'CoursesController@getAgregarSecciones')->name('seccion_listar_agregar')->middleware('auth');
Route::post('admin/course/secciones/seccion/guardar', 'CoursesController@postGuardarSeccion')->name('seccion_guardar')->middleware('auth');
Route::get('admin/course/obtener/secciones/{idcurso}', 'CoursesController@obtenerSecciones')->name('seccion_obtener')->middleware('auth');
Route::get('admin/course/secciones/seccion/mostrar/{idclase}', 'CoursesController@getMostrarSeccion')->name('seccion_mostrar_editar')->middleware('auth');
Route::get('admin/course/secciones/seccion/cambiarEstadoSeccion/{idclase}/{estado}','CoursesController@getcambiarEstadoSeccion')->name('seccion_cambiar_estado')->middleware('auth');
/** Fin CURSO: Secciones **/

Route::get('admin/course/docentes/eliminar/{id}', 'CoursesController@getEliminarDocente')->middleware('auth');
Route::get('admin/course/estudiantes/{idCurso}', 'CoursesController@listarEstudiantes')->middleware('auth');
Route::get('admin/course/estudiantes/curso/lista', 'CoursesController@listarEstudiantesCurso')->middleware('auth');
Route::get('admin/course/estudiantes/notas/{idUser}/{idCurso}', 'CoursesController@listarNotasUsuario')->middleware('auth');
Route::get('admin/course/estudiantes/examenes/{idUser}', 'CoursesController@listarExamenUsuario')->middleware('auth');

/* ***************************************************************************** */

Route::get('admin/course/examen/{idCurso}', 'CoursesController@listarExamen')->middleware('auth');
Route::get('admin/course/obtener/examen/{idCurso}', 'CoursesController@obtenerExamen')->middleware('auth');
Route::get('admin/course/examen/agregar/{idCurso}', 'CoursesController@verFormExamen')->middleware('auth');
Route::get('admin/course/mostrar/examen/{idExam}', 'CoursesController@mostrarExamen')->middleware('auth');
Route::post('admin/course/examen/guardar', 'CoursesController@agregarExamen')->name('examen_guardar')->middleware('auth');
Route::get('admin/course/ver/examen/{idExam}', 'CoursesController@mostrarExamenCompleto')->middleware('auth');

Route::get('admin/notas/estudiantes/examen/{idExamen}', 'CoursesController@estudianteNotaExamen')->middleware('auth');
Route::get('ver/notas/estudiantes/examen', 'CoursesController@listaEstudianteNotaExamen')->middleware('auth');
// Route::get('admin/course/examen/resuelto/{idExamen}/{idUser?}', 'CoursesController@listarExamenUsuario');

/** Ruta para ver las resoluciones de examenes **/
Route::get('admin/course/estudiante/examen/{idCurso}/{idUser}', 'CoursesController@listarEstudianteResoluciones')->middleware('auth');
Route::get('admin/course/eliminar/examen/{idExam}', 'CoursesController@eliminarExamen')->middleware('auth');
Route::get('admin/course/cambiarEstadoExamen/examen/{idExam}/{estado}', 'CoursesController@cambiarEstadoExamen')->middleware('auth');

Route::get('admin/course/examen/preguntas/{idExam}', 'CoursesController@preguntasExamen')->middleware('auth');
Route::get('admin/course/eliminar/pregunta/{idpreg}', 'CoursesController@eliminarPregunta')->middleware('auth');
Route::post('admin/course/preguntas/examen/guardar', 'CoursesController@agregarPregunta')->name('preguntas_guardar')->middleware('auth');
Route::get('admin/courses/mostrar/pregunta/editar/{idPreg}', 'CoursesController@mostrarPregunta')->middleware('auth');
Route::get('admin/courses/mostrar/alternativas/{idPreg}', 'CoursesController@alternativasPregunta')->middleware('auth');

/* ******************************************************************************************** */

/** Clases de la SECCIÓN **/
Route::get('admin/course/secciones/clases/{idseccion}', 'CoursesController@getAgregarClases')->middleware('auth');
Route::get('admin/course/secciones/obtener/clases/{idseccion}', 'CoursesController@obtenerClases')->middleware('auth');
Route::post('admin/course/secciones/clases/guardar', 'CoursesController@postGuardarClase')->name('clase_guardar')->middleware('auth');
Route::get('admin/course/secciones/clases/mostrar/{idclase}', 'CoursesController@getMostrarClase')->middleware('auth');
//Route::get('admin/course/secciones/clases/eliminar/{idclase}', 'CoursesController@getEliminarClase')->middleware('auth');
Route::get('admin/course/secciones/clases/cambiarEstado/{idclase}/{estado}', 'CoursesController@getCambiarEstadoClase')->middleware('auth');

/** Requisitos del CURSO **/
Route::get('/admin/requisitos/{id}', 'CoursesController@requisitosIndex')->name('requisitosCursoId')->middleware('auth');
Route::get('/admin/obtener/requisitos/{id}', 'CoursesController@obtenerRequisitos')->name('obtenerRequisitosCursoId')->middleware('auth');
Route::post('/admin/requisitos', 'CoursesController@guardarEditarRequisitos')->name('guardEditarRequisitos')->middleware('auth');
Route::get('/admin/mostrarrequisitos/{id}', 'CoursesController@mostrarRequisitos')->middleware('auth');
// Route::get('/admin/eliminarrequisitos/{id}', 'CoursesController@eliminarRequisitos')->middleware('auth');
Route::get('/admin/cambiarEstadoRequisitos/{id}/{estado}', 'CoursesController@cambiarEstadoRequisitos')->middleware('auth');

/* Temas del CURSO */
Route::get('/admin/temas/{id}', 'CoursesController@temasIndex')->name('temasCursoId')->middleware('auth');
Route::get('/admin/obtener/temas/{id}', 'CoursesController@obtenerTemas')->name('obtenerTemasId')->middleware('auth');
Route::post('/admin/temas', 'CoursesController@guardarEditarTemas')->name('guardEditarTemas')->middleware('auth');
Route::get('/admin/mostrartemas/{id}', 'CoursesController@mostrarTemas')->middleware('auth');
Route::get('/admin/cambiarEstadoTemas/{id}/{estado}', 'CoursesController@cambiarEstadoTemas')->middleware('auth');

/* Comunidad del CURSO */
Route::get('/admin/comunidad/{id}', 'CoursesController@comunidadIndex')->name('comunidadCursoId')->middleware('auth');
Route::get('/admin/obtener/comunidad/{id}', 'CoursesController@obtenerComunidad')->name('obtenerComunidadId')->middleware('auth');
Route::post('/admin/comunidad', 'CoursesController@guardarEditarComunidad')->name('guardEditarComunidad')->middleware('auth');
Route::get('/admin/mostrarcomunidad/{id}', 'CoursesController@mostrarComunidad')->middleware('auth');
Route::get('/admin/cambiarEstadoComunidad/{id}/{estado}', 'CoursesController@cambiarEstadoComunidad')->middleware('auth');

/* Docentes del CURSO */
Route::get('/admin/docentes/{id}', 'CoursesController@docentesIndex')->name('docentesCursoId')->middleware('auth');
Route::get('/admin/obtener/docentes/{id}', 'CoursesController@obtenerDocentes')->name('obtenerDocentesCursoId')->middleware('auth');
Route::post('/admin/docentes', 'CoursesController@guardarEditarDocentes')->name('guardEditarDocentes')->middleware('auth');
Route::get('/admin/mostrardocentes/{id}', 'CoursesController@mostrarDocente')->middleware('auth');
//Route::get('/admin/eliminardocentes/{id}', 'CoursesController@eliminarDocente')->middleware('auth');
Route::get('/admin/cambiarEstadoDocente/{iddocente}/{estado}', 'CoursesController@cambiarEstadoDocente')->middleware('auth');

/* **************************************************************************** */

/* PERSONAS Y USUARIOS */
Route::get('admin/personas', 'PersonaController@index')->name('admin_personas')->middleware('auth');
//Route::get('admin/personas/list', 'PersonaController@list')->name('admin_list')->middleware('auth');

/*PAGINATE*/
Route::get('admin/personas/paginate', 'PersonaController@paginatePersona')->middleware('auth');
Route::get('admin/personas/create', 'PersonaController@create')->name('admin_personas_create')->middleware('auth');
Route::post('admin/personas/store', 'PersonaController@store')->name('admin_personas_store')->middleware('auth');
Route::get('admin/personas/edit/{id}', 'PersonaController@edit')->name('admin_personas_edit')->middleware('auth');
Route::patch('admin/personas/update/{id}', 'PersonaController@update')->name('admin_personas_update')->middleware('auth');
//Route::get('admin/personas/delete/{id}', 'PersonaController@destroy')->name('admin_personas_delete')->middleware('auth');
Route::get('admin/personas/cambiarEstado/{id}/{estado}', 'PersonaController@cambiarEstadoPersona')->name('admin_personas_cambiarEstado')->middleware('auth');

/*INICIO*/
Route::get('admin/inicio', 'InicioController@index')->name('admin_inicio')->middleware('auth');

Route::get('admin/inicio/listvoucher', 'InicioController@listPagosVoucher')->middleware('auth');
Route::get('admin/inicio/habventa/{idventa}', 'InicioController@habilitarVenta')->middleware('auth');
Route::get('admin/inicio/elimventa/{idventa}', 'InicioController@eliminarVenta')->middleware('auth');

/*ARCHIVOS DE CLASE SUBIDOS POR EL ADMIN*/
Route::get('admin/recursos-clase', 'CursoController@indexRecursoClase')->name('index_recurso_clase')->middleware('auth');
Route::post('admin/recurso-clase', 'CursoController@nuevoArchivo')->name('regisrecursosclase')->middleware('auth');
Route::get('admin/listmodulos/{idcurso}', 'CursoController@listmodulos')->name('listmodulos')->middleware('auth');
Route::get('admin/listsecciones/{idseccion}', 'CursoController@listSecciones')->name('listsecciones')->middleware('auth');
Route::get('admin/listclases/{idseccion}', 'CursoController@listclases')->name('listclases')->middleware('auth');
Route::get('admin/listrecursosclases/{idseccion}/{idclase}', 'CursoController@listrecursosclases')->name('listrecursosclases')->middleware('auth');

/*REPORTE PAGOS*/
/*Route::get('/admin/pagos', function () {
    return view('admin.pagos.list');
})->middleware('auth');;*/
//Route::get('admin/listpagos', 'ReportesController@listCursosComprados')->name('admin_listpagos')->middleware('auth');;
Route::get('admin/pagos', 'ReportesController@indexCursoComprados')->name('admin_listpagos')->middleware('auth');
Route::get('admin/listpagosdet/{id}', 'ReportesController@indexPagoDet')->name('admin_listpagosdet')->middleware('auth');
Route::get('admin/listEstPaginate/', 'ReportesController@listEstudiantesPaginate')->middleware('auth');
Route::get('admin/desactivarCuenta/{id}', 'ReportesController@desactivarCuenta')->middleware('auth');
Route::get('admin/activarCuenta/{id}', 'ReportesController@activarCuenta')->middleware('auth');

/* COMENTARIOS */
Route::get('admin/comentarios', 'MensajesController@index')->name('admin_comentarios')->middleware('auth');
Route::get('admin/listcoment', 'MensajesController@listarComentarios')->name('admin_listcoment')->middleware('auth');
Route::get('admin/msjleido/{id}', 'MensajesController@mensajeLeido')->name('admin_msjleido')->middleware('auth');

/* ASIGNAR ALUMNO AL CURSO */
Route::get('/admin/asignar-alumno', 'PersonaController@indexAsignarAlumno')->name('admin_asignar_alumno')->middleware('auth');
Route::post('/admin/guardarasigalumno', 'PersonaController@guardarAsignarAlumno')->name('admin_guardar_asig')->middleware('auth');
Route::get('/admin/listasigalumno', 'PersonaController@listasigalumno')->name('admin_listasigalumno')->middleware('auth');
Route::get('/admin/mostrarasigalumno/{id}', 'PersonaController@mostrarasigalumno')->name('admin_mostrarasigalumno')->middleware('auth');
Route::get('/admin/ver/resolucion/estudiante/{idusuario}/{idexamen}', 'CoursesController@getVerExamenResuelto')->middleware('auth');

Route::get('resolver/examen/{idexamen}', 'ResolverExamenController@getMostrarResolverExamen')->name("resolverExamenEst");

/* RESOLVER EXAMEN POR MODULO */
Route::get('resolver/examen/tiempo/{idexamen}', 'ResolverExamenController@getTiempoTermino')->middleware('auth');
Route::post('resolver/examen/guardar', 'ResolverExamenController@postGuardarExamen')->middleware('auth');
Route::post('resolver/examen/terminar', 'ResolverExamenController@postTerminarExamen')->middleware('auth');
Route::get('/admin/prueba/lista', 'ReportesController@getListarReportes')->middleware('auth');

/** ROLES Y PERMISOS **/
Route::get('/admin/roles/lista', 'RoleController@listarRoles')->name('admin.listar.roles')->middleware('auth');
Route::get('/admin/roles/obtener', 'RoleController@obtenerRoles')->name('admin.obtener.roles')->middleware('auth');
Route::post('/admin/roles/create', 'RoleController@guardarEditarRol')->name('admin.crearEditar.roles')->middleware('auth');
Route::get('/admin/roles/mostrarRol/{id}', 'RoleController@mostrarRoles')->name('admin.mostrar.roles')->middleware('auth');
Route::get('/admin/roles/listarPermisos/{id}', 'RoleController@listarPermisos')->name('admin.listar.permisos')->middleware('auth');
Route::get('/admin/roles/cambiarEstado/{id}/{estado}', 'RoleController@cambiarEstadoRol')->name('admin.cambiarEstado.roles')->middleware('auth');