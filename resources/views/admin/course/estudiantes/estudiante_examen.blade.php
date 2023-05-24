@extends('layouts.app_admin')
@section('tituloPagina','Reporte de exámenes resueltos')

@section('styles')
@endsection

@section('subheader')
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <h3 class="text-dark font-weight-bold my-1 mr-5">Estudiante: {{ $estudiante->Persona->nombre }} {{ $estudiante->Persona->apellidos }}</h3>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <a href="{{route('admin_inicio')}}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
            <a href="{{ asset('/admin/course/estudiantes/' . $idCurso) }}" class="btn btn-light-primary font-weight-bolder btn-sm">
                <i class="fas fa-list"></i>
                Ver Estudiantes
            </a>
        </div>
    </div>
</div>
@endsection

@section('contenido')
<div class="container">
    <div class="card card-custom" id="cardExamen">
        <div class="card-header">
            <div class="card-title">
                <h2 class="card-label text-primary"> EXÁMENES RESUELTOS</h2>
            </div>
        </div>
        <div class="card-body p-0">                
                <div class="col-12">
                    <form action="#">
                        <div class="form-group m-4">
                            <select class="form-control" name="idexamen" id="idexamen" onchange="mostrarDetalle()" style="font-size: 15px">
                                <option selected disabled>Seleccione un Examen</option>
                                @foreach ($examenes as $item)
                                    <option value="{{ $item->idexamen }}">{{ $item->Seccion->titulo }} : {{ $item->titulo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <div class="col-md-12 px-15"  id="contentPreguntasExamenResuelto">
                    
                </div>

                <div class="col-lg-12 m-6" id="alerta">
                    <h4 class="text-center">Este apartado se muestra la resolución de los exámenes desarrollados por el estudiante.</h4>
                </div>
        </div>
        <!--end::Wizard-->
    </div>
</div>
@endsection

@section('modal')
@endsection

@section('script')
<script>    
    var card = new KTCard('cardExamen');
    function mostrarDetalle(){
        KTApp.block(card.getSelf(), {
            overlayColor: '#F3F6F9',type: 'loader',state: 'primary',opacity: 0.8,size: 'lg',message: 'Espere por favor...'
        });
        var idExamen = $("#idexamen").val();

        $.get('/admin/ver/resolucion/estudiante/' + {{ $estudiante->idusuario }} + '/' + idExamen, function(data){
            $("#contentPreguntasExamenResuelto").html(data);
            KTApp.unblock(card.getSelf());
            $("#alerta").hide();
        });
    }
</script>
@endsection