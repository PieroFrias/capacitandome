<thead class="table-primary">
    <tr>
        <th scope="col">#</th>
        <th scope="col">Título</th>
        <th style="width: 40%" scope="col">Sección</th>
        <th scope="col">Fecha final</th>
        <th scope="col" class="text-center">Acción</th>
    </tr>
</thead>

<tbody>
    @foreach ($examen->examenes as $key => $exam)
        <tr id="tr_{{ $exam->idexamen }}">
            <td class="align-middle" scope="col">{{ $key+1 }}</td>
            <td class="align-middle" scope="col">{{ $exam->titulo }}</td>
            <td class="align-middle" scope="col"><strong>{{ $exam->Seccion->titulo }}</strong></td>
            {{--<td>{{ $exam->descripcion }}</td>--}}
            <td class="align-middle" scope="col">{{ date('d-m-Y | H:i:s', strtotime($exam->fecha_final)) }}</td>
            <td scope="col" class="text-center align-middle">
                <a href="{{ asset('/admin/course/examen/preguntas/' . $exam->idexamen)  }}" class="btn btn-light-info font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Ver preguntas"><i class="fas fa-question-circle p-0"></i></a> 
                {{-- <a href="{{ asset('/admin/course/examen/resuelto/' . $exam->idexamen . '/') }}" class="btn btn-light-info font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Ver resoluciones"><i class="fas fa-file-contract p-0"></i></a> --}}
                <a href="{{ asset('/admin/course/ver/examen/' . $exam->idexamen) }}" class="btn btn-light-info font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Previsualizar"><i class="fas fa-eye p-0"></i></a>
                {{-- <a href="javascript:void(0)" onclick="mostrarNotasEstudiantes({{ $exam->idexamen }})" class="btn btn-light-info font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Notas de los estudiantes">
                    <i class="fas fa-list-ol p-0"></i>
                </a> --}}
                <a href="{{ asset('/admin/notas/estudiantes/examen/' . $exam->idexamen) }}" class="btn btn-light-info font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Ver Notas de los estudiantes">
                    <i class="fas fa-list-ol p-0"></i>
                </a>
                <a href="{{ asset('admin/course/mostrar/examen/' . $exam->idexamen) }}" class="btn btn-light-warning font-weight-bold btn-sm my-1" data-toggle="tooltip" data-placement="top" title="Editar">
                    <i class="fas fa-edit p-0"></i>
                </a>
                @if($exam->estado == 1)
                    <a href="javascript:void(0)" onclick="cambiarEstadoExamen({{$exam->idexamen}}, 0, {{ $exam->idcurso }})" 
                        class="btn btn-light-danger font-weight-bold btn-sm my-1" data-toggle="tooltip" 
                        data-placement="top" title="" data-original-title="Deshabilitar"><i class="fas fa-times ml-1"></i>
                    </a>
                @else
                    <a href="javascript:void(0)" onclick="cambiarEstadoExamen({{$exam->idexamen}}, 1, {{ $exam->idcurso }})" 
                        class="btn btn-light-success font-weight-bold btn-sm my-1" data-toggle="tooltip" 
                        data-placement="top" title="" data-original-title="Habilitar"><i class="fas fa-check ml-1"></i>
                    </a>
                @endif
            </td>
        </tr>
    @endforeach

    @if (count($examen->examenes) == 0)
        <tr>
            <td class="text-center" colspan="5">Aún no hay exámenes registrados...</td>
        </tr>
    @endif
</tbody>