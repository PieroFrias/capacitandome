
@extends('layouts.app_webLogueado')
@section('tituloPagina','Mis cursos')
@section('styles')

@endsection
@section('contenido')

<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area my-courses-bread" style="background-color: #233d63 !important;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-content my-courses-bread-content">
                    <div class="section-heading">
                        <h2 class="section__title">Mi aprendizaje</h2>
                    </div>
                </div><!-- end breadcrumb-content -->
                <div class="my-courses-tab">
                    <div class="section-tab section-tab-2">
                        <ul class="nav nav-tabs" role="tablist" id="review">
                            <li role="presentation">
                                <a href="#all-courses" role="tab" data-toggle="tab" class="active" aria-selected="true">
                                    Mis cursos
                                </a>
                            </li>                                                    
                        </ul>
                    </div>
                </div>
            </div><!-- end col-lg-12 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!-- ================================
       START MY COURSES
================================= -->
<section class="my-courses-area padding-top-30px padding-bottom-90px">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="my-course-content-wrap">
                    <div class="tab-content">
                        
                        <div role="tabpanel" class="tab-pane fade active show" id="all-courses">
                            <div class="course-alert-info">
                                <div class="alert alert-info fade show d-flex align-items-center" role="alert">
                                    <i class="la la-users"></i> <a href="javascript:" class="alert-link">Comparte CAPACITÁNDOME con tus amigos</a>
                                </div>
                            </div><!-- end course-alert-info -->

                            <div class="my-course-content-body">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="my-collection-info">
                                            <div class="d-flex align-items-center pb-2">
                                                <h3 class="widget-title">Para la emisión del certificado</h3>
                                                <div class="my-collection-action-wrap ml-2">
                                                    <span class="fa fa-graduation-cap icon-element" title="Delete"></span>
                                                </div>
                                            </div>
                                            <p>En la parte superior del curso aparecerá una etiqueta <strong>VER CERTIFICADO</strong>, dar click para ver ó descargar</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-12 mt-3">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-md-5 offset-md-7">
                                                    <style>
                                                        .search {
                                                            height: auto;
                                                            line-height: inherit;
                                                            padding: 10px 20px 10px 45px;
                                                            font-size: 14px;
                                                            margin-right: 7px;
                                                            font-weight: 400;
                                                            background-color: #fff;
                                                            -webkit-box-shadow: 0 0 0 0;
                                                            -moz-box-shadow: 0 0 0 0;
                                                            box-shadow: 0 0 0 0;
                                                            -webkit-transition: all 0.3s;
                                                            -moz-transition: all 0.3s;
                                                            -ms-transition: all 0.3s;
                                                            -o-transition: all 0.3s;
                                                            transition: all 0.3s;
                                                            -webkit-border-radius: 5px;
                                                            -moz-border-radius: 5px;
                                                            border-radius: 5px;
                                                        }

                                                        .search:focus {
                                                            outline:none !important;
                                                            outline-width: 0 !important;
                                                            box-shadow: none;
                                                            -moz-box-shadow: none;
                                                            -webkit-box-shadow: none;
                                                        }                                                      
                                                    </style>
                                            
                                                    <form method="GET" action="{{ route('miscursos') }}" autocomplete="off">
                                                        <div class="input-group mb-5">
                                                            <input type="text" class="form-control search" name="search" placeholder="Buscar por titulo">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-success" type="submit">
                                                                    <span class="la la-search"></span> Buscar
                                                                </button>    
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (session('success'))
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="course-alert-info">
                                                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                                                    <i class="la la-users"></i> <a href="#" class="alert-link">{{session('success')}}</a>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="my-course-container">
                                    <div class="row">
                                        @if (Auth::user()->idrol == 2)
                                            @foreach ($miscursos as $micurso)
                                                <div class="col-lg-4 column-td-half">
                                                    <div class="card-item">
                                                        <div class="card-image">
                                                            <a href="{{ route('miaprendizaje', $micurso['idcurso']) }}" class="card__img">
                                                                <img src="{{ asset('/storage/cursos/'.$micurso['portada'].'') }}" alt="" height="230px">
                                                                <div class="play-button">
                                                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="-307.4 338.8 91.8 91.8" style=" enable-background:new -307.4 338.8 91.8 91.8;" xml:space="preserve">
                                                                        <style type="text/css">
                                                                            .st0{opacity:0.6;fill:#000000;border-radius: 100px;enable-background:new;}
                                                                            .st1{fill:#FFFFFF;}
                                                                        </style>
                                                                        <g>
                                                                        <circle class="st0" cx="-261.5" cy="384.7" r="45.9"/><path class="st1" d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364C-274.4,363.3-273.5,362.8-272.9,363.2z"/>
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                            </a>

                                                            @if ($micurso['certificado'] != '0')
                                                                <div class="card-badge">
                                                                    <a href="/storage/{{$micurso['certificado']}}" target="_blank" rel="Mi Certificado"><span class="badge-label"><i class="fa fa-graduation-cap"></i> VER CERTIFICADO</span></a>
                                                                </div>
                                                            @endif
                                                        </div><!-- end card-image -->

                                                        <div class="card-content p-4">
                                                            <h3 class="card__title mt-0" style="height: 70px;font-size: 18px;">
                                                                <a href="{{ route('miaprendizaje', $micurso['idcurso']) }}">{{ $micurso['titulo'] }}</a>
                                                            </h3>
                                                            <span>DOCENTE(S) :</span>
                                                            <p class="card__author" style="height: 80px;">                                                            
                                                                <span style="font-size: 13px;">
                                                                @foreach ($micurso['docentes'] as $docente)
                                                                    {{ $docente->nombre.' '.$docente->apellidos.',' }}
                                                                @endforeach
                                                                </span>
                                                            </p>
                                                            {{--<div class="course-complete-bar-2 mt-2">
                                                                <div class="progress-item mb-0">
                                                                    <p class="skillbar-title">Complete:</p>
                                                                    <div class="skillbar-box mt-1">
                                                                        <div class="skillbar" data-percent="70%">
                                                                            <div class="skillbar-bar skillbar-bar-1"></div>
                                                                        </div> <!-- End Skill Bar -->
                                                                    </div>
                                                                    <div class="skill-bar-percent">70%</div>
                                                                </div>
                                                            </div>--}}
                                                            <div class="rating-wrap d-flex mt-3">
                                                                <a href="{{ route('miaprendizaje', $micurso['idcurso']) }}" class="btn rating-btn" style="background: #233d63 !important;color: white">
                                                                <i class="fa fa-plus-circle"></i> Ver el curso
                                                                </a>
                                                            </div><!-- end rating-wrap -->
                                                        </div><!-- end card-content -->
                                                    </div><!-- end card-item -->
                                                </div><!-- end col-lg-4 -->
                                            @endforeach
                                        @endif

                                        @if (Auth::user()->idrol == 1)
                                            @foreach ($miscursos as $micurso)
                                                <div class="col-lg-4 column-td-half">
                                                    <div class="card-item">
                                                        <div class="card-image">
                                                            <a href="{{ route('miaprendizaje', $micurso->idcurso) }}" class="card__img">
                                                                <img src="{{ asset('/storage/cursos/'.$micurso->portada.'') }}" alt="" height="220px">
                                                                <div class="play-button">
                                                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="-307.4 338.8 91.8 91.8" style=" enable-background:new -307.4 338.8 91.8 91.8;" xml:space="preserve">
                                                                        <style type="text/css">
                                                                            .st0{opacity:0.6;fill:#000000;border-radius: 100px;enable-background:new;}
                                                                            .st1{fill:#FFFFFF;}
                                                                        </style>
                                                                        <g>
                                                                        <circle class="st0" cx="-261.5" cy="384.7" r="45.9"/><path class="st1" d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364C-274.4,363.3-273.5,362.8-272.9,363.2z"/>
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                            </a>                                                    
                                                        </div><!-- end card-image -->
                                                        <div class="card-content p-4">
                                                            <h3 class="card__title mt-0" style="height: 90px !important;">
                                                                <a href="{{ route('miaprendizaje', $micurso->idcurso) }}">{{ $micurso->titulo }}</a>
                                                            </h3>
                                                            {{--<div class="course-complete-bar-2 mt-2">
                                                                <div class="progress-item mb-0">
                                                                    <p class="skillbar-title">Complete:</p>
                                                                    <div class="skillbar-box mt-1">
                                                                        <div class="skillbar" data-percent="70%">
                                                                            <div class="skillbar-bar skillbar-bar-1"></div>
                                                                        </div> <!-- End Skill Bar -->
                                                                    </div>
                                                                    <div class="skill-bar-percent">70%</div>
                                                                </div>
                                                            </div>--}}
                                                            
                                                            <div class="rating-wrap d-flex mt-3">
                                                                <div class="col-lg-6">
                                                                    <a href="{{ route('miaprendizaje', $micurso->idcurso) }}" class="btn btn-secondary" style="background: #233d63 !important;color: white">
                                                                        <i class="fa fa-plus-circle"></i> Ver el curso
                                                                    </a>
                                                                </div>
                                                                
                                                                {{--@if (Auth::user()->idusuario == 27 && $micurso->idcurso != 14)--}}
                                                                <div class="col-lg-6">
                                                                    <a href="{{ route('revisarTarea', $micurso->idcurso) }}" class="btn btn-outline-success">
                                                                        <i class="fa fa-plus-circle"></i> Ver tareas
                                                                    </a>
                                                                </div>
                                                                {{--@endif--}}
                                                                
                                                            </div>
                                                            
                                                        </div><!-- end card-content -->
                                                    </div><!-- end card-item -->
                                                </div><!-- end col-lg-4 -->
                                            @endforeach
                                        @endif
                                    
                                        @if ($search != "" && count($miscursos) == 0)
                                            <div class="col-lg-12 mb-5">
                                                <div class="shopping-cart-detail-item mt-4">
                                                    <h3 class="widget-title font-size-20">
                                                        <i class="fa fa-info"></i> RESPUESTA
                                                    </h3>
                                                    <div class="pt-2">
                                                        <h2 class="line-height-35">
                                                            No hemos encontrado resultados para "<strong>{{ $search }}</strong>"
                                                        </h2>
                                                        <div class="contact-form-action pt-3">
                                                            <a href="{{ route('miscursos') }}" class="btn btn-success">
                                                                <span class="la la-arrow-left"></span> Ir a mis cursos
                                                            </a>
                                                        </div><!-- end contact-form-action -->
                                                    </div><!-- end shopping-cart-content -->
                                                </div>
                                            </div>
                                        @endif

                                        @if ($search == "" && count($miscursos) == 0)
                                            <div class="col-lg-12 mb-5">
                                                <div class="shopping-cart-detail-item mt-4">
                                                    <h3 class="widget-title font-size-20">
                                                        <i class="fa fa-info"></i> RESPUESTA
                                                    </h3>
                                                    <div class="pt-2">
                                                        <h2 class="line-height-35">
                                                            Aún no hay cursos que mostrar
                                                        </h2>
                                                        <div class="contact-form-action pt-3">
                                                            <a href="{{ route('cursos') }}" class="btn btn-success">
                                                                <span class="la la-arrow-left"></span> Ir a Tienda
                                                            </a>
                                                        </div><!-- end contact-form-action -->
                                                    </div><!-- end shopping-cart-content -->
                                                </div>
                                            </div>
                                        @endif                                
                                    </div>
                                </div>
                                <!--<div class="page-navigation-wrap mt-4 text-center">
                                    <div class="page-navigation-inner d-inline-block">
                                        <div class="page-navigation">
                                            $miscursos->links()
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div><!-- end tab-pane -->                       
                    </div>
                </div>
            </div><!-- end col-lg-12 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end my-courses-area -->
<!-- ================================
       START MY COURSES
================================= -->

<!-- start scroll top -->
<div id="scroll-top">
    <i class="fa fa-angle-up" title="Go top"></i>
</div>
<!-- end scroll top -->
@endsection

@section('script')
@endsection