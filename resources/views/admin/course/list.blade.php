@extends('layouts.app_admin')

@section('tituloPagina','Lista de cursos')

@section('styles')
@endsection

@section('subheader')
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <h5 class="text-dark font-weight-bold my-1 mr-5"> <i class="fa fa-list"></i> CURSOS DICTADOS POR CAPACITÁNDOME</h5>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{route('admin_inicio')}}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
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
                    <i class="fa fa-book text-primary"></i>
                </span>
                <h3 class="card-label">Lista de cursos</h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->

            {{-- {{ route('curso.create') }} --}}
            <a href="/admin/course/nuevo" class="btn btn-primary"> <i class="la la-plus-circle"></i> REGISTRAR</a>
            <!--end::Button-->
            </div>
        </div>
        <div class="card-body">
            <!--begin: Datatable-->

            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="6">
                                    <span>
                                        Presione el botón <span class="btn btn-light-success btn-sm"><i class="la la-plus-circle p-0"></i></span> para configurar los siguiente:
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <a href="javascript:" class="btn btn-light-info"><i class="fas fa-book-reader p-0"></i></a>
                                </td>
                                <td>Gestión de exámenes</td>
                                <td class="text-center">
                                    <a href="javascript:" class="btn btn-light-info"><i class="fas fa-star-half-alt p-0"></i></a>
                                </td>
                                <td>Notas estudiantes</td>
                                <td class="text-center">
                                    <a href="javascript:" class="btn btn-light-info"><i class="fas fa-graduation-cap p-0"></i></a>
                                </td>
                                <td>Secciones del curso</td>
                            </tr>                            
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-4">
                        {{-- <label>Buscar:</label> --}}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar curso..." id="buscar_curso">
                        </div>
                                                                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    @if(Session::has('success'))
                        <div class="alert alert-success my-3">
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
            </div>

            <div class="row" id="table_courses">
                
            </div>
            <!--end: Datatable-->
        </div>
    </div>
    <!--end::Card-->


</div>
<!--end::Container-->
@endsection

@section('modal')
<div class="modal fade" id="exampleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <h3>Recursos del curso</h3>
                    </div>
                    <style>
                        .hoverModal:hover {
                            cursor: pointer;
                            background: rgba(236, 233, 233, 0.747);
                        }
                    </style>
                    <div class="col-xl-6">
                        <!--begin::Stats Widget 5-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectRequisitos">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">REQUISITOS</span>
                                        <span class="font-weight-bold text-success font-size-lg">Requisitos del cuso</span>
                                    </div>
                                    <i class="fas fa-clipboard-list" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 5-->
                    </div>
                    <div class="col-xl-6">
                        <!--begin::Stats Widget 6-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectTemas">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">¿QUE APRENDERÉ?</span>
                                        <span class="font-weight-bold text-success font-size-lg">Temas del curso</span>
                                    </div>
                                    <i class="fas fa-book-reader" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 6-->
                    </div>
                    <div class="col-xl-6">
                        <!--begin::Stats Widget 6-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectComunidad">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">¿DIRIGIDO A?</span>
                                        <span class="font-weight-bold text-success font-size-lg">Dirigido a:</span>
                                    </div>
                                    <i class="fas fa-user-graduate" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 6-->
                    </div>

                    <div class="col-xl-6">
                        <!--begin::Stats Widget 6-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectDocentes">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">DOCENTES</span>
                                        <span class="font-weight-bold text-success font-size-lg">Docentes del curso</span>
                                    </div>
                                    <i class="fas fa-chalkboard-teacher" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 6-->
                    </div>

                    <div class="col-lg-12">
                        <h3>Configuración del curso</h3>
                        <hr>
                    </div>

                    <div class="col-xl-6">
                        <!--begin::Stats Widget 6-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectExamenes">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">EXÁMENES</span>
                                        <span class="font-weight-bold text-success font-size-lg">Gestión de exámenes</span>
                                    </div>
                                    <i class="fas fa-book-reader" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 6-->
                    </div>

                    <div class="col-xl-6">
                        <!--begin::Stats Widget 6-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectCalificacion">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">CALIFICACIONES</span>
                                        <span class="font-weight-bold text-success font-size-lg">Notas del estudiante</span>
                                    </div>
                                    <i class="fas fa-star-half-alt" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 6-->
                    </div>

                    <div class="col-xl-12">
                        <!--begin::Stats Widget 6-->
                        <div class="card card-custom card-stretch gutter-b hoverModal">
                            <a id="redirectSecciones">
                                <div class="card-body d-flex align-items-center py-0 mt-2">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">SECCIONES</span>
                                        <span class="font-weight-bold text-success font-size-lg">Seciones y clases</span>
                                    </div>
                                    <i class="fas fa-graduation-cap" style="font-size: 50px"></i>
                                </div>
                            </a>
                        </div>
                        <!--end::Stats Widget 6-->
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

    <script>
        function index() {

            listar_courses();

            $("#buscar_curso").on('keyup', function () {
                listar_courses();
            });

            $(document).on("click", '.paginate-go', function(e) {
                e.preventDefault();
                listar_courses($(this).attr('href').split('page=')[1]);
            });
        }

        function listar_courses(page = 1) {
            
            $.get(`/admin/courses/listar?page=${page}&filtro_search=${$("#buscar_curso").val()}`, function (data, textStatus, jqXHR) {
                $("#table_courses").html(data);

                $('[data-toggle="tooltip"]').tooltip()
            });
        }

        function desactivar(idcurso) {
            Swal.fire({
                title: '¿Seguro que quiere eliminar este registro?',
                text: "No se podra recuperar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f64e60',
                // cancelButtonColor: '#f64e60',
                confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.get(`/admin/course/eliminar/${idcurso}`, function (data, status) {
                        data = JSON.parse(data);

                        console.log(data);

                        if (data.status == true) {

                            Swal.fire('Eliminado', '', 'success');

                            $(`#tr_${idcurso}`).remove();

                            var rowCount = $('#table_secciones tr').length;

                            if (rowCount <= 1) {
                                $('#table_secciones').append('tr><td class="text-center" colspan="3">Aún no hay cursos registrados...</td></tr>');
                            }

                        }else{
                            alert('Ocurrio un error, se refescara la página');
                            location.reload();
                        }

                    });

                }else{

                }
            });
        }

        function mostrarModal(idcurso) {
            if (idcurso != "") {
                $("#exampleModal").modal('show');
                $("#redirectRequisitos").attr('href','/admin/requisitos/'+idcurso);
                $("#redirectTemas").attr('href','/admin/temas/'+idcurso);
                $("#redirectComunidad").attr('href','/admin/comunidad/'+idcurso);
                $("#redirectDocentes").attr('href','/admin/docentes/'+idcurso);
                /*Configuracion del curso*/
                $("#redirectExamenes").attr('href','/admin/course/examen/'+idcurso);
                $("#redirectCalificacion").attr('href','/admin/course/estudiantes/'+idcurso);
                $("#redirectSecciones").attr('href','/admin/course/secciones/'+idcurso);
            } else {
                alert("Recargar la página.");
            }
        }

        index();
    </script>
@endsection
