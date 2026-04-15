@extends('layouts.app')

@section('content')
@php
// --- LOGIC TÍNH TOÁN NGÀY THÁNG ---
// 1. Xác định mốc thời gian hiển thị (3 tháng bắt đầu từ tháng của Project)
$startDate = $project->start_date->copy()->startOfMonth();
$endDate = $startDate->copy()->addMonths(3)->endOfMonth();
$totalDays = $startDate->diffInDays($endDate);

// 2. Hàm tính % vị trí (Left)
$getLeft = function($date) use ($startDate, $totalDays) {
$diff = $startDate->diffInDays($date, false);
return ($diff / $totalDays) * 100;
};

// 3. Hàm tính % độ rộng (Width)
$getWidth = function($start, $end) use ($totalDays) {
$diff = $start->diffInDays($end, false);

// Nếu diff = 0 (làm trong 1 ngày) thì ép nó rộng ít nhất 8% để hiện được chữ
// Nếu không thì cứ tính theo tỉ lệ nhưng tối thiểu là 8%
$w = ($diff / $totalDays) * 100;
return max(8, $w);
};
@endphp

<div class="container mx-auto py-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">PROJECT TIMELINE REPORT</h2>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">Admin View</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-xs uppercase">
                        <th class="p-4 border w-64 text-left">Projects + Tasks</th>
                        <th class="p-4 border w-24 text-center">% COMP</th>
                        <th class="p-4 border text-center w-1/4">Tháng {{ $startDate->format('m') }}</th>
                        <th class="p-4 border text-center w-1/4">Tháng {{ $startDate->copy()->addMonth()->format('m') }}</th>
                        <th class="p-4 border text-center w-1/4">Tháng {{ $startDate->copy()->addMonths(2)->format('m') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-gray-50 font-bold">
                        <td class="p-4 border text-blue-700">{{ strtoupper($project->name) }}</td>
                        <td class="p-4 border text-center">{{ $project->progress_percentage }}%</td>
                        <td colspan="3" class="p-0 border relative h-16 bg-white timeline-grid">
                            <div class="absolute bg-green-500 text-white text-[10px] p-2 rounded shadow-sm flex items-center overflow-hidden whitespace-nowrap"
                                style="left: {{ $getLeft($project->start_date) }}%; 
                                        width: {{ $getWidth($project->start_date, $project->end_date) }}%; 
                                        top: 12px; height: 35px;">
                                {{ strtoupper($project->name) }} | {{ $project->start_date->format('d/m') }} - {{ $project->end_date->format('d/m') }}
                            </div>
                        </td>
                    </tr>

                    @foreach($tasks as $index => $task)
                    @php
                    $colors = ['bg-blue-400', 'bg-purple-400', 'bg-orange-400', 'bg-pink-400', 'bg-indigo-400'];
                    $bgColor = $colors[$task->assigned_to % count($colors)] ?? 'bg-gray-400';
                    @endphp
                    <tr>
                        <td class="p-4 border pl-10 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full {{ $bgColor }} mr-2"></span>
                                {{ $task->title }}
                            </div>
                        </td>
                        <td class="p-4 border text-center text-sm text-gray-500">{{ $task->completion_percentage }}%</td>
                        <td colspan="3" class="p-0 border relative h-12 timeline-grid">
                            <div class="absolute {{ $bgColor }} text-white text-[9px] px-2 rounded opacity-90 flex items-center shadow-sm overflow-hidden whitespace-nowrap"
                                style="left: {{ $getLeft($task->start_date) }}%; 
                                        width: {{ $getWidth($task->start_date, $task->end_date) }}%; 
                                        top: 8px; height: 24px;">
                                {{ $task->assignedTo->name ?? 'User' }} - {{ $task->end_date->format('d/m') }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .timeline-grid {
        background-image: linear-gradient(to right, #f3f4f6 1px, transparent 1px);
        background-size: 33.333% 100%;
    }
</style>
@endsection