@extends('layouts.app')

@section('content')
  <h1>Créer une tâche</h1>

  @if ($errors->any())
    <div>
    <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
  @endforeach
    </ul>
    </div>
  @endif

  <form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <div>
    <label for="title">Titre</label>
    <input type="text" name="title" id="title" required>
    </div>

    <div>
    <label for="description">Description</label>
    <textarea name="description" id="description"></textarea>
    </div>

    <button type="submit">Créer</button>
  </form>
@endsection