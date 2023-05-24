<label class="text-primary">Lista de permisos:</label>
<div class="form-group row">
    <ul class="list-unstyled col-md-6 ">
        @foreach ($permissions->take($permissions->count()/2) as $permission)
            <li>
                <label>
                    <input class="align-middle" type="checkbox" name="permissions[]" value="{{ $permission->id }}" @if($permission->checked) checked @endif>
                    {{$permission->description}}
                </label>
            </li>
        @endforeach
    </ul>
    <ul class="list-unstyled col-md-6">
        @foreach ($permissions->skip($permissions->count()/2) as $permission)
            <li>
                <label>
                    <input class="align-middle" type="checkbox" name="permissions[]" value="{{ $permission->id }}" @if($permission->checked) checked @endif>
                    {{$permission->description}}
                </label>
            </li>
        @endforeach
    </ul>
</div>