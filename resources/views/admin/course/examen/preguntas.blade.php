@extends('layouts.app_admin')

@section('tituloPagina', 'Preguntas del Examen')

@section('styles')
    {{--<link href="{{ asset('/recursos/admin/assets/css/pages/wizard/wizard-1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />--}}
@endsection

@section('subheader')
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Examen: {{ $preguntas->titulo }}</h5>
                </div>
            </div>
            <!--end::Info-->

            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Actions-->
                <a href="{{route('admin_inicio')}}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
                <a href="{{ asset('/admin/course/examen/' . $preguntas->idcurso) }}" class="btn btn-light-primary font-weight-bolder btn-sm">
                    <i class="fas fa-list"></i>
                    Ver Exámenes
                </a>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
@endsection

@section('contenido')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td class="text-center" style="background: cadetblue;color:white">
                                    <strong>Paso 01</strong>
                                </td>
                                <td>Ingrese una pregunta y asigne un pantaje.</td>
                                <td class="text-center" style="background: cadetblue;color:white">
                                    <strong>Paso 02</strong>
                                </td>
                                <td>Ingrese una alternativa y marque el check si es correcta o incorrecta.</td>
                                <td class="text-center" style="background: cadetblue;color:white">
                                    <strong>Paso 03</strong>
                                </td>
                                <td>Click en el botón agregar alternativa.</td>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">                        
                    <div class="col-lg-6">
                        {{--<div class="card card-custom gutter-b">--}}
                            {{--<div class="card-header" style="min-height: 40px;">
                                <div class="card-title">--}}
                                    <h5 class="card-label"> AGREGAR PREGUNTAS AL EXÁMEN </h5>
                                    <hr>
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="card-body">--}}
                                
                                <form id="FormPreguntas" action="{{ route('preguntas_guardar') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="idpregunta" id="idpregunta">
                                    <input type="hidden" name="idexamen" value="{{ $preguntas->idexamen }}">
                                    <div class="row">                                    
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="form-group mb-4">
                                                        <label>PREGUNTA  <span class="text-danger">*</span></label>
                                                        <input type="text" name="pregunta" id="pregunta" class="form-control" placeholder="Ingrese la pregunta..." value="{{ old('pregunta') }}">
                                                    </div>
                                                </div>
            
                                                <div class="col-md-3">
                                                    <div class="form-group mb-4">
                                                        <label>PUNTOS  <span class="text-danger">*</span></label>
                                                        <input type="number" name="puntos" id="puntos" class="form-control" placeholder="Puntos..." value="{{ old('puntos') }}" min="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <h3 class="card-label mt-2 text-primary"> ALTERNATIVAS </h3>
                                            
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="nombre_alternativa" placeholder="Ingrese la alternativa...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <input type="checkbox" class="form-control" id="correcta" data-toggle="tooltip" data-placement="top" title="Correcta o Incorrecta">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <button class="btn btn-primary btn-block" id="btn_add_alt" type="button" onclick="comprobarCorrecta()" data-toggle="tooltip" data-placement="top" title="Click para agregar">
                                                                <i class="fas fa-plus-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-hover" id="table_alternativa">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Alternativa</th>
                                                                <th>Correcta</th>
                                                                <th class="text-center">Acción</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr id="20">
                                                                <td class="text-center" colspan="4"> Sin registros de alternativas...</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
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
                                            <button type="button" class="btn btn-primary float-right" id="btn_guardar" onclick="comprobarForm()">
                                                <i class="fas fa-save"></i> GUARDAR
                                            </button>
                                            {{-- <button type="button" onclick="limpiar()" class="btn btn-warning float-right mr-2" id="btn_limpiar">
                                                    LIMPIAR
                                            </button> --}}
                                            <button type="button" class="btn btn-info float-right" id="btn_nuevo"><i class="la la-plus-circle"></i> NUEVO</button>
                                            <button type="button" class="btn btn-secondary float-right mr-2" id="btn_cancelar">CANCELAR</button>
                                        </div>
                                    </div>
                                </form>
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>                       

                    <div class="col-lg-6">
                        {{--<div class="card card-custom gutter-b">--}}
                            {{--<div class="card-header" style="min-height: 40px;">
                                <div class="card-title">--}}
                                    <h5 class="card-label text-primary"> LISTA DE PREGUNTAS </h5>
                                    <hr>
                                {{--</div>
                            </div>--}}
                            {{--<div class="card-body">--}}
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table table-bordered table-hover" id="table_preguntas">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 62%">PREGUNTA</th>
                                                    <th style="width: 3%">PUNTOS</th>
                                                    <th class="text-center" style="width: 30%">ACCIÓN</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($preguntas->Preguntas as $key => $preg)
                                                    <tr id="tr_{{ $preg->idpregunta }}">
                                                        <td class="align-middle">{{ $key+1 }}</td>
                                                        <td class="align-middle">{{ $preg->nombre }}</td>
                                                        <td class="align-middle text-center">{{ $preg->puntos }}</td>
                                                        <td class="text-center align-middle">
                                                            <a href="javascript:void(0)" onclick="alternativas({{ $preg->idpregunta }})" class="btn btn-light-info font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Alternativas">
                                                                <i class="fas fa-eye p-0"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="editarPregunta({{ $preg->idpregunta }})" class="btn btn-light-warning font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Modificar Pregunta">
                                                                <i class="fas fa-edit p-0"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="desactivar({{ $preg->idpregunta }})" class="btn btn-light-danger font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Eliminar">
                                                                <i class="fas fa-times p-0"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if (count($preguntas->Preguntas) == 0)
                                                    <tr>
                                                        <td class="text-center" colspan="4">Aún no hay preguntas creadas...</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
            </div>
            <!--end::Wizard-->
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="ModalAlternativas" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="preguntaModal"></h5>
                </div>
                <div class="modal-body">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Alternativa</th>
                                <th>Correcta?</th>
                            </tr>
                        </thead>
                        <tbody id="contentModalAlternativa">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{--<script src="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('/recursos/admin/assets/js/pages/crud/forms/widgets/select2.js') }}"></script>--}}
    <script src="{{ asset('/recursos/admin/assets/js/pages/features/miscellaneous/toastr.js') }}"></script>

    <script>
        var contador = 0;
        var contCorr = 0;

        function preguntas() {
            activar_form(false);

            $("#btn_nuevo").on('click', function () {
                activar_form(true);
            });

            $("#btn_cancelar").on('click', function () {
                limpiar();
                activar_form(false);
            });
        }

        function makeid() {
            var result           = '';
            var characters       = '0123456789';
            var charactersLength = characters.length;

            for ( var i = 0; i < 10; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }

            return result;
        }

        function activar_form(flat) {
            if (flat) {
                $("#btn_nuevo").hide();

                $("#btn_guardar").show();
                // $("#btn_limpiar").show();
                $("#btn_cancelar").show();

                $("#pregunta").prop("disabled", false);
                $("#puntos").prop("disabled", false );

                $("#nombre_alternativa").prop("disabled", false );
                $("#correcta").prop("disabled", false );
                $("#btn_add_alt").prop("disabled", false );
            } else {
                $("#btn_nuevo").show();

                $("#btn_guardar").hide();
                // $("#btn_limpiar").hide();
                $("#btn_cancelar").hide();

                $("#pregunta").prop("disabled", true);
                $("#puntos").prop("disabled", true );

                $("#nombre_alternativa").prop("disabled", true );
                $("#correcta").prop("disabled", true );
                $("#btn_add_alt").prop("disabled", true );
            }
        }

        function limpiar(){
            contCorr = 0;

            $(".tbl_alt_preg").remove();

            $("#20").remove();
            $("#table_alternativa").append('<tr id="20"><td class="text-center" colspan="4"> Sin registros de alternativas...</td></tr>');

            $("#pregunta").val('');
            $("#puntos").val('');

            $("#nombre_alternativa").val('');
            $("#correcta").val('');
            $("#btn_add_alt").val('');
        }

        function editarPregunta(idPreg){
            contador = 0;

            activar_form(true);
            $("#20").remove();
            $(".tbl_alt_preg").remove();

            $.get('/admin/courses/mostrar/pregunta/editar/' + idPreg, function(data){
                data = JSON.parse(data);

                $("#pregunta").val(data.nombre);
                $("#puntos").val(data.puntos);
                $("#idpregunta").val(data.idpregunta);

                $.each(data.alternativas, function(index, datos){
                    var id = makeid();
                    contador++;
                    var estado = '';

                    if(datos.correcta == 1){
                        estado   = '<span class="btn btn-light-success">Correcta<span>';
                        contCorr = 1;
                    } else {
                        estado = '<span class="btn btn-light-danger">Incorrecta<span>';
                    }

                    var tr = '<tr class="tbl_alt_preg" id="' + id + '">'
                            + '<td>' + contador + '</td>'
                            + '<td>'
                                + '<p>' + datos.nombre + '</p>'
                                + '<input name="alternativas[]" type="hidden" value="' + datos.nombre + '">'
                            + '</td>'
                            + '<td>'
                                + '<p>' + estado + '</p>'
                                + '<input name="correctas[]" type="hidden" value="' + datos.correcta + '">'
                            + '</td>'
                            + '<td class="text-center">'
                                + '<a class="btn btn-danger btn-xs px-3 py-2" onclick="quitar_alternativa(' + id + ', ' + datos.correcta + ')">'
                                    + '<i class="fa fa-trash p-0"></i>'
                                + '</a>'
                            + '</td>'
                        + '</tr>';

                    $("#table_alternativa").append(tr);
                });
            });
        }

        function quitar_alternativa(id, estado){
            $("table#table_alternativa tr#" + id).remove();

            contador--;

            if(estado == 1){
                contCorr = 0;
            }

            if(contador == 0){
                var tr = '<tr id="20"><td class="text-center" colspan="4"> Sin registros de alternativas...</td></tr>';

                $("#table_alternativa").append(tr);
            }
        }

        function comprobarCorrecta(){
            if($("#correcta").prop('checked')){
                if(contCorr == 1){
                    //alert('Ya existe una respuesta correcta');
                    toastr.error('Ya existe una respuesta correcta.', 'ERROR!')
                } else {
                    agregar_alternativa();
                }

            } else {
                agregar_alternativa();
            }
        }

        function agregar_alternativa(){
            if ($("#pregunta").val() !='' && $("#puntos").val() != '') {
                if($("#nombre_alternativa").val() != ""){
                    $("#20").remove();

                    contador++;

                    var id = makeid();
                    var nombre = $("#nombre_alternativa").val();
                    var correcta = '';
                    var estado   = '';

                    if( $("#correcta").prop('checked') ) {
                        correcta = '<span class="btn btn-light-success">Correcta<span>';
                        estado   = '1';
                        contCorr = 1;
                    } else{
                        correcta = '<span class="btn btn-light-danger">Incorrecta<span>';
                        estado   = '0';
                    }

                    var tr = '<tr class="tbl_alt_preg" id="' + id + '">'
                                + '<td>' + contador + '</td>'
                                + '<td>'
                                    + '<p>' + nombre + '</p>'
                                    + '<input name="alternativas[]" type="hidden" value="' + nombre + '">'
                                + '</td>'
                                + '<td>'
                                    + '<p>' + correcta + '</p>'
                                    + '<input name="correctas[]" type="hidden" value="' + estado + '">'
                                + '</td>'
                                + '<td class="text-center">'
                                    + '<a class="btn btn-danger btn-xs px-3 py-2" onclick="quitar_alternativa(' + id + ', ' + estado + ')">'
                                        + '<i class="fa fa-trash p-0"></i>'
                                    + '</a>'
                                + '</td>'
                            + '</tr>';

                    $("#table_alternativa").append(tr);

                    $("#nombre_alternativa").val('');
                    $("#correcta").prop('checked', false);

                } else {
                    toastr.error('Debe ingresar una alternativa.', 'ERROR!')
                }
            } else {
                toastr.error('Debe ingresar una pregunta y su puntaje.', 'ERROR!')
            }
        }

        function alternativas(id){
            $.get('admin/courses/mostrar/alternativas/' + id, function(data){
                data = JSON.parse(data);

                var count = 1;

                $("#ModalAlternativas").modal('show');

                $("#preguntaModal").text(data.pregunta);

                $(".tr_alt_modal").remove();
                $.each(data.alternativas, function (index, datos) {
                    var estado = '';

                    if(datos.correcta == 1){
                        estado = '<span class="badge badge-success">Correcta</span>';
                    } else {
                        estado = '<span class="badge badge-danger">Incorrecta</span>';
                    }

                    var alt =   '<tr class="tr_alt_modal">'
                                    + '<td>' + count + '</td>'
                                    + '<td>' + datos.nombre + '</td>'
                                    + '<td>' + estado + '</td>'
                                + '</tr>';

                    $("#contentModalAlternativa").append(alt);

                    count++;

                });
            });
        }

        function desactivar(idpregunta) {
            Swal.fire({
            title: '¿Seguro que quiere eliminar este registro?',
            text: "No se podra recuperar",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f64e60',
            confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get(`/admin/course/eliminar/pregunta/${idpregunta}`, function (data, status) {
                        data = JSON.parse(data);

                        console.log(data);

                        if (data.status == true) {
                            Swal.fire('Eliminado', '', 'success');

                            $(`#tr_${idpregunta}`).remove();

                            var rowCount = $('#table_preguntas tr').length;

                            if (rowCount <= 1) {
                                $('#table_preguntas').append('tr><td class="text-center" colspan="4">Aún no hay preguntas creadas...</td></tr>');
                            }

                        }else{
                            alert('Ocurrio un error, se refescara la pagina');
                            location.reload();
                        }
                    });
                }else{

                }
            })
        }

        function comprobarForm(){
            //AGREGUÉ PREGUNTA A VALIDAR
            if($("#pregunta").val() !='' && $("#puntos").val() != ''){
                if(contCorr > 0){
                    $("#FormPreguntas").submit();
                } else {
                    alert('Debe marcar una alternativa como correcta');
                }

            } else {
                //alert('Debe ingresar los puntos de la pregunta');
                toastr.error('Debe ingresar una pregunta y su puntaje.', 'ERROR!')
            }

        }

        preguntas();
    </script>
@endsection