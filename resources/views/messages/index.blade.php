@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="glass-panel p-5 rounded-2xl border border-white/80 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-950 tracking-tight">
                Messages
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Direct conversations regarding job applications and details
            </p>
        </div>
    </div>

    <!-- Chat Interface Container (Glassmorphism Two-Column) -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-[640px]">
        
        <!-- Left: Contacts Sidebar (4 cols) -->
        <div class="md:col-span-4 glass-panel p-4 rounded-3xl border border-white/80 shadow-xl flex flex-col h-full overflow-hidden">
            <div class="pb-3 mb-2 border-b border-sky-100 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-950">Active Conversations</h3>
                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                    {{ $contacts->count() }}
                </span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                @if($contacts->isEmpty())
                    <div class="text-center py-10 text-xs text-slate-600 font-semibold">
                        <p>No active chat threads yet.</p>
                        <p class="mt-1 text-[11px] text-slate-500">Visit a job posting or applicant card to start messaging.</p>
                    </div>
                @else
                    @foreach($contacts as $contact)
                        <a href="{{ route('messages.index', ['user_id' => $contact->id]) }}"
                            class="p-3 rounded-2xl block transition-all {{ $activeContact && $activeContact->id === $contact->id ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'glass-card hover:bg-white text-slate-900' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $activeContact && $activeContact->id === $contact->id ? 'bg-white text-sky-600' : 'bg-sky-100 text-sky-800' }} font-black flex items-center justify-center text-xs flex-shrink-0">
                                    {{ strtoupper(substr($contact->display_name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-black truncate {{ $activeContact && $activeContact->id === $contact->id ? 'text-white' : 'text-slate-950' }}">
                                            {{ $contact->display_name }}
                                        </p>
                                    </div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider {{ $activeContact && $activeContact->id === $contact->id ? 'text-sky-100' : 'text-sky-700' }}">
                                        {{ $contact->role }} • {{ $contact->address ? explode(',', $contact->address)[0] : 'Philippines' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right: Message Thread & Composer (8 cols) -->
        <div class="md:col-span-8 glass-panel rounded-3xl border border-white/80 shadow-xl flex flex-col h-full overflow-hidden">
            
            @if($activeContact)
                <!-- Chat Top Bar -->
                <div class="p-4 bg-white/70 backdrop-blur-md border-b border-sky-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-sky-400 text-white font-black flex items-center justify-center text-sm shadow-sm">
                            {{ strtoupper(substr($activeContact->display_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-950">
                                {{ $activeContact->display_name }}
                            </h3>
                            <p class="text-[11px] font-semibold text-slate-600">
                                <span class="capitalize font-bold text-sky-800">{{ $activeContact->role }}</span> • {{ $activeContact->contact_number ?? $activeContact->email }}
                            </p>
                        </div>
                    </div>

                    @if($relatedJob)
                        <div class="hidden sm:block text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Regarding Post:</span>
                            <a href="{{ route('jobs.show', $relatedJob) }}" class="text-xs font-bold text-sky-700 hover:underline max-w-[180px] truncate block">
                                {{ $relatedJob->title }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Chat History Messages Scroll Area -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" id="chatMessageContainer">
                    @if($messages->isEmpty())
                        <div class="text-center py-16 text-slate-600 text-xs font-semibold">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </div>
                            <p class="font-bold text-slate-800">Start the conversation</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Send a greeting, ask about requirements, or clarify work details.</p>
                        </div>
                    @else
                        @foreach($messages as $msg)
                            @php $isMe = ($msg->sender_id === auth()->id()); @endphp
                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                <div class="max-w-[80%] rounded-2xl px-4 py-3 text-xs leading-relaxed {{ $isMe ? 'bg-sky-500 text-white rounded-tr-none shadow-md shadow-sky-500/15' : 'bg-white/90 text-slate-900 border border-slate-200/80 rounded-tl-none shadow-sm' }}">
                                    <p class="font-semibold whitespace-pre-line">{{ $msg->message }}</p>
                                </div>
                                <span class="text-[10px] text-slate-600 font-bold mt-1 px-1">
                                    {{ $msg->created_at->format('h:i A') }} • {{ $msg->created_at->diffForHumans() }}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Message Composer -->
                <div class="p-3 bg-white/80 backdrop-blur-md border-t border-sky-100">
                    <form method="POST" action="{{ route('messages.store') }}" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $activeContact->id }}">
                        @if($relatedJob)
                            <input type="hidden" name="job_post_id" value="{{ $relatedJob->id }}">
                        @endif

                        <input type="text" name="message" required autofocus
                            placeholder="Type a message to {{ $activeContact->first_name ?? $activeContact->display_name }}..."
                            class="flex-1 px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs shadow-md shadow-sky-500/20 flex items-center gap-1.5 transition-all hover:scale-[1.02]">
                            <span>Send</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </form>
                </div>
            @else
                <!-- No Contact Selected -->
                <div class="flex-1 flex items-center justify-center p-8 text-center text-slate-700">
                    <div>
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <h3 class="text-base font-black text-slate-950">Select a conversation</h3>
                        <p class="text-xs font-semibold text-slate-600 mt-1">Choose a contact from the left list to view chat history and exchange messages.</p>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chatMessageContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endpush
@endsection
