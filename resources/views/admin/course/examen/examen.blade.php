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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">CURSO: {{ $examen->titulo }}</h5>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('admin_inicio') }}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
                <a href="{{ asset('/admin/courses') }}" class="btn btn-light-primary font-weight-bolder btn-sm"><i class="fas fa-list"></i> Ver Cursos</a>
            </div>
        </div>
    </div>
@endsection

@section('contenido')
    <div class="container">
        <div class="card card-custom gutter-b">
            <div class="card-header" style="min-height: 40px;">
                <div class="card-title">
                    <h3 class="card-label text-primary"><i class="fa fa-list mr-1"></i> LISTA DE EXÁMENES </h3>
                </div>
                <div class="card-toolbar">
                    <a href="admin/course/examen/agregar/{{ $examen->idcurso }}" class="btn btn-primary font-weight-bolder">
                        <i class="fa fa-plus-circle"></i> NUEVO EXAMEN
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td class="text-center">
                                    <a href="javascript:" class="btn btn-info"><i class="fas fa-question-circle p-0"></i></a>
                                </td>
                                <td style="vertical-align: middle">Registrar preguntas</td>
                                <td class="text-center">
                                    <a href="javascript:" class="btn btn-info"><i class="fas fa-eye p-0"></i></a>
                                </td>
                                <td style="vertical-align: middle">Pre-visualizar examen</td>
                                <td class="text-center">
                                    <a href="javascript:" class="btn btn-info"><i class="fas fa-list-ol p-0"></i></a>
                                </td>
                                <td style="vertical-align: middle">Notas del estudiante</td>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-hover table-bordered" id="table_examen">
                            @include('admin.course.examen.examen_table')
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="ModalNotasEstudianteExamen" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" id="contentModalNotasEstudianteExamen">

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('/recursos/admin/assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
    <script src="{{ asset('/recursos/admin/assets/js/pages/features/miscellaneous/toastr.js') }}"></script>

    <script>
        function examen() {

            //

        }

        function mostrarNotasEstudiantes(id){
            $.get('/ver/notas/estudiantes/examen/' + id, function(data){

                $('#ModalNotasEstudianteExamen').modal('show');

                $('#contentModalNotasEstudianteExamen').html(data);
            });
        }

        function listarExamen(idcurso) {            
            $.get(`/admin/course/obtener/examen/${idcurso}`, function (data, textStatus, jqXHR) {
                $("#table_examen").html(data);

                $('[data-toggle="tooltip"]').tooltip();
            });
        }

        function cambiarEstadoExamen(idexam, estado, idcurso) {
            Swal.fire({
                title: '¿Seguro que quiere cambiar el estado de este registro?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f64e60',
                confirmButtonText: '¡Si, cambiar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get(`/admin/course/cambiarEstadoExamen/examen/${idexam}/${estado}`, function (data, status) {
                        data = JSON.parse(data);
                        console.log(data);
                        console.log(idcurso);
                        if (data.status == true) {
                            Swal.fire('Estado cambiado', '', 'success');
                            listarExamen(idcurso);
                        }else{
                            alert('Ocurrio un error, se refescara la página');
                            location.reload();
                        }
                    });
                }else{
                    
                }
            });
        }

        examen();
    </script>
@endsection
