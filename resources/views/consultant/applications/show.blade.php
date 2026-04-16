@extends('layouts.consultant')

@section('title', 'Application Details - ' . $application->full_name)

@section('content')
<div class="space-y-6">
     <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
   <div class="flex items-center gap-4">
    <a href="{{ route('consultant.applications.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $application->full_name }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Application #{{ $application->application_number }}</p>
    </div>
</div>


    <div class="flex items-center gap-3">
        <!-- Schedule Appointment Button (hide for active, compliance_inspection_scheduled)
             Also disable if required documents have not been uploaded and approved -->
             
        @php
                // Stages where document approval is NOT required for scheduling
                $documentFreeStages = ['meet_and_greet', 'initial_inspection'];
                
                // Stages where documents MUST be approved before scheduling
                $documentRequiredStages = ['second_inspection', 'final_inspection', 'contract_signing', 'compliance_inspection'];
                
                $currentStage = $application->current_stage;
                $requiresDocuments = in_array($currentStage, $documentRequiredStages);
                
                // Only check document approval if we're in a stage that requires it
                $allApproved = true;
                if ($requiresDocuments) {
                    $requiredDocs = $documentRequirements ?? collect();
                    if ($requiredDocs->count() > 0) {
                        foreach ($requiredDocs as $req) {
                            $exists = $application->documents()
                                        ->where('document_requirement_id', $req->id)
                                        ->where('status', 'approved')
                                        ->exists();
                            if (!$exists) {
                                $allApproved = false;
                                break;
                            }
                        }
                    }
                }
                
                // Don't allow scheduling if status is active or compliance_inspection_scheduled
                $statusAllows = !in_array($application->status, ['active', 'compliance_inspection_scheduled', 'initial_inspection_completed', 'rejected']);
                
                  $allowedProfileStatuses = [
                    'active',
                    'compliance_inspection_due',
                    'compliance_inspection_scheduled',
                ];
                
                // Check current stage completion
                $stageRecord = $application->stages()
                    ->where('stage_name', $application->current_stage)
                    ->latest('created_at')
                    ->first();
                $stageCompleted = $stageRecord ? ($stageRecord->status === 'completed') : true;
                
                // Prevent scheduling if there's already a pending/confirmed appointment for the same type/stage
                $hasPendingAppointment = $application->appointments()
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->where('type', $application->current_stage)
                    ->where('scheduled_at', '>', now())
                    ->exists();
                
                // Final decision: can schedule if status allows, stage is completed, no pending appointment,
                // AND (we're in a document-free stage OR all required docs are approved)
                $canSchedule = $statusAllows 
                    && $stageCompleted 
                    && !$hasPendingAppointment
                    && (!$requiresDocuments || $allApproved);
                    
                // Determine the disable message
                $disableMessage = '';
                if (!$statusAllows) {
                    $disableMessage = 'Cannot schedule - application status does not allow scheduling';
                } elseif (!$stageCompleted) {
                    $disableMessage = 'Current stage must be completed first';
                } elseif ($hasPendingAppointment) {
                    $disableMessage = 'There is already a pending appointment for this stage';
                } elseif ($requiresDocuments && !$allApproved) {
                    $disableMessage = 'Required documents must be uploaded and approved before scheduling';
                }
            @endphp

           @if($canSchedule)
    <button @click="window.dispatchEvent(new CustomEvent('open-appointment-modal', {
        detail: {
            applicationId: {{ $application->id }},
            applicantId: {{ $application->user_id ?? 'null' }},
            applicantAddress: {{ json_encode($application->full_address) }}
        }
    }));" 
    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
    Schedule Appointment
</button>
@else
    <button disabled
        title="{{ $disableMessage }}"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium opacity-50 cursor-not-allowed">
        Schedule Appointment
    </button>
@endif

        
            <!-- Mark Compliance Due Button (only show when status is active) -->
            @if($application->status === 'active')
                <button @click="$dispatch('open-compliance-due-modal')" 
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Mark Compliance Due
                </button>
            @endif
    
        <!-- Action Buttons Dropdown -->
        <div x-data="{ showActions: false }" class="relative">
            @if($application->status != 'rejected')
                <button @click="showActions = !showActions" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                    Actions
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @endif

            <div x-show="showActions" 
                @click.away="showActions = false"
                x-cloak
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-10">
                
                <!-- Approve Button (conditionally visible) -->
                @if(in_array($application->status, ['submitted', 'under_review', 'final_inspection_completed']))
                    <button @click="showActions = false; $dispatch('open-approve-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-600 dark:text-green-400 flex items-center gap-2">
                        Approve Application
                    </button>
                @endif
    
                <!-- Reject Button (hide for active, compliance_inspection_scheduled, compliance_inspection_completed) -->
                @if(!in_array($application->status, ['active', 'compliance_inspection_scheduled', 'compliance_inspection_completed', 'rejected']))
                    <button @click="showActions = false; $dispatch('open-reject-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400 flex items-center gap-2">
                        Reject Application
                    </button>
                @endif
    
                <!-- Activate Button (hide for active, compliance_inspection_scheduled, compliance_inspection_completed) -->
                @if(!in_array($application->status, ['active', 'compliance_inspection_scheduled', 'compliance_inspection_completed', 'rejecte']))
                    <button @click="showActions = false; $dispatch('open-activate-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        Activate Application
                    </button>
                @endif
    
                <!-- Divider (show when compliance actions are available) -->
                @if($application->status === 'compliance_inspection_completed' || $application->status === 'suspended')
                    <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                @endif
    
                <!-- Suspend Button (only show for compliance_inspection_completed) -->
                @if($application->status === 'compliance_inspection_completed')
                    <button @click="showActions = false; $dispatch('open-suspend-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-yellow-600 dark:text-yellow-400 flex items-center gap-2">
                        Suspend Dayhome
                    </button>
                @endif
    
                <!-- Terminate Button (only show for compliance_inspection_completed) -->
                @if($application->status === 'compliance_inspection_completed')
                    <button @click="showActions = false; $dispatch('open-terminate-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400 flex items-center gap-2">
                        Terminate Dayhome
                    </button>
                @endif
    
                <!-- Reinstate Button (show for suspended or compliance_inspection_completed) -->
                @if(in_array($application->status, ['suspended', 'compliance_inspection_completed']))
                    <button @click="showActions = false; $dispatch('open-reinstate-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-600 dark:text-green-400 flex items-center gap-2">
                        Reinstate Dayhome
                    </button>
                @endif
    
                <!-- Require Remediation Button (show for compliance_inspection_completed) -->
                @if($application->status === 'compliance_inspection_completed')
                    <button @click="showActions = false; $dispatch('open-remediation-modal')" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-orange-600 dark:text-orange-400 flex items-center gap-2">
                        Require Remediation
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
  <div class="bg-gradient-to-r from-white-500 to-white-600 rounded-2xl p-6 text-white shadow-lg">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-purple-600 text-sm font-medium">Current Status</p>
            <p class="text-2xl text-purple-600 font-bold mt-1">{{ $application->status_display }}</p>
            <p class="text-purple-600 text-sm mt-2">{{ $application->current_stage_display }}</p>
        </div>
        <div class="w-full md:w-64">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-green-700">Progress</span>
                <!-- CHANGED: Use application_progress_percentage instead of completion_percentage -->
                <span class="text-sm text-green-700 font-semibold">{{ number_format($application->application_progress_percentage, 0) }}%</span>
            </div>
            <div class="w-full bg-purple-400/30 rounded-full h-3">
                <div class="bg-green-700 h-3 rounded-full transition-all" style="width: {{ $application->application_progress_percentage }}%"></div>
            </div>
        </div>
    </div>
</div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 text-sm text-green-800 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 text-sm text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Applicant Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->full_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->phone }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->full_address }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Childcare Level</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->childcare_level ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Desired Start Date</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->desired_start_date?->format('M j, Y') ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Home Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Home Type</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1 capitalize">{{ $application->home_type ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ownership</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1 capitalize">{{ $application->home_ownership ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Residents</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->home_residents_count ?? 0 }} people</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Fenced Backyard</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->fenced_backyard ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pets</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $application->has_pets ? ($application->pets_details ?? 'Yes') : 'No pets' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($application->status != 'rejected')

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Required Documents</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('consultant.applications.set-required-documents', $application) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($documentRequirements as $req)
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" 
                                           name="required_documents[]" 
                                           value="{{ $req->id }}"
                                           id="doc-{{ $req->id }}"
                                           {{ $application->documentRequirements->contains($req->id) ? 'checked' : '' }}
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <label for="doc-{{ $req->id }}" class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $req->name }}
                                        @if($req->help_text)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $req->help_text }})</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" 
                                class="mt-4 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors text-sm">
                            Save Required Documents
                        </button>
                    </form>
                </div>
                
            </div>
            @endif

            @if($application->status != 'rejected')

@if($application->imported_by_consultant || $application->workflow_concluded)
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Upload Documents (Legacy)</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upload documents on behalf of applicant</p>
        </div>
        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs font-semibold">
            Auto-Approved
        </span>
    </div>
    
    <div class="p-6">
        <form action="{{ route('consultant.documents.consultant-store', $application) }}" 
              method="POST" 
              enctype="multipart/form-data"
              x-data="consultantDocumentUpload()">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Documents to Upload
                </label>
                <input type="file" 
                       name="files[]" 
                       multiple 
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       @change="handleFileSelect($event)"
                       class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Max 50 files, 10MB each. Accepted: PDF, DOC, DOCX, JPG, PNG
                </p>
            </div>

            <!-- File Preview & Metadata -->
            <template x-if="files.length > 0">
                <div class="space-y-3 mb-4">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm" x-text="file.name"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatFileSize(file.size)"></p>
                                </div>
                                <button type="button" 
                                        @click="removeFile(index)"
                                        class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Document Metadata -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Document Name *
                                    </label>
                                    <input type="text" 
                                           :name="'documents[' + index + '][name]'"
                                           :value="file.name.replace(/\.[^/.]+$/, '')"
                                           required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Document Type *
                                    </label>
                                    <select :name="'documents[' + index + '][category]'"
                                            required
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                        <option value="">Select type...</option>
                                        @foreach($documentRequirements as $req)
                                            <option value="{{ $req->id }}">{{ $req->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Description (Optional)
                                    </label>
                                    <textarea :name="'documents[' + index + '][description]'"
                                              rows="2"
                                              class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Issue Date
                                    </label>
                                    <input type="date" 
                                           :name="'documents[' + index + '][issue_date]'"
                                           max="{{ date('Y-m-d') }}"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Expiry Date
                                    </label>
                                    <input type="date" 
                                           :name="'documents[' + index + '][expiry_date]'"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        :disabled="files.length === 0"
                        :class="files.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors text-sm">
                    <span x-show="files.length === 0">No Files Selected</span>
                    <span x-show="files.length > 0" x-text="'Upload ' + files.length + ' Document(s)'"></span>
                </button>
                <button type="button" 
                        @click="files = []"
                        x-show="files.length > 0"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors text-sm">
                    Clear All
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endif
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Documents</h2>
        <a href="{{ route('consultant.documents.pending-review') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">View Pending →</a>
    </div>
    <div class="p-6">
        <div class="space-y-3">
            @forelse($application->documents()->latest()->take(5)->get() as $document)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 max-w-[200px]">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm truncate" title="{{ $document->name }}">{{ $document->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                            {{ $document->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                            {{ $document->status === 'uploaded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                            {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                            {{ ucfirst($document->status) }}
                        </span>
                        
                        @if($document->file_path && \Storage::exists($document->file_path))
                            <div class="flex items-center gap-1">
                                <a href="{{ route('consultant.documents.preview', $document) }}" 
                                   target="_blank"
                                   class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors group"
                                   title="Preview document">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('consultant.documents.download', $document) }}" 
                                   class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors group"
                                   title="Download document">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No documents uploaded yet</p>
            @endforelse
        </div>
        
        @if($application->documents()->count() > 5)
            <div class="mt-4 text-center">
                 <a href="/consultant/documents/pending-review" 
                   class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 font-medium">
                    View All {{ $application->documents()->count() }} Documents →
                </a>
            </div>
        @endif
    </div>
</div>
        </div>

        <div class="space-y-6">
            @if($application->status != 'rejected')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                     @if(!in_array($application->status, ['documents_pending', 'documents_submitted', 'documents_approved', 'contract_signed', 'approved', 'rejected', 'cancelled']))
                   <button @click="window.dispatchEvent(new CustomEvent('open-appointment-modal', {
                        detail: {
                            applicationId: {{ $application->id }},
                            applicantId: {{ $application->user_id ?? 'null' }},
                            applicantAddress: {{ json_encode($application->full_address) }}
                        }
                    }));" 
                    class="w-full px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    Schedule Appointment
                </button>
                @endif
                    @if(!in_array($application->status, ['documents_pending', 'documents_submitted', 'documents_approved', 'contract_signed', 'approved', 'rejected', 'cancelled']))
                        <button onclick="conductInspection()" class="w-full px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                            </svg>
                            Conduct Inspection
                        </button>
                    @endif
                    
                     @if(!in_array($application->status, ['documents_pending', 'documents_submitted', 'documents_approved', 'contract_signed', 'approved', 'rejected', 'cancelled']))
                    <button onclick="reviewDocuments()" class="w-full px-4 py-2 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                        Review Documents
                    </button>
                    @endif
                    
                          @if(in_array($application->status, $allowedProfileStatuses) && $profile?->exists())
                          <a href="{{ route('consultant.educators.profile', $profile->id) }}"
                            class="w-full px-4 py-2 bg-indigo-50 dark:bg-indigo-900/20
                                text-indigo-700 dark:text-indigo-300
                                rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30
                                transition-colors text-sm font-medium text-left
                                flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 100 8 4 4 0 000-8zm-7 14a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                            View Educator Profile
                        </a>
                    @else
                        <span class="text-gray-500 dark:text-gray-400 text-sm">
                            Educator profile not available
                        </span>
                    @endif

                   @if(!in_array($application->status, ['documents_pending', 'documents_submitted', 'documents_approved', 'second_inspection_scheduled', 'second_inspection_completed', 'contract_signing_scheduled', 'contract_signed', 'approved', 'rejected', 'cancelled']))
                    <form action="{{ route('consultant.applications.enable-documents', $application) }}" 
                          method="POST" 
                          onsubmit="return confirm('Enable document uploads for this application?')">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm5 12a1 1 0 100-2 1 1 0 000 2zm-3-3a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                            Enable Document Upload
                        </button>
                    </form>
                @endif
                
                {{-- Approve Application Button - Only show when contract is signed --}}
                    @if($application->status === 'contract_signed')
                        <form action="{{ route('consultant.applications.approve', $application) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to approve this application? This will grant the applicant a license valid for one year.')">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-semibold transition-all text-sm text-left flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Approve Application
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Activity Timeline</h3>
                <div class="space-y-4">
                    @if($application->submitted_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Application Submitted</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->submitted_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @foreach($application->appointments()->latest('scheduled_at')->take(3)->get() as $appointment)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $appointment->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                    <a href="#" class="text-sm text-orange-600 hover:text-orange-700 font-medium">View Full History →</a>
                </div>
            </div>

           <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Consultant Notes</h3>
        
            <form action="{{ route('consultant.applications.notes.save', $application->id) }}"
                  method="POST">
                @csrf
        
                <textarea name="admin_notes"
                          rows="4"
                          placeholder="Add internal notes about this application..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white text-sm">{{ $application->admin_notes }}</textarea>
        
                <button type="submit"
                        class="mt-3 w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors text-sm">
                    Save Notes
                </button>
            </form>
        </div>

        </div>
    </div>
</div>

<!-- Approve Application Modal -->
<div x-data="{ showApproveModal: false }" 
     @open-approve-modal.window="showApproveModal = true"
     @keydown.escape.window="showApproveModal = false">
    <div x-show="showApproveModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showApproveModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 text-center">Approve Application</h3>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
                    Are you sure you want to approve this application? The applicant will be notified and their dayhome will be licensed.
                </p>
                
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-300">
                        <strong>Applicant:</strong> {{ $application->full_name }}
                    </p>
                    <p class="text-sm text-green-800 dark:text-green-300">
                        <strong>Application #:</strong> {{ $application->application_number }}
                    </p>
                </div>
                
                <form method="POST" action="{{ route('consultant.applications.approve', $application) }}">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" @click="showApproveModal = false"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                            Approve Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Application Modal -->
<div x-data="{ showRejectModal: false }" 
     @open-reject-modal.window="showRejectModal = true"
     @keydown.escape.window="showRejectModal = false">
    <div x-show="showRejectModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showRejectModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Reject Application</h3>
                
                <form method="POST" action="{{ route('consultant.applications.reject', $application) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Rejection *
                        </label>
                        <textarea name="rejection_reason" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Provide a detailed reason for rejecting this application..."></textarea>
                    </div>
                    
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">
                            <strong>Warning:</strong> This action will notify the applicant and they will not be able to proceed with this application.
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" @click="showRejectModal = false"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                            Reject Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Activate profile Modal -->
<div x-data="{ showActivateModal: false }" 
     @open-activate-modal.window="showActivateModal = true"
     @keydown.escape.window="showActivateModal = false">
    <div x-show="showActivateModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" 
                 @click="showActivateModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 text-center">
                    Activate profile
                </h3>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
                    Are you sure you want to activate this application? Applicant access and system functions will be enabled.
                </p>
                
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <strong>Applicant:</strong> {{ $application->full_name }}
                    </p>
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <strong>Application #:</strong> {{ $application->application_number }}
                    </p>
                </div>
                
                <form method="POST" action="{{ route('consultant.applications.activate-dayhome', $application) }}">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" 
                                @click="showActivateModal = false"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            Activate profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Mark Compliance Due Modal -->
<div x-data="{ open: false }" 
     @open-compliance-due-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Mark Compliance Inspection Due</h3>
            <form method="POST" action="{{ route('consultant.applications.mark-compliance-due', $application) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason
                    </label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                              placeholder="Why is a compliance inspection due?"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                        Mark as Due
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div x-data="{ open: false }" 
     @open-suspend-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Suspend Dayhome</h3>
            <form method="POST" action="{{ route('consultant.applications.suspend-dayhome', $application) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Suspension <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                              placeholder="Explain the reason for suspension..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg">
                        Suspend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Terminate Modal -->
<div x-data="{ open: false }" 
     @open-terminate-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Terminate Dayhome</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This action is permanent and cannot be undone. The dayhome will be completely terminated.
            </p>
            <form method="POST" action="{{ route('consultant.applications.terminate-dayhome', $application) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Termination <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                              placeholder="Explain the reason for termination..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        Terminate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reinstate Modal -->
<div x-data="{ open: false }" 
     @open-reinstate-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reinstate Dayhome</h3>
            <form method="POST" action="{{ route('consultant.applications.reinstate-dayhome', $application) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                              placeholder="Add any notes about the reinstatement..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Reinstate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Require Remediation Modal -->
<div x-data="{ open: false }" 
     @open-remediation-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Require Remediation</h3>

            <form method="POST" action="{{ route('consultant.applications.require-remediation', $application) }}">
                @csrf

                <!-- Remediation Reason -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Remediation Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="remediation_reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                              placeholder="Describe the reason remediation is required..."></textarea>
                </div>

                <!-- Deadline -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Deadline <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="deadline" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                        Require Remediation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<x-appointments.schedule-modal />

@push('scripts')
<script>
function conductInspection() {
    // Redirect to inspection selection page which will show available inspection types
    window.location.href = "{{ route('consultant.inspections.create', ['application_id' => $application->id]) }}";
}

function reviewDocuments() {
    window.location.href = "{{ route('consultant.documents.pending-review') }}";
}

function addNote() {
    alert('Add note functionality');
}
function consultantDocumentUpload() {
    return {
        files: [],
        
        handleFileSelect(event) {
            const selectedFiles = Array.from(event.target.files);
            
            // Validate file count
            if (selectedFiles.length > 50) {
                alert('Maximum 50 files allowed');
                event.target.value = '';
                return;
            }
            
            // Validate each file
            const validFiles = selectedFiles.filter(file => {
                if (file.size > 10 * 1024 * 1024) {
                    alert(`${file.name} exceeds 10MB limit`);
                    return false;
                }
                return true;
            });
            
            this.files = validFiles;
        },
        
        removeFile(index) {
            this.files.splice(index, 1);
            
            // Optional: If you want to clear the file input when all files are removed
            if (this.files.length === 0) {
                const fileInput = document.querySelector('input[type="file"][name="files[]"]');
                if (fileInput) {
                    fileInput.value = '';
                }
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    }
}
</script>
@endpush

<script>
[x-cloak] { display: none !important; }
</script>
<style>
[x-cloak] { display: none !important; }
</style>
@endsection