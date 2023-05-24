@extends('layouts.app_admin')

@section('tituloPagina','Inicio')

@section('styles')
    {{-- <link href="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" /> --}}
@endsection

@section('subheader')
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <h5 class="text-dark font-weight-bold my-1 mr-5"> <i class="fa fa-home"></i> INICIO</h5>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{route('cerrarSesion')}}" class="btn btn-light-primary font-weight-bolder btn-sm mr-2">
                <i class="fas fa-sign-out-alt"></i> CERRAR SESIÓN
            </a>
        </div>
    </div>
</div>
@endsection

@section('contenido')
<!--begin::Container-->
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-12 col-xl-12">
                    <!--begin::Tiles Widget 2-->
                    @can('admin_course_list')
                        <div class="card card-custom bg-danger gutter-b" style="height: 130px;cursor: pointer;">
                            <!--begin::Body-->
                            <a href="{{ route('admin_course_list') }}">
                                <div class="card-body d-flex flex-column p-0" style="position: relative;">
                                    <!--begin::Stats-->
                                    <div class="flex-grow-1 card-spacer-x pt-6">
                                            <div class="text-center text-inverse-danger font-weight-bold">CURSOS</div>
                                            <div class="text-center p-5">
                                                <i class="fa fa-book" style="font-size: 30px;color: white"></i>
                                            </div>
                                    </div>
                                </div>
                            </a>
                            <!--end::Body-->
                        </div>
                    @endcan
                    <!--end::Tiles Widget 2-->
                    
                    <!--begin::Tiles Widget 3-->
                    <div class="card card-custom bg-success gutter-b" style="height: 130px;cursor: pointer;">
                        <!--begin::Body-->
                        <a href="/admin/pagos">
                            <div class="card-body d-flex flex-column p-0" style="position: relative;">
                                <div class="flex-grow-1 card-spacer-x pt-6">
                                        <div class="text-center text-inverse-danger font-weight-bold">PAGOS</div>
                                        <div class="text-center p-5">
                                            <i class="fa fa-list" style="font-size: 30px;color: white"></i>
                                        </div>
                                </div>
                            </div>
                        </a>
                        <!--end::Body-->
                    </div>
                    <!--end::Tiles Widget 3-->
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-12 col-xl-12">
                    <!--begin::Tiles Widget 4-->
                    @can('admin_personas')
                        <div class="card card-custom bg-warning gutter-b" style="height: 130px;cursor: pointer;">
                            <!--begin::Body-->
                            <a href="{{route('admin_personas')}}">
                                <div class="card-body d-flex flex-column p-0" style="position: relative;">
                                    <div class="flex-grow-1 card-spacer-x pt-6">
                                        <div class="text-center text-inverse-danger font-weight-bold">PERSONA</div>
                                        <div class="text-center p-5">
                                            <i class="fa fa-user-circle" style="font-size: 30px;color: white"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <!--end::Body-->
                        </div>
                    @endcan
                    <!--end::Tiles Widget 4-->
                    
                    <!--begin::Tiles Widget 5-->
                    <div class="card card-custom bg-info gutter-b" style="height: 130px;cursor: pointer;">
                        <!--begin::Body-->
                        <a href="{{route('admin_comentarios')}}">
                            <div class="card-body d-flex flex-column p-0" style="position: relative;">
                                <div class="flex-grow-1 card-spacer-x pt-6">
                                    <div class="text-center text-inverse-danger font-weight-bold">MENSAJES</div>
                                    <div class="text-center p-5">
                                        <i class="fa fa-comments" style="font-size: 30px;color: white"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <!--end::Body-->
                    </div>
                    <!--end::Tiles Widget 5-->
                </div>
            </div>
            <!--begin::Mixed Widget 20-->
            <div class="row">
               <div class="col-lg-12">                
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bolder text-dark">{{count($mensajes)}} MENSAJES</h3>
                        <div class="card-toolbar">
                            <div class="dropdown dropdown-inline">
                                <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ki ki-bold-more-ver"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <!--begin::Navigation-->
                                    <ul class="navi navi-hover">
                                        <li class="navi-header pb-1">
                                            <span class="text-primary text-uppercase font-weight-bold font-size-sm">MENSAJES:</span>
                                        </li>
                                        <li class="navi-item">
                                            <a href="javascript:" class="navi-link">
                                                <span class="navi-icon">
                                                    <i class="fa fa-comments"></i>
                                                </span>
                                                <span class="navi-text">VER TODO</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!--end::Navigation-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body pt-0">
                        @foreach ($mensajes as $mensaje)
                            <div class="mb-10">
                                @php
                                    $fecha = date_create($mensaje->fecha);
                                    $cadena = date_format($fecha,"Y/m/d");
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45 symbol-light mr-5">
                                        <span class="symbol-label">
                                            <i class="fa fa-user-circle" style="font-size: 30px"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <a href="javascript:" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">{{$mensaje->nombre_apellido}}</a>
                                        <span class="text-muted font-weight-bold">{{$cadena}}</span>
                                    </div>
                                </div>
                                <p class="text-dark-50 m-0 pt-5 font-weight-normal">{{$mensaje->mensaje}}</p>
                            </div>
                        @endforeach
                    </div>
                    <!--end::Body-->
                </div>
               </div>
            </div>
            <!--end::Mixed Widget 20-->
        </div>
        
        <div class="col-lg-9">
            <!--begin::Advance Table Widget 4-->
            <div class="card card-custom card-stretch gutter-b" id="cardPagos">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">REPORTE PAGOS</span>
                        <span class="text-muted mt-3 font-weight-bold font-size-sm">Verifique que los pagos realizados sean correctos</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-0 pb-3">
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Buscar por apellido</label>
                                    <div class="input-icon input-icon-right">
                                     <input type="text" id="buscarPago" class="form-control" placeholder="Buscar por apellido..."/>
                                     <span><i class="flaticon2-search-1 icon-md"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive" id="pagosPaginate"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Advance Table Widget 4-->
        </div>
    </div>
</div>
<!--end::Container-->
@endsection

@section('modal')
@endsection

@section('script')
    {{--<script src="{{ asset('/recursos/admin/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>--}}
    <script src="{{ asset('/recursos/ajax/admin/inicio/inicio.js') }}"></script>
@endsection