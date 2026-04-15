@extends('layouts.app')

@section('content')
@php

$startDate = $project->start_date->copy()->startOfMonth();


$chartEndDate = $startDate->copy()->addMonths(2)->endOfMonth();


$totalDaysInChart = $startDate->diffInDays($chartEndDate);


$getLeft = function($date) use ($startDate, $totalDaysInChart) {
$daysFromStart = $startDate->diffInDays($date, false);
return ($daysFromStart / $totalDaysInChart) * 100;
};


$getWidth = function($start, $end) use ($totalDaysInChart) {
$duration = $start->diffInDays($end, false);

return max(8, ($duration / $totalDaysInChart) * 100);
};
@endphp

<div class="container mx-auto py-8 px-4">
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-100">
        <div class="p-6 border-b flex justify-between items-center bg-white">
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">PROJECT TIMELINE REPORT</h2>
            <span class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-blue-100">
                Admin View
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[1100px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-widest">
                        <th class="p-4 border-b border-r w-72 text-left font-semibold">Projects + Tasks</th>
                        <th class="p-4 border-b border-r w-28 text-center font-semibold text-blue-600">% Comp</th>
                        <th class="p-4 border-b text-center w-1/4 font-bold text-slate-700">Tháng {{ $startDate->format('m') }}</th>
                        <th class="p-4 border-b text-center w-1/4 font-bold text-slate-700">Tháng {{ $startDate->copy()->addMonth()->format('m') }}</th>
                        <th class="p-4 border-b text-center w-1/4 font-bold text-slate-700">Tháng {{ $startDate->copy()->addMonths(2)->format('m') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 border-r font-bold text-blue-700 text-base">{{ strtoupper($project->name) }}</td>
                        <td class="p-4 border-r text-center font-black text-lg text-slate-800">{{ $project->progress_percentage }}%</td>
                        <td colspan="3" class="p-0 relative h-20 bg-white timeline-grid">
                            <div class="absolute bg-green-500 text-white text-[11px] font-bold p-2.5 rounded-lg shadow-md flex items-center overflow-hidden whitespace-nowrap z-10 transition-all hover:brightness-105"
                                style="left: {{ $getLeft($project->start_date) }}%; 
                                        width: {{ $getWidth($project->start_date, $project->end_date) }}%; 
                                        top: 15px; height: 40px;">
                                <span class="drop-shadow-sm">{{ strtoupper($project->name) }} | {{ $project->start_date->format('d/m') }} - {{ $project->end_date->format('d/m') }}</span>
                            </div>
                        </td>
                    </tr>

                    @foreach($tasks as $index => $task)
                    @php
                    $colors = ['bg-amber-400', 'bg-pink-500', 'bg-indigo-500', 'bg-sky-400', 'bg-emerald-400'];
                    $bgColor = $colors[$task->assigned_to % count($colors)] ?? 'bg-slate-400';
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-4 border-r pl-10 text-sm text-slate-600 font-medium">
                            <div class="flex items-center">
                                <span class="w-2.5 h-2.5 rounded-full {{ $bgColor }} mr-3 shadow-sm"></span>
                                {{ $task->title }}
                            </div>
                        </td>
                        <td class="p-4 border-r text-center text-sm text-slate-400 font-semibold">{{ $task->completion_percentage }}%</td>
                        <td colspan="3" class="p-0 relative h-14 timeline-grid">
                            <div class="absolute {{ $bgColor }} text-white text-[10px] font-semibold px-3 rounded-md shadow flex items-center overflow-hidden whitespace-nowrap transition-all hover:scale-[1.02] cursor-default"
                                style="left: {{ $getLeft($task->start_date) }}%; 
                                        width: {{ $getWidth($task->start_date, $task->end_date) }}%; 
                                        top: 10px; height: 28px;">
                                {{ $task->assignedTo->name ?? 'User' }} - {{ $task->end_date->format('d/m') }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5 bg-slate-50 border-t flex justify-between items-center text-[11px] text-slate-400">
            <span>* Báo cáo tiến độ tự động dựa trên {{ $tasks->count() }} công việc thành phần.</span>
            <span class="font-medium text-slate-500">CARBOOK Management System v2.0</span>
        </div>
    </div>
</div>

<style>
    /* Đường kẻ mờ phân chia các tháng */
    .timeline-grid {
        background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px);
        background-size: 33.33333% 100%;
    }

    /* Hiệu ứng bo góc và đổ bóng nhẹ cho thanh Gantt */
    .absolute {
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
</style>
@endsection