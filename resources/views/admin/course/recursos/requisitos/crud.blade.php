@extends('layouts.app_admin')

@section('styles')
@endsection

@section('subheader')
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-primary font-weight-bold my-1 mr-5">
                        <i class="fas fa-clipboard-list mr-1"></i> REQUISITOS DEL CURSO
                    </h5>
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->

            <div class="d-flex align-items-center">
                {{-- <a href="/admin/courses" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="la la-book"></i> CURSO</a> --}}

                <a href="{{ route('admin_course_list') }}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="la la-book"></i> CURSO</a>

                <a href="{{ route('admin_inicio')}}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="la la-home"></i> INICIO</a>
            </div>
        </div>
    </div>
@endsection

@section('contenido')
    <div class="container">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <div class="d-flex align-items-center">
                        <!--begin::Pic-->
                        <div class="flex-shrink-0 mr-4 symbol symbol-65 symbol-circle">
                            <img src="{{ asset('/storage/cursos/'.$curso->portada.'') }}" alt="image">
                        </div>
                        <!--end::Pic-->

                        <!--begin::Info-->
                        <div class="d-flex flex-column mr-auto">
                            <!--begin: Title-->
                            <a href="javascript:" class="card-title text-hover-primary font-weight-bolder font-size-h5 text-dark mb-1">{{$curso->titulo}}</a>

                            {{-- <span class="text-muted font-weight-bold"><i class="far fa-calendar-alt"></i> {{$curso->fecha_inicio}} | <i class="far fa-calendar-alt"></i> {{$curso->fecha_final}}</span> --}}

                            <span class="text-muted font-weight-bold"><i class="far fa-calendar-alt"></i> {{ date('d-m-Y', strtotime($curso->fecha_inicio)) }} | <i class="far fa-calendar-alt"></i> {{ date('d-m-Y', strtotime($curso->fecha_final)) }}</span>
                            <!--end::Title-->
                        </div>
                        <!--end::Info-->
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Lista de requisitos</h3>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-head-custom" id="tablaRequisitos">
                                        <thead style="">
                                            <tr>
                                                <th style="width: 5%">N°</th>
                                                <th style="width: 50%">TITULO</th>
                                                <th style="width: 40%" class="text-center">Estado</th>
                                                <th style="width: 5%;" class="text-center"><i class="fa fa-cogs"></i></th>
                                            </tr>
                                        </thead>

                                        @php
                                            $autoi = 1;
                                        @endphp
                                        
                                        <tbody>
                                            @foreach ($requisitos as $item)
                                                <tr id="tr_{{ $item->idrequisitos }}">
                                                    <td style="vertical-align: middle;">{{ $autoi++ }}.</td>
                                                    <td style="vertical-align: middle;">{{ $item->requisitos }}</td>

                                                    @if($item->estado == 1)
                                                        <td class="text-center" style="vertical-align: middle;">
                                                            <a href="javascript:void(0)" onclick="cambiarEstadoRequisitos({{$item->idrequisitos}}, 0, {{$item->curso->idcurso}})" 
                                                                class="btn btn-success text-white font-weight-bold btn-sm" data-toggle="tooltip" 
                                                                data-placement="top" title="" data-original-title="Deshabilitar">
                                                                Habilitado <i class="fas fa-check ml-1"></i>
                                                            </a>
                                                        </td>
                                                    @else
                                                        <td class="text-center" style="vertical-align: middle;">
                                                            <a href="javascript:void(0)" onclick="cambiarEstadoRequisitos({{$item->idrequisitos}}, 1, {{$item->curso->idcurso}})" 
                                                                class="btn btn-danger text-white font-weight-bold btn-sm" data-toggle="tooltip" 
                                                                data-placement="top" title="" data-original-title="Habilitar">
                                                                Deshabilitado <i class="fas fa-times ml-1"></i>
                                                            </a>
                                                        </td>
                                                    @endif

                                                    <td class="text-center" style="vertical-align: middle;">
                                                        <a href="javascript:" onclick="mostrarRequisitos({{ $item->idrequisitos }})" 
                                                            class="btn btn-light-warning font-weight-bold btn-sm" data-toggle="tooltip" 
                                                            data-placement="top" data-original-title="Editar requisito"><i class="fas fa-edit p-0"></i>
                                                        </a>
                                                        {{-- <a href="javascript:void(0)" onclick="eliminarRequisitos({{$item->idrequisitos}})" 
                                                            class="btn btn-light-danger font-weight-bold btn-sm" data-toggle="tooltip" 
                                                            data-placement="top" title="" data-original-title="Eliminar curso"><i class="fas fa-trash p-0"></i>
                                                        </a> --}}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if (count($requisitos) == 0)
                                                <tr><td class="text-center" colspan="3">Aún no hay requisitos registrados...</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">Ingrese requisito</h3>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <form action="{{ route('guardEditarRequisitos')}} " method="post">
                                            @csrf
                                            <input type="hidden" name="idrequisitos" id="idrequisitos" value="">
                                            <input type="hidden" name="idcurso" id="idcurso" value="{{ $curso->idcurso }}">
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

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group mb-4">
                                                        <label for="titulo" class="text-primary">Requerido</label>
                                                        <textarea name="titulo" id="titulo" class="form-control {{ $errors->first('titulo') ? 'is-invalid' : '' }}" cols="30" rows="5"></textarea>
                                                        @if ($errors->first('titulo'))
                                                            <span class="form-text text-danger" id="errorTitulo">{{ $errors->first('titulo') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 text-center">
                                                    <button type="button" onclick="limpiar()" class="btn btn-secondary font-weight-bold mr-2"><i class="la la-close"></i> LIMPIAR</button>
                                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2"><i class="la la-plus-circle"></i> GUARDAR</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Wizard-->
        </div>
    </div>
@endsection

@section('modal')
@endsection

@section('script')
    <script src="{{ asset('/recursos/admin/assets/js/pages/features/miscellaneous/toastr.js') }}"></script>

    <script>
        function mostrarRequisitos(idrequisitos) {
            $.get("/admin/mostrarrequisitos/"+idrequisitos, function name(respuesta) {
                respuesta = JSON.parse(respuesta);
                
                $("#idrequisitos").val(respuesta.idrequisitos);
                $('#titulo').val(respuesta.requisitos);
            })
        }

        function listarRequisitos(idcurso) {            
            $.get(`/admin/obtener/requisitos/${idcurso}`, function (data, textStatus, jqXHR) {
                $("#tablaRequisitos").html(data);

                $('[data-toggle="tooltip"]').tooltip();
            });
        }
        
        function limpiar() {
            $("#idrequisitos").val('');
            $('#titulo').val('');
            $('#titulo').removeClass('is-invalid');
            $("#errorTitulo").html('');

            toastr.info('Formulario reseteado.')
        }

        function cambiarEstadoRequisitos(idrequisitos, estado, idcurso) {
            Swal.fire({
                title: '¿Seguro que quiere cambiar el estado de este registro?',
                text: "No se podra recuperar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f64e60',
                confirmButtonText: 'Si, cambiar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get(`/admin/cambiarEstadoRequisitos/${idrequisitos}/${estado}`, function (data, status) {
                        data = JSON.parse(data);
                        console.log(data);
                        console.log(idcurso);
                        if (data.status == true) {
                            Swal.fire('Estado cambiado', '', 'success');
                            listarRequisitos(idcurso);
                        }else{
                            alert('Ocurrio un error, se refescara la página');
                            location.reload();
                        }
                    });
                }else{
                }
            });
        }
    </script>
@endsection