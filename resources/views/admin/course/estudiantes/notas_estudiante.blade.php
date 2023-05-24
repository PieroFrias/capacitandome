<div class="modal-body">
    <div class="col-md-12 mb-4">
        <h5 class=" text-center m-0">{{ $user->Persona->nombre }} {{ $user->Persona->apellidos }}</h5>
    </div>
    @foreach ($examenes as $seccion)
        <div class="col-md-12">
            <div class="card card-custom">
                {{--<div class="card-header" style="min-height: 30px;">
                    <div class="card-title">
                        <h6 class="card-label"> Sección: {{ $seccion->titulo }} </h6>
                    </div>
                </div>--}}
                <div class="card-body">
                    <h6 class="card-label"> Sección: {{ $seccion->titulo }} </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-primary">
                                <tr>
                                    <th width="5%">N°</th>
                                    <th width="60%">Exámen</th>
                                    <th width="20%" class="text-center">Fecha final</th>
                                    <th width="15%" class="text-center">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($seccion->Examenes) > 0)
                                    @php
                                        $promedio = 0;
                                        $cant = count($seccion->Examenes);
                                    @endphp
                                    @foreach ($seccion->Examenes as $key => $item)
                                        <tr>
                                            <td>{{ (($key++) + 1) }}</td>
                                            <td>{{ $item->titulo }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->fecha_final)->format('d/m/Y') }}
                                                <br>
                                                <small class="text-primary">
                                                    {{ \Carbon\Carbon::parse($item->fecha_final)->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                @if (count($item->ResolverExamen) > 0)
                                                    {{ $item->ResolverExamen->first()->calificacion_final }}
                                                    @php
                                                        $promedio += $item->ResolverExamen->first()->calificacion_final;
                                                    @endphp
                                                @else
                                                    0 <br><small class="text-danger">(No Resuelto)</small>
                                                    @php
                                                        $promedio += 0;
                                                    @endphp
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-success">
                                        <td colspan="3"><b>Promedio</b></td>
                                        <td class="text-center"><b>{{ $promedio / $cant }}</b></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center" colspan="5"><span class="text-danger">No hay examenes en esta sección</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>EXÁMEN</th>
                <th>FECHA FINAL</th>
                <th>NOTA TOTAL</th>
                <th>NOTA FINAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notas as $key => $item)
                <tr>
                    <td>{{ $key++ }}</td>
                    <td>{{ $notas->ResolverExamenes->Examen->Curso->titulo }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
</div>