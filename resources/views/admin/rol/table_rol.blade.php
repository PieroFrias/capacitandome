<thead style="">
    <tr>
        <th style="width: 5%">N°</th>
        <th style="width: 30%">ROL</th>
        <th style="width: 20%;" class="text-center"><i class="fa fa-cogs"></i></th>
    </tr>
</thead>

@php
    $autoi = 1;
@endphp

<tbody>
    @foreach ($roles as $rol)
        @if ($rol->id != 1)
            <tr id="tr_{{ $rol->id }}">
                <td style="vertical-align: middle;">{{ $autoi++ }}.</td>
                <td style="vertical-align: middle;">{{ $rol->name }}</td>

                <td class="text-center" style="vertical-align: middle;">
                    <a href="javascript:" onclick="mostrarRoles({{ $rol->id }})" 
                        class="btn btn-light-warning font-weight-bold btn-sm my-1" data-toggle="tooltip" 
                        data-placement="top" data-original-title="Editar rol"><i class="fas fa-edit p-0"></i>
                    </a>
                    @if($rol->estado == 1)
                        <a href="javascript:void(0)" onclick="cambiarEstadoRol({{ $rol->id }}, 0)" 
                        class="btn btn-light-danger font-weight-bold btn-sm my-1" data-toggle="tooltip" 
                        data-placement="top" title="" data-original-title="Deshabilitar"><i class="fas fa-times ml-1"></i>
                        </a>
                    @else
                        <a href="javascript:void(0)" onclick="cambiarEstadoRol({{ $rol->id }}, 1)" 
                        class="btn btn-light-success font-weight-bold btn-sm my-1" data-toggle="tooltip" 
                        data-placement="top" title="" data-original-title="Habilitar"><i class="fas fa-check ml-1"></i>
                        </a>
                    @endif
                </td>
            </tr>
        @endif
    @endforeach

    @if (count($roles) == 0)
        <tr><td class="text-center" colspan="3">Aún no hay roles registrados...</td></tr>
    @endif
</tbody>