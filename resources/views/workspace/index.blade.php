@extends('layouts.app')

@section('title', 'Workspaces - Netcoder LMS')
@section('page-title', 'LMS Workspaces')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Your Slack-Style Workspaces</h1>
            <p class="text-sm text-slate-500 mt-1">Collaborate with educators, students, and project groups through dedicated team hubs.</p>
        </div>
        <a href="{{ route('workspace.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm shadow-sm transition">
            <i class="fas fa-plus text-xs"></i>
            <span>Create New Workspace</span>
        </a>
    </div>

    <!-- User's Workspaces -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        @forelse($userWorkspaces as $ws)
            <div class="bg-white border border-slate-200 hover:border-blue-400 rounded-2xl p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3.5 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-900 to-purple-900 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            @if($ws->logo)
                                <img src="{{ $ws->logo }}" alt="{{ $ws->name }}" class="w-full h-full rounded-2xl object-cover" onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                            @else
                                {{ strtoupper(substr($ws->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-base text-slate-900 truncate">{{ $ws->name }}</h3>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500">
                                <i class="fas fa-users text-[10px]"></i> {{ $ws->members->count() }} members
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed">
                        {{ $ws->description ?: 'Collaborative workspace for students, teachers and staff.' }}
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold {{ $ws->visibility === 'public' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($ws->visibility) }}
                    </span>
                    <a href="{{ route('workspace.show', $ws->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-blue-600 text-white rounded-xl text-xs font-semibold transition">
                        <span>Launch</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200">
                <i class="fas fa-layer-group text-4xl text-slate-300 mb-3 block"></i>
                <h3 class="text-base font-bold text-slate-800">No workspaces yet</h3>
                <p class="text-xs text-slate-500 mt-1">Create your first Slack-style workspace to start collaborating.</p>
                <a href="{{ route('workspace.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold">
                    <i class="fas fa-plus"></i> Create Workspace
                </a>
            </div>
        @endforelse
    </div>

    <!-- Public Workspaces Directory -->
    @if(isset($allPublicWorkspaces) && $allPublicWorkspaces->isNotEmpty())
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-4">Discover Public Workspaces</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($allPublicWorkspaces as $pws)
                    <div class="p-4 bg-white border border-slate-200 rounded-xl flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-bold text-sm text-slate-900 truncate">{{ $pws->name }}</div>
                            <div class="text-[11px] text-slate-500 truncate">{{ $pws->members->count() }} members</div>
                        </div>
                        <a href="{{ route('workspace.show', $pws->slug) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-semibold shrink-0">
                            Join / View
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
