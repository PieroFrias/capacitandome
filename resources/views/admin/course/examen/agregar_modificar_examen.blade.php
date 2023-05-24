@extends('layouts.app_admin')

@section('tituloPagina','Exámenes del Curso')

@section('styles')
    <link href="{{ asset('/recursos/admin/assets/css/pages/wizard/wizard-1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('subheader')
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">CURSO: {{ $curso->titulo }}</h5>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('admin_inicio') }}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
                <a href="{{ asset('/admin/course/examen/' . $curso->idcurso) }}" class="btn btn-light-primary font-weight-bolder btn-sm"><i class="fas fa-list"></i>Ver Exámenes</a>
            </div>
        </div>
    </div>
@endsection

@section('contenido')
    <div class="container">
        <div class="row">
            @if ($exam != null)                        
                <div class="card card-custom gutter-b">
                    <div class="card-header" style="min-height: 40px;">
                        <div class="card-title">
                            <h3 class="card-label"> MODIFICAR EXÁMEN </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('examen_guardar') }}" method="POST">
                            @csrf

                            <input type="hidden" name="idcurso" id="idcurso" value="{{ $curso->idcurso }}">
                            <input type="hidden" name="idexamen" id="idexamen" value="{{ $exam->idexamen }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label>TITULO  <span class="text-danger">*</span></label>
                                        <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ingrese titulo deL exámen..." value="{{ $exam->titulo }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label>SECCIÓN  <span class="text-danger">*</span></label>
                                        <select class="form-control" name="idseccion" id="idseccion">
                                            @foreach ($secciones as $item)
                                                <option {{ $exam->idseccion == $item->idseccion ? 'selected' : '' }} value="{{ $item->idseccion }}">{{ $item->titulo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label>FECHA Y HORA FINAL  <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ \Carbon\Carbon::parse($exam->fecha_final)->format('Y-m-d\TH:i') }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label>DESCRIPCIÓN  <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="descripcion" id="descripcion" cols="30" rows="4" placeholder="Ingrese una descripción...">{{ $exam->descripcion }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                </button>
                                            </div>
                                            <strong>Error!</strong> Revise los campos obligatorios.<br><br>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(Session::has('success'))
                                        <div class="alert alert-success">
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                </button>
                                            </div>
                                            {{Session::get('success')}}
                                        </div>
                                    @endif

                                    @if(Session::has('error'))
                                        <div class="alert alert-danger">
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                </button>
                                            </div>
                                            {{Session::get('error')}}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary float-right" id="btn_guardar">
                                        <i class="fas fa-save"></i> GUARDAR
                                    </button>

                                    <a href="{{ asset('/admin/course/examen/' . $curso->idcurso) }}" type="button" class="btn btn-secondary float-right mr-2" id="btn_cancelar">
                                        CANCELAR
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            @else                        
                <div class="card card-custom gutter-b">
                    <div class="card-header" style="min-height: 40px;">
                        <div class="card-title">
                            <h3 class="card-label"> CREAR NUEVO EXÁMEN </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('examen_guardar') }}" method="POST">
                            @csrf

                            <input type="hidden" name="idcurso" id="idcurso" value="{{ $curso->idcurso }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label>TITULO  <span class="text-danger">*</span></label>
                                        <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ingrese titulo deL exámen...">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label>SECCIÓN  <span class="text-danger">*</span></label>
                                        <select class="form-control" name="idseccion" id="idseccion">
                                            @foreach ($secciones as $item)
                                                <option value="{{ $item->idseccion }}">{{ $item->titulo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label>FECHA Y HORA FINAL  <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="fecha_fin" id="fecha_fin" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label>DESCRIPCIÓN  <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="descripcion" id="descripcion" cols="30" rows="4" placeholder="Ingrese una descripción..."></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                </button>
                                            </div>
                                            <strong>Error!</strong> Revise los campos obligatorios.<br><br>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(Session::has('success'))
                                        <div class="alert alert-success">
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                </button>
                                            </div>
                                            {{Session::get('success')}}
                                        </div>
                                    @endif

                                    @if(Session::has('error'))
                                        <div class="alert alert-danger">
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                </button>
                                            </div>
                                            {{Session::get('error')}}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary float-right" id="btn_guardar">
                                        <i class="fas fa-save"></i> GUARDAR
                                    </button>

                                    <a href="{{ asset('/admin/course/examen/' . $curso->idcurso) }}" type="button" class="btn btn-secondary float-right mr-2" id="btn_cancelar">
                                        CANCELAR
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('modal')
@endsection

@section('script')
    <script src="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('/recursos/admin/assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
    <script src="{{ asset('/recursos/admin/assets/js/pages/features/miscellaneous/toastr.js') }}"></script>

    <script>

    </script>
@endsection