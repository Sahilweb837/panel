@extends('layouts.app')

@section('title', 'Create Workspace - Netcoder LMS')
@section('page-title', 'Create Workspace')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
        <div class="mb-6">
            <a href="{{ route('workspace.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-3">
                <i class="fas fa-arrow-left text-[10px]"></i> Back to Workspaces
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Create a New Slack-Style Workspace</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Workspaces give your cohorts, instructors, and departments dedicated channels and messaging.</p>
        </div>

        <form action="{{ route('workspace.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Workspace Name</label>
                <input type="text" name="name" required placeholder="e.g. AI & Full-Stack Developers Cohort" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                <p class="text-[11px] text-slate-400 mt-1">This will be the title displayed on your Slack sidebar.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Description (Optional)</label>
                <textarea name="description" rows="3" placeholder="What is this workspace used for?" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Visibility & Permissions</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition">
                        <input type="radio" name="visibility" value="public" checked class="mt-0.5 text-blue-600">
                        <div>
                            <div class="text-xs font-bold text-slate-900">Public Workspace</div>
                            <div class="text-[11px] text-slate-500">Any student, teacher or staff member in your LMS can join.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition">
                        <input type="radio" name="visibility" value="private" class="mt-0.5 text-blue-600">
                        <div>
                            <div class="text-xs font-bold text-slate-900">Private Workspace</div>
                            <div class="text-[11px] text-slate-500">Only invited members can view channels and participate.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Logo Image URL (Optional)</label>
                <input type="url" name="logo" placeholder="https://..." class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('workspace.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                    Create Workspace
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
