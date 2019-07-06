@if ($message = Session::get('success'))
<div class="alert alert-success">
    <button class="close" data-dismiss="alert">&times;</button>

    <p>{{ $message }}</p>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-error">
    <button class="close" data-dismiss="alert">&times;</button>

    <p>{{ $message }}</p>
</div>
@endif

@if ($errors->any())
<div class="alert alert-error" >
    <button class="close" data-dismiss="alert">&times;</button>

    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
</div>
@endif