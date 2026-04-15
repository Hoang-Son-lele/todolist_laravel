@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Projects</h1>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Create New Project
        </a>
        @endif
    </div>

    @if ($message = Session::get('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ $message }}
    </div>
    @endif

    @if ($projects->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($projects as $project)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $project->name }}</h2>
                    <span class="inline-block mt-2 px-3 py-1 bg-{{ $project->status === 'active' ? 'green' : ($project->status === 'completed' ? 'blue' : 'yellow') }}-100 text-{{ $project->status === 'active' ? 'green' : ($project->status === 'completed' ? 'blue' : 'yellow') }}-800 rounded-full text-sm font-semibold">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
            </div>

            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($project->description, 100) }}</p>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Start: {{ $project->start_date->format('M d, Y') }}</span>
                    <span>End: {{ $project->end_date->format('M d, Y') }}</span>
                </div>
                <div class="text-sm text-gray-600">
                    Tasks: {{ $project->tasks_count ?? $project->tasks()->count() }} | Progress: {{ $project->progress_percentage }}%
                </div>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $project->progress_percentage }}%"></div>
            </div>

            <div class="flex justify-between gap-2 mt-4">
                <a href="{{ route('projects.show', $project) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded text-center text-sm">
                    View
                </a>

                @if(Auth::user()->role === 'admin')
                <a href="{{ route('projects.report', $project) }}" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded text-center text-sm">
                    Timeline
                </a>

                <a href="{{ route('projects.edit', $project) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded text-center text-sm">
                    Edit
                </a>

                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded text-sm">
                        Delete
                    </button>
                </form>
                @endif
            </div>



        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $projects->links() }}
    </div>
    @else
    <div class="bg-gray-100 rounded-lg p-8 text-center">
        <p class="text-gray-600 text-lg mb-4">No projects yet. Create your first project!</p>
        <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
            Create Project
        </a>
    </div>
    @endif
</div>
@endsection