@extends('layouts.app_admin')

@section('tituloPagina', 'Estudiantes y sus Notas')

@section('styles')
    <link href="{{ asset('/recursos/admin/assets/css/pages/wizard/wizard-1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('subheader')
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Examen: {{ $examen->titulo }}</h5>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('admin_inicio') }}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
                <a href="{{ asset('/admin/course/examen/' . $examen->idcurso) }}" class="btn btn-light-primary font-weight-bolder btn-sm">
                    <i class="fas fa-list"></i>
                    Ver Examenes
                </a>
            </div>
        </div>
    </div>
@endsection

@section('contenido')
    <div class="container">
        <div class="card card-custom gutter-b">
            <div class="card-header" style="min-height: 40px;">
                <div class="card-title">
                    <h3 class="card-label text-primary"><i class="fa fa-list mr-1"></i> Estudiantes y Notas</h3>
                </div>
                {{--<div class="card-toolbar">
                    <span class="card-label text-right">
                        <p class="font-size-22 text-success py-0">Estudiantes que resolvieron: {{ count($resoluciones) }}</p>
                        <p class="font-size-22 text-danger py-0">Estudiantes que faltan resolver: {{ count($estudiantes) - count($resoluciones) }}</p>
                    </span>
                </div>--}}
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <td class="text-center align-middle" style="background: rgb(239, 243, 243);">
                                    <strong>RESOLVIERON</strong>
                                </td>
                                <td class="text-center"><p class="font-size-22 text-success py-0"><strong class="font-size-22 text-success py-0">{{ count($resoluciones) }} ESTUDIANTES</strong></p></td>
                                <td class="text-center align-middle" style="background: rgb(239, 243, 243);">
                                    <strong>NO RESOLVIERON</strong>
                                </td>
                                <td class="text-center"><p class="font-size-22 text-danger py-0"><strong>{{ count($estudiantes) - count($resoluciones) }} ESTUDIANTES</strong></p></td>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <form action="#">
                                    <div class="form-group">
                                        <label for="searh_notas_estudiantes">Buscar estudiante</label>
                                        <div class="input-group">                                        
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Buscar estudiante..." id="searh_notas_estudiantes">
                                        </div>
                                    </div>
                                </form>                            
                            </div>
                        </div>
                        
                        <div id="tableNotasEstudiantes">

                        </div>
                    </div>
                </div>
            </div>
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
        function init(){
            listaNotas(1);

            $("#searh_notas_estudiantes").on('keyup', function () {
                listaNotas(1);
            });
        }

        function listaNotas(page){
            let searh_estudiante = $("#searh_notas_estudiantes").val();
            let id = {{ $examen->idexamen }}

            $.get('/ver/notas/estudiantes/examen?idexamen=' + id + '&searh_estudiante=' + searh_estudiante, function(data){
                $("#tableNotasEstudiantes").html(data);
            });
        }

        init();
    </script>
@endsection