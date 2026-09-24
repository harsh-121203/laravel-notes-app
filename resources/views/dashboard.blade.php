@extends('layouts.app')

@section('title', 'Workspace • Tasks & Notes')

@section('content')
<div id="workspaceRoot">
    <!-- Top Calendar Navigation Strip -->
    @include('partials.calendar')

    <!-- Two-Column Architecture: Tasks (Left) & Notebooks (Right) -->
    <div class="columns-container">
        @include('partials.tasks.task-list')
        @include('partials.notes.note-list')
    </div>
</div>
@endsection
