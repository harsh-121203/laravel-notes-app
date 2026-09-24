@extends('layouts.app')

@section('title', 'Workspace • Tasks & Notes')

@section('content')
<div id="workspaceRoot">
    <!-- Modern Three-Column Architecture -->
    <div class="three-column-layout">
        <!-- Left Sidebar: Calendar / Timeline -->
        <aside class="left-sidebar">
            @include('partials.calendar')
        </aside>

        <!-- Center Column: Tasks -->
        <section class="center-tasks">
            @include('partials.tasks.task-list')
        </section>

        <!-- Right Column: Notebook -->
        <section class="right-notes">
            @include('partials.notes.note-list')
        </section>
    </div>
</div>
@endsection
