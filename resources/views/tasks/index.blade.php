@extends('layouts.app')

@section('content')
  <div class="container mx-auto p-4">
    <h1 class="text-2xl mb-4">Tableau Kanban</h1>
    <div class="flex space-x-4">
    <a href="{{ route('tasks.create') }}">Ajouter une tâche</a>
    <form action="{{ route('task-columns.store') }}" method="POST">
      @csrf
      <input type="text" name="name" placeholder="Nom de la colonne" required>
      <button type="submit">Ajouter colonne</button>
    </form>
    @php
      $columns = [
      'to_do' => 'À faire',
      'in_progress' => 'En cours',
      'done' => 'Fait',
      'cancelled' => 'Annulé',
      ];
  @endphp

    @foreach($columns as $key => $label)
    <div class="w-1/4 bg-gray-100 p-2 rounded">
      <h2 class="text-lg font-semibold mb-2">{{ $label }}</h2>
      <div class="space-y-2" id="col-{{ $key }}">
      @foreach($tasks->where('status', $key) as $task)
      <div class="task-card bg-white p-3 rounded shadow" data-id="{{ $task->id }}">
      <h3 class="font-medium">{{ $task->title }}</h3>
      <p class="text-sm">{{ Str::limit($task->description, 50) }}</p>
      </div>
    @endforeach
      </div>
    </div>
  @endforeach
    </div>
  </div>
@endsection

@push('scripts')
  <script type="module">
    import Sortable from 'sortablejs';

    const statusMap = {
    'col-to_do': 'to_do',
    'col-in_progress': 'in_progress',
    'col-done': 'done',
    'col-cancelled': 'cancelled',
    };

    Object.keys(statusMap).forEach(colId => {
    const el = document.getElementById(colId);
    Sortable.create(el, {
      group: 'kanban',
      animation: 150,
      onEnd: evt => {
      const taskId = evt.item.dataset.id;
      const newStatus = statusMap[evt.to.id];
      // Appel AJAX pour mettre à jour le statut en base
      fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus }),
      })
        .then(res => {
        if (!res.ok) throw new Error('Erreur mise à jour statut');
        })
        .catch(console.error);
      },
    });
    });
  </script>

@endpush