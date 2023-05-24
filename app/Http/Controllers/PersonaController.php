<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\Persona;
Use App\User;
use App\Models\Usuario;
use Hash;
//use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PersonaRequest;
use App\Http\Requests\PersonaRequestUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\Log;
use App\http\Controllers\Helpers;

class PersonaController extends Controller 
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('can:admin_personas')->only('index', 'paginatePersona');
        $this->middleware('can:admin_personas_create')->only('create', 'store');
        $this->middleware('can:admin_personas_edit')->only('edit', 'update');
        $this->middleware('can:admin_personas_cambiarEstado')->only('cambiarEstadoPersona');
    }
   
    public function index(Request $request) {
        $departamentos = DB::table('departamento')->get();
        return view('admin.persona.list', ['departamentos' => $departamentos]);
    }

    public function paginatePersona(Request $request) {
        $search = $request->dni;
        
        $query = DB::table('persona')
                ->join('users as u', 'u.idpersona', '=', 'persona.idpersona')
                ->join('rol as r', 'r.idrol', '=', 'u.idrol')
                ->join('departamento as d', 'd.iddepartamento', '=', 'persona.iddepartamento')
                ->select('persona.idpersona','d.departamento','persona.nombre', 'persona.apellidos','persona.dni','persona.telefono','persona.direccion','u.idrol','u.usuario', 'persona.estado')
                ->where("persona.idpersona","!=", 1)
                ->where("persona.apellidos","LIKE","%{$request->dni}%");
                //->orWhere('persona.apellidos', 'like', "%{$request->dni}%");

                if ($request->fdep != "todos") {
                    $query->where("persona.iddepartamento","=",$request->fdep);
                }

                if ($request->est != "todos") {
                    switch ($request->est) {
                        case '1':
                                $query->where("persona.becario","=","1");
                            break;
                        case '0':
                                $query->where("persona.tipo_persona","=","estudiante");
                            break;
                        case '2':
                                $query->where("persona.tipo_persona","=","Docente");
                            break;
                    }
                }

        $personas = $query->orderBy('persona.idpersona', 'DESC')->paginate(50);
        
        return view('admin.persona.paginate', ['personas' => $personas,'search' => $search])->render();
    }

    public function create() {
        $departamentos = DB::table('departamento')->get();
        $roles = Role::get();

        return view('admin.persona.create', ['departamentos' => $departamentos, 'roles' => $roles]);
    }

    public function store(PersonaRequest $request) {
        $formData       = $request->all();

        //Valida si la persona está registrada.
        $validarPersona = Persona::where([['nombre', '=' , $formData['nombre']], ['apellidos' ,'=', $formData['apellidos']],['tipo_persona' ,'=', $formData['tipo_persona']]])->first();
        
        if (empty($validarPersona)) {
            if($request->hasFile('foto')){
                $archivo            = $request->file('foto');
                $nombre_archivo     = $archivo->getClientOriginalName();
                $extension          = explode(".", $nombre_archivo);
                $nvo_nombre_archivo = round(microtime(true)) . '.' . end($extension);            
                if (!Storage::disk('public')->exists('personas')) {
                    Storage::makeDirectory('public/personas', 0775, true);
                }

                \Storage::disk('public')->put("personas/".$nvo_nombre_archivo, \File::get($archivo)); 
                $formData['foto'] = $nvo_nombre_archivo;
            }
            
            $persona = Persona::create($formData);
            
            if ($persona) {
                $usuario = new User();
                $usuario->idrol     = ($formData['tipo_persona'] == 'Docente') ? 1 : 2;

                $usuario->idpersona = $persona->idpersona;
                $usuario->usuario   = $formData['usuario'];
                $usuario->password  = Hash::make($formData['clave']);
                $usuario->estado    = 1;

                $tipoPersona = $formData['tipo_persona'];
                //dd($tipoPersona);

                if ($tipoPersona == "Docente") {
                    $usuario->assignRole('Docente');
                }
                else if($tipoPersona == "estudiante") {
                    $usuario->assignRole('Estudiante');
                }

                $usuario->save();

                return redirect()->route('admin_personas_create')->with('success', 'Persona registrada correctamente.'); 
            } else {
                return redirect()->route('admin_personas_create')->with('error', 'Registro no guardado, recargue la página y vuelva a intentar.');
            }
        } else {
            return redirect()->route('admin_personas_create')->with('error', 'Ya existe un usuario registrado con este nombre y apellido: '.$validarPersona->nombre.' '.$validarPersona->apellidos.' | Rol: '.$validarPersona->tipo_persona);
        }            
    }

    public function edit($id) {
        $persona = Persona::find($id);

        if ($persona) {
            $departamentos = DB::table('departamento')->get();
            $usuario       = Usuario::where([['idpersona','=',$id]])->first();

            return view('admin.persona.edit', ['departamentos' => $departamentos, 'persona'=> $persona, 'usuario'=> $usuario]);
        } else {
            return \redirect()->route('admin_personas');
        }
    }

    public function update(PersonaRequestUpdate $request, $id) {
        $formData   = $request->all();
        $foto       = '';

        if($request->hasFile('foto_actual')){
            $archivo            = $request->file('foto_actual');
            $nombre_archivo     = $archivo->getClientOriginalName();
            $extension          = explode(".", $nombre_archivo);
            $nvo_nombre_archivo = round(microtime(true)) . '.' . end($extension);

            if (!Storage::disk('public')->exists('personas')) {
                Storage::makeDirectory('public/personas', 0775, true);
            }

            $foto = $nvo_nombre_archivo; 
        } else {
            $foto = $formData['foto'];
        }

        $persona = Persona::find($id);
        $persona->tipo_persona          = $formData['tipo_persona'];
        $persona->nombre                = $formData['nombre'];
        $persona->apellidos             = $formData['apellidos'];
        $persona->dni                   = $formData['dni'];
        $persona->telefono              = $formData['telefono'];
        $persona->iddepartamento        = $formData['iddepartamento'];
        $persona->foto                  = $foto;
        $persona->direccion             = $formData['direccion'];
        $persona->carrera               = $formData['carrera'];
        $persona->perfil                = $formData['perfil'];
        $persona->experiencia_laboral   = $formData['experiencia_laboral'];
        $persona->save();

        if ($persona ) {
            if ($formData['clave'] != "") {
                $usuario = Usuario::where([['idpersona', '=', $id],['idusuario','=',$formData['idusuario']]])->first();
                $usuario->password  = Hash::make($formData['clave']);
                $usuario->save();
            }
            return redirect()->route('admin_personas_edit',$id)->with('success', 'Persona actualizada correctamente.'); 
        } else {
            return redirect()->route('admin_personas_edit',$id)->with('error', 'Registro no actualizado, recargue la página y vuelva a intentar.');
        }
        
        //return redirect()->route('admin_personas_edit',$id)->with('success','PERSONA ACTUALIZADA SATISFACTORIAMENTE.');
    }

    public function cambiarEstadoPersona($id, $estado) {
        $persona = Persona::find($id);
        $persona->estado = $estado;
        $persona->save();

        DB::table('users')->where('idpersona', $id)->update(['estado'=>$estado]);
        return json_encode(["status" => true, "message" => "Se cambió el estado del registro"]);
    }

    public function cambiarContrasenia(Request $request) {
        $idusuario       = Auth::user()->idusuario;
        $clave_actual    = $request->input('clave_actual');
        $clave_nueva     = $request->input('clave_nueva');
        $clave_confir    = $request->input('clave_confir');
        $status          = false;
        $message         = false;

        if ($clave_actual != "" && $clave_nueva != "") {
            if ($clave_nueva == $clave_confir) {
                if (Hash::check($clave_actual, Auth::user()->password)) {
                    $usuario = Usuario::where('idusuario', $idusuario)->first();
                    $usuario->password  = Hash::make($request->input('clave_nueva'));
                    $usuario->save();                    
                    $status  = true;
                    $message = "Contraseña actualzada.";
                } else {
                    $status = false;
                    $message = "Error al actualizar contraseña.";
                }
            } else {
                $status = false;
                $message = "Contraseña nueva y la confirmación no son iguales.";
            }
        }

        return json_encode(["status" => $status, "message" => $message]);
    }

    //ASIGNAR ALUMNOS A UN CURSO
    public function indexAsignarAlumno() {
        $cursos      = DB::table('curso')->distinct()->get();
        $estudiantes = DB::table('persona')->where('tipo_persona', '=', 'estudiante')->distinct()->get();

        return view('admin.asignar-alumno.list', ['cursos' => $cursos, 'estudiantes' => $estudiantes]);
    }

    public function guardarAsignarAlumno(Request $request) {
        date_default_timezone_set("America/Lima");
        $idventa    = $request->input('idventa');
        $idcurso    = $request->input('idcurso');
        $idpersona  = $request->input('idpersona');

        $curso      = DB::table('curso')->where('idcurso', '=', $idcurso)->first();
        $usuario    = DB::table('users')->where('idpersona', '=', $idpersona)->first();
        $venta      = DB::table('venta')->where([['idusuario', '=', $usuario->idusuario],['idcurso', '=', $idcurso]])->first();

        if (empty($idventa)) {
            if (empty($venta)) {
                $asignar  = DB::table('venta')->insert([
                            'idcurso'            => $idcurso, 
                            'idusuario'          => $usuario->idusuario,
                            'fecha_venta'        => date("Y/m/d H:i:s"),
                            'precio'             => $curso->precio,
                            'precio_referencial' => $curso->precio,
                            'estado'             => 1,
                ]);

                return json_encode(['status' => true, 'message' => 'Guardado correctamente.']);
            } else {
                return json_encode(['status' => false, 'message' => 'EL ALUMNO YA SE ENCUENTRA REGISTRADO EN ESTE CURSO.']);
            }
        } else {
            $asignar  = DB::table('venta')->where('idventa', $idventa)->update([
                    'idcurso'            => $idcurso, 
                    'idusuario'          => $usuario->idusuario,
            ]);

            return json_encode(['status' => true, 'message' => 'Actualizado correctamente.']);
        }        
    }

    public function listasigalumno() {
        $venta = DB::table('venta')
                ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
                ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
                ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
                ->select(
                    'venta.idventa',
                    'p.nombre',
                    'p.apellidos',
                    'c.titulo',
                    'venta.fecha_venta',
                    'venta.precio'
                )
                ->where('venta.estado','=','1')
                ->get();
                $autoi = 1;
                $data = Array();
                foreach ($venta as $vent) {
                    $data[] = array(
                        "autoi"         =>$autoi++,
                        "fecha_venta"   =>$vent->fecha_venta,
                        "alumno"        =>$vent->nombre." ".$vent->apellidos,
                        "curso"         =>$vent->titulo,
                        "precio"        =>"s/.".$vent->precio,
                        "idventa"       =>$vent->idventa
                    );
                }

        return json_encode(array('data' => $data));
    }

    public function mostrarasigalumno($idventa) {
        $venta = DB::table('venta')
                ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
                ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
                ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
                ->select(
                    'venta.idventa',
                    'venta.idusuario',
                    'venta.idcurso',
                    'p.idpersona'
                )
                ->where('venta.idventa','=',$idventa)
                ->where('venta.estado','=','1')
                ->first();

        return \json_encode($venta);
    }
    
    public function verperfil() {
        $idpersona       = Auth::user()->idpersona;
        $idusuario       = Auth::user()->idusuario;
        $miscursos_header = DB::table('venta')
            ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
            ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
            ->select('c.idcurso','c.titulo', 'c.portada','c.descripcion','c.hora_duracion','c.total_clases')
            ->where([['venta.idusuario', '=', $idusuario],['venta.estado', '=', 1]])
            ->distinct()->limit(3)->get();
        $departamentos = DB::table('departamento')->get();
        $persona       = Persona::find($idpersona);

        return view('web.perfil', [
                'departamentos' => $departamentos, 
                'persona'       => $persona,
                'miscursos_header' =>$miscursos_header
        ]);
    }

    public function actualizarperfil(Request $request) {
        $idpersona       = Auth::user()->idpersona;
        $persona = Persona::find($idpersona);
        $persona->telefono       = $request->input('telefono');
        $persona->correo         = $request->input('correo');
        $persona->iddepartamento = $request->input('iddepartamento');
        $persona->direccion      = $request->input('direccion');
        $persona->save();
        
        return redirect()->route('perfil')->with('mensaje', 'PERFIL ACTUALIZADO CORRECTAMENTE.');
    }
}