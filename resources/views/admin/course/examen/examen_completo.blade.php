@extends('layouts.app_admin')

@section('tituloPagina','Exámenes del Curso')

@section('styles')
    {{--<link href="{{ asset('/recursos/admin/assets/css/pages/wizard/wizard-1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />--}}
@endsection

@section('subheader')
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Previsualización del Examen: "{{ $exam->titulo }}"</h5>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('admin_inicio') }}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2"><i class="fa fa-home"></i> Inicio</a>
                <a href="{{ asset('/admin/course/examen/' . $exam->idcurso) }}" class="btn btn-light-primary font-weight-bolder btn-sm">
                    <i class="fas fa-list"></i>
                    Ver Exámenes
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
                    <h3 class="h2 card-label text-primary py-2"> {{ $exam->titulo }} </h3>
                </div>
            </div>
            <div class="card-body">
                <h6> {{ $exam->descripcion }} </h6>
                <br><br>
                <div class="row">
                    @php
                        $autoi = 1;
                    @endphp
                    
                    @foreach ($exam->Preguntas as $preg)
                        <div class="col-12 card card-custom gutter-b">
                            <div class="card-header" style="min-height: 40px;">
                                <div class="card-title">
                                    <h5 class="card-label text-success"> {{$autoi++}}. {{ $preg->nombre }} </h5>
                                </div>
                            </div>
                            <div class="card-body py-0 px-3 m-0">
                                @foreach ($preg->Alternativas as $alt)
                                    <p></p>
                                    <div class="custom-control custom-radio mb-3">
                                        <input class="custom-control-input" id="pregunta{{ $alt->idalternativa }}" name="Preg{{ $preg->idpregunta }}" type="radio">
                                        <label class="custom-control-label" for="pregunta{{ $alt->idalternativa }}">
                                            <span>{{ $alt->nombre }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="card-footer px-0 pt-3 pb-0 m-0">
                                <div class="alert alert-success alert-dismissible show" role="alert">
                                    <span class="alert-inner--icon">
                                        <i class="ni ni-bell-55"></i>
                                    </span>
                                    <span class="alert-inner--text ml-1 pt-0">
                                        Respuesta:
                                        <strong> "{{ $preg->Correcta->nombre }}"</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
@endsection