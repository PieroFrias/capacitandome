<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
Use App\Models\Usuario;
Use App\Models\Venta;

//use Illuminate\Mail\Mailable;
use App\Mail\ConfirmacionCursoMailable;
use Illuminate\Support\Facades\Mail;

class InicioController extends Controller {
    
    public function index() {
        $mensajes = DB::table('mensajes')
        ->where('estado', '=', '1' )
        ->orderBy('idmensajes', 'desc')
        ->limit(3)
        ->get();

        return view('admin.inicio.inicio', ['mensajes' => $mensajes]);
    }

    public function listPagosVoucher(Request $request) {

        $search = $request->filtro_search;
        $query  = DB::table('venta')
                ->join('users as u', 'u.idusuario', '=', 'venta.idusuario')
                ->join('persona as p', 'p.idpersona', '=', 'u.idpersona')
                ->join('curso as c', 'c.idcurso', '=', 'venta.idcurso')
                ->select('venta.idventa','venta.idusuario','venta.fecha_venta','c.titulo','p.nombre','p.apellidos', 'c.precio','venta.boucher_pago')
                ->where('venta.boucher_pago', '!=', NULL )
                ->where('venta.estado', '=', '2' );

                if ($search != "") {
                    $query->where("p.apellidos","LIKE","%{$search}%");
                }
                $pagos = $query->orderBy('venta.idventa', 'desc')->paginate(20);

        return view('admin.inicio.pagosPaginate', ['pagos' => $pagos,'search' => $search])->render();
    }

    public function habilitarVenta($idventa) {
        $ventaFind = Venta::where('idventa', $idventa)->first();

        if (!empty($ventaFind)) {
            $venta           = Venta::find($idventa);
            $venta->estado   = 1;
            $venta->save();

            $persona = DB::table('users')->join('persona', 'users.idpersona', '=', 'persona.idpersona')->where('idusuario', $venta->idusuario)->first();

            if (!empty($persona->correo)) {

                $curso   = DB::table('curso')->where('idcurso', $venta->idcurso)->first();
                $msg = [
                    "persona"   => $persona->nombre." ".$persona->apellidos,
                    "curso"     => $curso->titulo
                ];
                $correo = new ConfirmacionCursoMailable($msg);
                $email = Mail::to($persona->correo)->send($correo);
            }       

            return json_encode(["status" => true, "message" => "Curso habilitado."]);
        } else {
            return json_encode(["status" => false, "message" => "No existe esta venta."]);
        }
    }

    public function eliminarVenta($idventa) {
        $ventaFind = Venta::where('idventa', $idventa)->first();

        if (!empty($ventaFind)) {
            $venta = Venta::find($idventa);
            $venta->delete();

            return json_encode(["status" => true, "message" => "Venta eliminada."]);
        } else {
            return json_encode(["status" => false, "message" => "No existe esta venta."]);
        }
    }
}