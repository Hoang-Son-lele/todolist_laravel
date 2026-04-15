@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold">{{ $project->name }}</h1>
                <span class="inline-block mt-2 px-3 py-1 bg-{{ $project->status === 'active' ? 'green' : ($project->status === 'completed' ? 'blue' : 'yellow') }}-100 text-{{ $project->status === 'active' ? 'green' : ($project->status === 'completed' ? 'blue' : 'yellow') }}-800 rounded-full text-sm font-semibold">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
            <div class="flex gap-2">
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('projects.edit', $project) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Delete
                    </button>
                </form>
                @endif
                <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-gray-600 text-sm">Start Date</p>
                    <p class="text-lg font-semibold">{{ $project->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">End Date</p>
                    <p class="text-lg font-semibold">{{ $project->end_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Days Remaining</p>
                    <p class="text-lg font-semibold">{{ $project->days_remaining }} days</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Progress</p>
                    <p class="text-lg font-semibold">{{ $project->progress_percentage }}%</p>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-gray-600 text-sm mb-2">Overall Progress</p>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full transition" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
            </div>

            @if ($project->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600 text-sm mb-2">Description</p>
                <p class="text-gray-800">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Project Tasks</h2>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                Add Task
            </a>
            @endif
        </div>

        @if ($tasks->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Title</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Difficulty</th>
                        <th class="px-6 py-3 text-left font-semibold">Assigned To</th>
                        <th class="px-6 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="#" class="text-blue-500 hover:underline">{{ $task->title }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }} rounded-full text-sm">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 {{ $task->difficulty === 'hard' ? 'bg-red-100 text-red-800' : ($task->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }} rounded-full text-sm">
                                {{ ucfirst($task->difficulty) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $task->assignedTo?->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
        @else
        <p class="text-gray-600 text-center py-8">No tasks in this project yet. <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="text-blue-500 hover:underline">Create one now</a></p>
        @endif
    </div>
</div>
@endsection