<thead style="">
    <tr>
        <th style="width: 5%">N°</th>
        <th style="width: 55%">DOCENTE</th>
        <th style="width: 35%">ESTADO</th>
        <th style="width: 5%" class="text-center"><i class="fa fa-cogs"></i></th>
    </tr>
</thead>

@php
    $autoi = 1;
@endphp

<tbody>
    @foreach ($docentes as $item)
        <tr id="tr_{{ $item->iddocente }}">
            <td>{{ $autoi++ }}.</td>
            <td>{{ $item->nombre." ".$item->apellidos }}</td>

            @if($item->estado == 1)
                <td class="text-center" style="vertical-align: middle;">
                    <a href="javascript:void(0)" 
                    onclick="cambiarEstadoDocente({{ $item->iddocente }}, 0, {{ $item->idcurso }})" 
                        class="btn btn-success text-white font-weight-bold btn-sm" data-toggle="tooltip" 
                        data-placement="top" title="" data-original-title="Deshabilitar">
                        Habilitado <i class="fas fa-check ml-1"></i>
                    </a>
                </td>
            @else
                <td class="text-center" style="vertical-align: middle;">
                    <a href="javascript:void(0)" 
                    onclick="cambiarEstadoDocente({{ $item->iddocente }}, 1, {{ $item->idcurso }})" 
                        class="btn btn-danger text-white font-weight-bold btn-sm" data-toggle="tooltip" 
                        data-placement="top" title="" data-original-title="Habilitar">
                        Deshabilitado <i class="fas fa-times ml-1"></i>
                    </a>
                </td>
            @endif

            <td class="text-center">
                <a href="javascript:" onclick="mostrarComunidad({{ $item->iddocente }})" 
                    class="btn btn-light-warning font-weight-bold btn-sm" data-toggle="tooltip" 
                    data-placement="top" data-original-title="Editar curso"><i class="fas fa-edit p-0"></i>
                </a>
                {{-- <a href="javascript:void(0)" onclick="eliminarComunidad({{$item->iddocente}})" 
                    class="btn btn-light-danger font-weight-bold btn-sm" data-toggle="tooltip" 
                    data-placement="top" title="" data-original-title="Eliminar curso"><i class="fas fa-trash p-0"></i>
                </a> --}}
            </td>
        </tr>
    @endforeach
    @if (count($docentes) == 0)
        <tr><td class="text-center" colspan="5">Aún no hay docentes registrados...</td></tr>
    @endif
</tbody>