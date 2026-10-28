<!-- Global Flash Messages -->
@if(session('success'))
    <div class="mb-5 p-4 rounded-2xl glass-card border border-emerald-500/30 bg-emerald-950/60 text-emerald-200 flex items-center gap-3 shadow-lg">
        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        <div class="flex-1 text-xs font-bold">
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-5 p-4 rounded-2xl glass-card border border-red-500/30 bg-red-950/60 text-red-200 flex items-center gap-3 shadow-lg">
        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        <div class="flex-1 text-xs font-bold">
            {{ session('error') }}
        </div>
    </div>
@endif

@if(session('info'))
    <div class="mb-5 p-4 rounded-2xl glass-card border border-zinc-700 bg-zinc-900/80 text-zinc-200 flex items-center gap-3 shadow-lg">
        <svg class="w-5 h-5 text-zinc-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <div class="flex-1 text-xs font-bold">
            {{ session('info') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="mb-5 p-4 rounded-2xl glass-card border border-red-500/30 bg-red-950/60 text-red-200 shadow-lg">
        <p class="text-xs font-bold mb-1 flex items-center gap-1.5 text-red-300">
            Please review the following errors:
        </p>
        <ul class="list-disc pl-5 text-xs font-semibold space-y-0.5 text-red-200">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

