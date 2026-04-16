@extends('layouts.admin')

@section('title', 'Certificate Details')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.certificates.adminIndex') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    ← Back to Certificates
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Certificate Details</h1>
                <p class="mt-1 text-sm text-gray-600">Certificate #{{ $certificate->certificate_number }}</p>
            </div>
            <div>
                @if($certificate->status === 'active')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                    </span>
                @elseif($certificate->status === 'expired')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                        Expired
                    </span>
                @elseif($certificate->status === 'revoked')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                        Revoked
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Certificate Information -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Certificate Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Certificate Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $certificate->certificate_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Applicant Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->applicant_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->issue_date->format('F d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $certificate->expiry_date->format('F d, Y') }}
                                @if($certificate->status === 'active' && $certificate->expiry_date->isPast())
                                    <span class="ml-2 text-xs text-red-600 font-semibold">(Expired)</span>
                                @elseif($certificate->status === 'active' && $certificate->expiry_date->diffInDays(now()) <= 30)
                                    <span class="ml-2 text-xs text-yellow-600 font-semibold">(Expires in {{ $certificate->expiry_date->diffInDays(now()) }} days)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Issued By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->issuedBy->name ?? 'System' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">CEO Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->ceo_name }}</dd>
                        </div>
                    </dl>

                    @if($certificate->status === 'revoked')
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h3 class="text-sm font-semibold text-red-900 mb-2">Revocation Details</h3>
                        <p class="text-sm text-red-700"><strong>Date:</strong> {{ $certificate->revoked_at->format('F d, Y h:i A') }}</p>
                        <p class="text-sm text-red-700 mt-1"><strong>Reason:</strong> {{ $certificate->revocation_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Application Details -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Related Application</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tracking Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('admin.applications.show', $certificate->application) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ $certificate->application->tracking_number }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Applicant Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->application->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $certificate->application->status }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $certificate->application->approved_at ? $certificate->application->approved_at->format('F d, Y') : 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Certificate Preview -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Certificate Preview</h2>
                  <a href="{{ route('admin.certificates.preview', $certificate) }}"  
                  target="_blank"
                   class="inline-block px-3 py-2 text-sm text-blue-600 hover:text-blue-800 font-medium border rounded">
                    View Full Size
                </a>

                </div>
                <div class="p-6">
                    <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                        <iframe src="{{ route('admin.certificates.preview', $certificate) }}" 
                                class="w-full h-96"
                                frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <!-- Download PDF -->
                    <a href="{{ route('admin.certificates.download', $certificate) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </a>

                    <!-- Preview in New Tab -->
                    <a href="{{ route('admin.certificates.preview', $certificate) }}" 
                       target="_blank"
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview Full Page
                    </a>

                    <!-- Regenerate PDF -->
                    <form action="{{ route('admin.certificates.regenerate', $certificate) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to regenerate this certificate PDF?')">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Regenerate PDF
                        </button>
                    </form>

                    <!-- View Application -->
                    <a href="{{ route('admin.applications.show', $certificate->application) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Application
                    </a>

                    <div class="border-t border-gray-200 my-4"></div>

                    <!-- Revoke Certificate -->
                    @if($certificate->status === 'active')
                    <button onclick="openRevokeModal()" 
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Revoke Certificate
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revoke Modal -->
<div id="revokeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Revoke Certificate</h3>
                <button onclick="closeRevokeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.certificates.revoke', $certificate) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Revocation <span class="text-red-500">*</span>
                    </label>
                    <textarea name="revocation_reason" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Enter the reason for revoking this certificate..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeRevokeModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        Revoke Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 w-11/12 max-w-6xl">
        <div class="bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Certificate Preview</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <iframe src="{{ route('admin.certificates.preview', $certificate) }}" 
                        class="w-full h-[80vh] border-0"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openRevokeModal() {
    document.getElementById('revokeModal').classList.remove('hidden');
}

function closeRevokeModal() {
    document.getElementById('revokeModal').classList.add('hidden');
}

function openPreviewModal() {
    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRevokeModal();
        closePreviewModal();
    }
});
</script>
@endpush