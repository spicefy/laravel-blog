{{-- resources/views/admin/tags/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Edit Tag')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
      <h2 class="text-sm font-bold text-slate-700">Edit Tag</h2>
      <a href="{{ route('dashboard.tags.index') }}" class="text-slate-400 hover:text-slate-600 text-sm transition-colors">
        <i class="fas fa-times"></i>
      </a>
    </div>

    @if(session('success'))
    <div class="mx-5 mt-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-start gap-2">
      <i class="fas fa-check-circle mt-0.5 shrink-0"></i>
      {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('dashboard.tags.update', $tag) }}" method="POST" class="p-5 space-y-5">
      @csrf
      @method('PUT')

      {{-- Name --}}
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
          Tag Name <span class="text-red-400">*</span>
        </label>
        <input
          type="text"
          name="name"
          value="{{ old('name', $tag->name) }}"
          required
          autofocus
          placeholder="e.g. KCSE, Scholarships, EdTech"
          class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition @error('name') border-red-400 bg-red-50 @enderror"
        />
        @error('name')
          <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Slug (read-only, updated by model hook on save) --}}
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
          Slug <span class="text-slate-400 font-normal">(auto-generated)</span>
        </label>
        <input
          type="text"
          value="{{ $tag->slug }}"
          disabled
          readonly
          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-400 font-mono cursor-not-allowed"
        />
        <p class="text-xs text-slate-400 mt-1.5">
          The slug updates automatically when you change the name.
        </p>
      </div>

      {{-- Actions --}}
      <div class="flex gap-3 pt-2">
        <button
          type="submit"
          class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2"
        >
          <i class="fas fa-save text-xs"></i> Update Tag
        </button>
        <a
          href="{{ route('dashboard.tags.index') }}"
          class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl transition-colors text-center"
        >
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
