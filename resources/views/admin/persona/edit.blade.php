@extends('layouts.app_admin')
@section('tituloPagina','Persona')
@section('styles')
@endsection
@section('subheader')
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <h5 class="text-dark font-weight-bold my-1 mr-5"> <i class="fa fa-edit"></i> {{strtoupper($persona->apellidos.", ".$persona->nombre." | Tipo : ".$persona->tipo_persona)}}</h5>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{route('admin_inicio')}}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
            <a href="{{route('admin_personas')}}" class="btn btn-light-primary font-weight-bolder btn-sm"><i class="fas fa-list"></i> Lista de personas</a>
        </div>
    </div>
</div>
@endsection
@section('contenido')
<!--begin::Container-->
<div class="container">

    <!--begin::Card-->
    <div class="card card-custom">
        <div class="card-header py-3">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fa fa-user text-primary"></i>
                </span>
                <h3 class="card-label">Editar persona</h3>
            </div>
        </div>
        <div class="card-body">

            <style>
                .error-select{border: 1px solid red !important;border-radius: .42rem !important;}
            </style>

            <div class="row">
                <div class="col-lg-12">
                    <p><strong class="text-danger">Importante:</strong></p>
                    <ul>
                        <li>Asegúrese de <strong>COMPLETAR LOS CAMPOS REQUERIDOS</strong></li>
                        <li>Al momento de <strong>EDITAR UN DOCENTE</strong> asegurese de de adjuntar una imagen y completar su <strong>INFORMACIÓN ACADEMICA.</strong></li>
                        <li>En caso la persona pierda la contraseña de su cuenta <strong> DEBE REGISTRAR UNA NUEVA.</strong></li>
                    </ul>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">                                               
                                    
                    @if(Session::has('success'))                                                        
                        <div class="alert alert-custom alert-success fade show" role="alert">
                            <div class="alert-icon"><i class="la la-check"></i></div>
                            <div class="alert-text">{{Session::get('success')}}</div>
                            <div class="alert-close">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(Session::has('error'))
                        <div class="alert alert-custom alert-danger fade show" role="alert">
                            <div class="alert-icon"><i class="la la-close"></i></div>
                            <div class="alert-text">{{Session::get('error')}}</div>
                            <div class="alert-close">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $disabled = '';
                if($persona->tipo_persona == 'estudiante') {
                    $disabled = 'readonly';
                }
            @endphp

            <form action="{{route('admin_personas_update', $persona->idpersona)}}" method="post" autocomplete="off" enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                <h3 class="font-size-lg text-dark bg-secondary rounded py-2 px-4 font-weight-bold mb-6">
                    1. DATOS PERSONALES :
                </h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control {{ $errors->first('nombre') ? 'is-invalid' : '' }}" 
                                placeholder="Ingrese nombre" value="{{ $persona->nombre }}">
                            @if ($errors->first('nombre'))
                                <span class="form-text text-danger">{{ $errors->first('nombre') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control {{ $errors->first('apellidos') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese apellido" value="{{ $persona->apellidos }}">
                            @if ($errors->first('apellidos'))
                                <span class="form-text text-danger">{{ $errors->first('apellidos') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-4">
                            <label>DNI/Carnet ext</label>
                            <input type="text" name="dni" class="form-control {{ $errors->first('dni') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese dni" value="{{ $persona->dni }}">
                            @if ($errors->first('dni'))
                                <span class="form-text text-danger">{{ $errors->first('dni') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-4">
                            <label>Telefono</label>
                            <input type="text" name="telefono" class="form-control {{ $errors->first('telefono') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese telefono" value="{{ $persona->telefono }}">
                            @if ($errors->first('telefono'))
                                <span class="form-text text-danger">{{ $errors->first('telefono') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control {{ $errors->first('direccion') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese dirección" value="{{ $persona->direccion }}">
                            @if ($errors->first('direccion'))
                                <span class="form-text text-danger">{{ $errors->first('direccion') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Departamento <span class="text-danger">*</span></label>
                            <select id="iddepartamento" name="iddepartamento" class="form-control selectpicker {{ $errors->first('iddepartamento') ? 'error-select' : '' }}" data-live-search="true">
                                <option value="" disabled>Seleccione..</option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento->iddepartamento }}" {{ $persona->iddepartamento == $departamento->iddepartamento ? "selected" : "" }}>
                                        {{ $departamento->departamento }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->first('iddepartamento'))
                                <span class="form-text text-danger">{{ $errors->first('iddepartamento') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Foto <span class="text-primary" style="font-size: 11px">(Requerido para docente)</span></label>
                            <div class="custom-file">
                                <input type="hidden" name="foto" value="{{ $persona->foto }}">
                                <input type="file" name="foto_actual" {{$disabled}} class="custom-file-input {{ $errors->first('foto') ? 'is-invalid' : '' }}" id="file" accept="image/*">
                                <label class="custom-file-label" for="file">{{ $persona->foto }}</label>
                            </div>
                            @if ($errors->first('foto'))
                                <span class="form-text text-danger">{{ $errors->first('foto') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <h3 class="font-size-lg text-dark bg-secondary py-2 rounded px-4 mr-2 font-weight-bold mb-6">
                    2. DATOS ACADEMICOS :
                </h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Tipo persona <span class="text-danger">*</span></label>
                            <select name="tipo_persona" class="form-control selectpicker {{ $errors->first('tipo_persona') ? 'error-select' : '' }}">
                                <option value="" selected disabled>Seleccione</option>
                                @if ($disabled == '')
                                <option value="Docente" {{ $persona->tipo_persona == 'Docente' ? "selected" : "" }}>Docente</option>
                                @endif                                
                                <option value="estudiante" {{ $persona->tipo_persona == 'estudiante' ? "selected" : "" }}>Alumno</option>
                            </select>
                            @if ($errors->first('tipo_persona'))
                                <span class="form-text text-danger">{{ $errors->first('tipo_persona') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-4">
                            <label>Grado academico <span class="text-primary" style="font-size: 11px">(Requerido para docente)</span></label>
                            <input type="text" name="carrera" {{$disabled}} class="form-control {{ $errors->first('carrera') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese grado academico" value="{{ $persona->carrera }}">
                            @if ($errors->first('carrera'))
                                <span class="form-text text-danger">{{ $errors->first('carrera') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>Perfil <span class="text-primary" style="font-size: 11px">(Requerido para docente)</span></label>
                            <textarea name="perfil" {{$disabled}} class="form-control {{ $errors->first('perfil') ? 'is-invalid' : '' }}" 
                             cols="30" rows="8" placeholder="Ingrese perfil profesional">{{ $persona->perfil }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-4">
                            <label>Exp. Laboral</label>
                            <textarea {{$disabled}} class="form-control {{ $errors->first('experiencia_laboral') ? 'is-invalid' : '' }}" name="experiencia_laboral" 
                                placeholder="Ingrese exp. laboral" cols="30" rows="8">{{ $persona->experiencia_laboral }}</textarea>
                        </div>
                    </div>
                </div>
                
                <h3 class="font-size-lg text-dark bg-secondary rounded py-2 px-4 font-weight-bold mb-3">
                    3. CUENTA DE ACCESO AL SISTEMA :
                </h3>

                <div class="row">
                    <div class="col-lg-12">
                        <p>
                            @if ($usuario == "")
                                <strong class="text-danger">OJO:</strong> Esta persona no tiene usuario ni contraseña asignado.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>Usuario <span class="text-danger">*</span></label>
                            <input type="text" name="usuario" disabled class="form-control {{ $errors->first('usuario') ? 'is-invalid' : '' }}" 
                                placeholder="Ingrese usuario" value="{{ $usuario->usuario }}">
                                <input type="hidden" name="idusuario" value="{{ $usuario->idusuario }}">
                            @if ($errors->first('usuario'))
                                <span class="form-text text-danger">{{ $errors->first('usuario') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>contraseña <span class="text-danger">(Nueva contraseña)</span></label>
                            <input type="password" name="clave" class="form-control {{ $errors->first('clave') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese contraseña">
                            @if ($errors->first('clave'))
                                <span class="form-text text-danger">{{ $errors->first('clave') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4 text-right">
                        <a href="{{route('admin_personas')}}" class="btn btn-warning"><i class="la la-close"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="la la-plus-circle"></i> Editar persona</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--end::Card-->


</div>
<!--end::Container-->
@endsection

@section('modal')
@endsection

@section('script')
@endsection
