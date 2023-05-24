<div class="row">
    <div class="col-lg-12">
        <div class="filter-bar">
            <div class="row mb-2">
                <div class="col-md-12">
                    @if ($resolver_examen)                        
                        <span class="h1 my-2 text-info">NOTA: {{ $resolver_examen->calificacion_final }}/{{ $resolver_examen->calificion_total }}</span>
                    @else
                        <span class="h1 my-2 text-info">NOTA: 0 <i class="text-info">(Este examen no ha sido resulto por el estudiante)</i></span>
                    @endif
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <span class="h1 my-2">{{ $examen->titulo }}</span>
                </div>
            </div>
            
            <div class="row mb-2">
                <div class="col-md-12">
                    <h3 class="text-muted mb-4">
                        {{ $examen->descripcion }}
                    </h3>
                </div>                
            </div>

            @foreach ($examen->preguntas as $index => $pregunta)                
                <div class="row">
                    <div class="col-md-12">
                        @php
                            $respuesta_correcta = '';
                            $alternativa_correcta = false;
                        @endphp

                        <h4 class="text-primary"> <b>{{ ($index + 1) }}.-</b> <span class="font-weight-bold">{{ $pregunta->nombre }}</span> <i class="text-muted h6"> ({{ $pregunta->puntos }} puntos)</i></h4>
                    
                        @foreach ($pregunta->alternativas as $alternativa)
                            @php
                                $flat_respuesta = false;
                            @endphp

                            @if ($resolver_examen)
                                @foreach ($resolver_examen->DetalleResolverExamen as $detalle_resolver_examen)
                                    @if ($detalle_resolver_examen->idpregunta == $alternativa->idpregunta && $detalle_resolver_examen->idalternativa == $alternativa->idalternativa)
                                        @php
                                            $flat_respuesta = true;
                                        @endphp

                                        @if ($alternativa->correcta == 1)
                                            @php
                                                $alternativa_correcta = true;
                                            @endphp
                                        @endif
                                    @endif
                                @endforeach
                            @endif

                            <ul class="ml-4" style="list-style-type: none;">
                                <li class="h5 my-2">
                                    <span class="badge {{ ($flat_respuesta) ? 'badge-primary' : '' }}">{{ $alternativa->nombre }}</span>
                                </li>                            
                            </ul>

                            @if ($alternativa->correcta == 1)
                                @php
                                        $respuesta_correcta = $alternativa->nombre ;
                                @endphp
                            @endif
                        @endforeach

                        {{-- <p><span class="{{ ($alternativa->correcta == 1)? 'badge badge-success' :'' }}">{{ $alternativa->nombre }}</span></p> --}}

                        <div class="{{ ($alternativa_correcta) ? 'alert alert-success': 'alert alert-danger' }} alert-dismissible show" role="alert">
                            <span class="alert-inner--icon">
                                <i class="ni ni-bell-55"></i>
                            </span>
                            <span class="alert-inner--text ml-1 pt-0">
                                @if ($alternativa_correcta)
                                    Bien +{{ $pregunta->puntos }} puntos, la respuesta es: {{ $respuesta_correcta }}
                                @else
                                    Mal, la respuesta es: {{ $respuesta_correcta }}
                                @endif
                                
                                <strong> </strong>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>