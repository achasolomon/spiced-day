@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Stay updated on your application progress
                </p>
            </div>
            
            <div class="flex gap-3">
                @if($notifications->where('is_read', false)->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                            Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <a href="{{ route('notifications.index') }}" 
               class="px-6 py-3 text-sm font-medium {{ !request('unread_only') ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                All Notifications
            </a>
            <a href="{{ route('notifications.index', ['unread_only' => 1]) }}" 
               class="px-6 py-3 text-sm font-medium {{ request('unread_only') ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                Unread 
                @if($notifications->where('is_read', false)->count() > 0)
                    <span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-200 rounded-full text-xs">
                        {{ $notifications->where('is_read', false)->count() }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Notifications List --}}
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                            {{ $notification->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                            {{ $notification->priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30' : '' }}
                            {{ $notification->priority === 'normal' ? 'bg-purple-100 dark:bg-purple-900/30' : '' }}
                            {{ $notification->priority === 'low' ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            @if(str_contains($notification->type, 'document'))
                                <svg class="w-5 h-5 {{ $notification->priority === 'urgent' ? 'text-red-600' : 'text-purple-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(str_contains($notification->type, 'appointment'))
                                <svg class="w-5 h-5 {{ $notification->priority === 'urgent' ? 'text-red-600' : 'text-purple-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(str_contains($notification->type, 'application'))
                                <svg class="w-5 h-5 {{ $notification->priority === 'urgent' ? 'text-red-600' : 'text-purple-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 {{ $notification->priority === 'urgent' ? 'text-red-600' : 'text-purple-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                </svg>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $notification->title }}
                                </h3>
                                @if(!$notification->is_read)
                                    <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $notification->message }}
                            </p>

                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                @if($notification->priority !== 'normal')
                                    <span class="px-2 py-0.5 rounded-full
                                        {{ $notification->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                        {{ $notification->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                        {{ $notification->priority === 'low' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                        {{ ucfirst($notification->priority) }} Priority
                                    </span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            @if($notification->action_url)
                                <div class="mt-3">
                                    <a href="{{ $notification->action_url }}" 
                                       class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-700">
                                        {{ $notification->action_text ?? 'View Details' }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-2">
                            @if(!$notification->is_read)
                                <button onclick="markAsRead({{ $notification->id }})"
                                        class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                        title="Mark as read">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            @endif
                            
                            <form action="{{ route('notifications.destroy', $notification) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Delete this notification?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                        title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Notifications</h3>
                    <p class="text-gray-600 dark:text-gray-400">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection