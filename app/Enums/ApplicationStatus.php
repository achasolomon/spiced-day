<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case MEET_AND_GREET_SCHEDULED = 'meet_and_greet_scheduled';
    case MEET_AND_GREET_COMPLETED = 'meet_and_greet_completed';
    case INITIAL_INSPECTION_SCHEDULED = 'initial_inspection_scheduled';
    case INITIAL_INSPECTION_COMPLETED = 'initial_inspection_completed';
    case DOCUMENTS_PENDING = 'documents_pending';
    case DOCUMENTS_SUBMITTED = 'documents_submitted';
    case DOCUMENTS_APPROVED = 'documents_approved';
    case SECOND_INSPECTION_SCHEDULED = 'second_inspection_scheduled';
    case SECOND_INSPECTION_COMPLETED = 'second_inspection_completed';
    
    // NEW: Final Inspection Phase
    case FINAL_INSPECTION_SCHEDULED = 'final_inspection_scheduled';
    case FINAL_INSPECTION_COMPLETED = 'final_inspection_completed';
    case FINAL_INSPECTION_PASSED = 'final_inspection_passed';
    case FINAL_INSPECTION_FAILED = 'final_inspection_failed';
    
    case CONTRACT_SIGNING_SCHEDULED = 'contract_signing_scheduled';
    case CONTRACT_SIGNED = 'contract_signed';
    case APPROVED = 'approved';
    
    // NEW: Post-Approval Statuses
    case ACTIVE = 'active';
    case COMPLIANCE_INSPECTION_DUE = 'compliance_inspection_due';
    case COMPLIANCE_INSPECTION_SCHEDULED = 'compliance_inspection_scheduled';
    case COMPLIANCE_INSPECTION_COMPLETED = 'compliance_inspection_completed';
    case SUSPENDED = 'suspended';
    case UNDER_REVIEW = 'under_review';
    case REMEDIATION_REQUIRED = 'remediation_required';
    case TERMINATED = 'terminated';
    
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::MEET_AND_GREET_SCHEDULED => 'Meet & Greet Scheduled',
            self::MEET_AND_GREET_COMPLETED => 'Meet & Greet Completed',
            self::INITIAL_INSPECTION_SCHEDULED => 'Initial Inspection Scheduled',
            self::INITIAL_INSPECTION_COMPLETED => 'Initial Inspection Completed',
            self::DOCUMENTS_PENDING => 'Documents Pending',
            self::DOCUMENTS_SUBMITTED => 'Documents Submitted',
            self::DOCUMENTS_APPROVED => 'Documents Approved',
            self::SECOND_INSPECTION_SCHEDULED => 'Second Inspection Scheduled',
            self::SECOND_INSPECTION_COMPLETED => 'Second Inspection Completed',
            
            // NEW: Final Inspection
            self::FINAL_INSPECTION_SCHEDULED => 'Final Inspection Scheduled',
            self::FINAL_INSPECTION_COMPLETED => 'Final Inspection Completed',
            self::FINAL_INSPECTION_PASSED => 'Final Inspection Passed',
            self::FINAL_INSPECTION_FAILED => 'Final Inspection Failed',
            
            self::CONTRACT_SIGNING_SCHEDULED => 'Contract Signing Scheduled',
            self::CONTRACT_SIGNED => 'Contract Signed',
            self::APPROVED => 'Approved',
            
            // NEW: Post-Approval
            self::ACTIVE => 'Active',
            self::COMPLIANCE_INSPECTION_DUE => 'Compliance Inspection Due',
            self::COMPLIANCE_INSPECTION_SCHEDULED => 'Compliance Inspection Scheduled',
            self::COMPLIANCE_INSPECTION_COMPLETED => 'Compliance Inspection Completed',
            self::SUSPENDED => 'Suspended',
            self::UNDER_REVIEW => 'Under Review',
            self::REMEDIATION_REQUIRED => 'Remediation Required',
            self::TERMINATED => 'Terminated',
            
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::MEET_AND_GREET_SCHEDULED, 
            self::INITIAL_INSPECTION_SCHEDULED,
            self::SECOND_INSPECTION_SCHEDULED,
            self::FINAL_INSPECTION_SCHEDULED,
            self::CONTRACT_SIGNING_SCHEDULED,
            self::COMPLIANCE_INSPECTION_SCHEDULED => 'yellow',
            
            self::MEET_AND_GREET_COMPLETED,
            self::INITIAL_INSPECTION_COMPLETED,
            self::DOCUMENTS_SUBMITTED,
            self::SECOND_INSPECTION_COMPLETED,
            self::FINAL_INSPECTION_COMPLETED,
            self::COMPLIANCE_INSPECTION_COMPLETED => 'indigo',
            
            self::DOCUMENTS_PENDING,
            self::COMPLIANCE_INSPECTION_DUE => 'orange',
            
            self::DOCUMENTS_APPROVED,
            self::FINAL_INSPECTION_PASSED => 'teal',
            
            self::CONTRACT_SIGNED,
            self::APPROVED,
            self::ACTIVE => 'green',
            
            self::SUSPENDED,
            self::UNDER_REVIEW,
            self::REMEDIATION_REQUIRED,
            self::FINAL_INSPECTION_FAILED,
            self::REJECTED,
            self::CANCELLED,
            self::TERMINATED => 'red',
        };
    }

    public function nextStatus(): ?self
    {
        return match($this) {
            self::DRAFT => self::SUBMITTED,
            self::SUBMITTED => self::MEET_AND_GREET_SCHEDULED,
            self::MEET_AND_GREET_SCHEDULED => self::MEET_AND_GREET_COMPLETED,
            self::MEET_AND_GREET_COMPLETED => self::INITIAL_INSPECTION_SCHEDULED,
            self::INITIAL_INSPECTION_SCHEDULED => self::INITIAL_INSPECTION_COMPLETED,
            self::INITIAL_INSPECTION_COMPLETED => self::DOCUMENTS_PENDING,
            self::DOCUMENTS_PENDING => self::DOCUMENTS_SUBMITTED,
            self::DOCUMENTS_SUBMITTED => self::DOCUMENTS_APPROVED,
            self::DOCUMENTS_APPROVED => self::SECOND_INSPECTION_SCHEDULED,
            self::SECOND_INSPECTION_SCHEDULED => self::SECOND_INSPECTION_COMPLETED,
            
            // NEW: Final Inspection flow
            self::SECOND_INSPECTION_COMPLETED => self::FINAL_INSPECTION_SCHEDULED,
            self::FINAL_INSPECTION_SCHEDULED => self::FINAL_INSPECTION_COMPLETED,
            self::FINAL_INSPECTION_PASSED => self::CONTRACT_SIGNING_SCHEDULED,
            
            self::CONTRACT_SIGNING_SCHEDULED => self::CONTRACT_SIGNED,
            self::CONTRACT_SIGNED => self::APPROVED,
            
            // NEW: Post-approval flow (manual transitions)
            self::APPROVED => null, 
            self::ACTIVE => self::COMPLIANCE_INSPECTION_COMPLETED, 
            
            default => null,
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        // Can always reject or cancel (except for terminated applications)
        if (in_array($newStatus, [self::REJECTED, self::CANCELLED]) && $this !== self::TERMINATED) {
            return true;
        }

        // Can't change if already in terminal state (but APPROVED can go to ACTIVE)
        if (in_array($this, [self::REJECTED, self::CANCELLED, self::TERMINATED])) {
            return false;
        }
        
        // ACTIVE is terminal - can only transition to compliance/suspension states
        if ($this === self::ACTIVE && !in_array($newStatus, [
            self::COMPLIANCE_INSPECTION_DUE,
            self::COMPLIANCE_INSPECTION_SCHEDULED,
            self::SUSPENDED,
            self::TERMINATED
        ])) {
            return false;
        }

        // Allow idempotent transitions (already in the target status)
        if ($this === $newStatus) {
            return true;
        }

        // Check if it's the next status in sequence
        if ($this->nextStatus() === $newStatus) {
            return true;
        }

        // Allow manual transitions for post-approval statuses
        $manualTransitions = [
            self::APPROVED->value => [self::ACTIVE->value],
            self::ACTIVE->value => [
                self::COMPLIANCE_INSPECTION_DUE->value,
                self::COMPLIANCE_INSPECTION_SCHEDULED->value,
                self::SUSPENDED->value,
                self::TERMINATED->value,
            ],
            self::COMPLIANCE_INSPECTION_DUE->value => [self::COMPLIANCE_INSPECTION_SCHEDULED->value],
            self::COMPLIANCE_INSPECTION_SCHEDULED->value => [self::COMPLIANCE_INSPECTION_COMPLETED->value],
            self::COMPLIANCE_INSPECTION_COMPLETED->value => [
                self::ACTIVE->value,
                self::SUSPENDED->value,
                self::REMEDIATION_REQUIRED->value,
            ],
            self::SUSPENDED->value => [
                self::ACTIVE->value,
                self::REMEDIATION_REQUIRED->value,
                self::TERMINATED->value,
            ],
            self::REMEDIATION_REQUIRED->value => [
                self::ACTIVE->value,
                self::SUSPENDED->value,
                self::TERMINATED->value,
            ],
            // Allow consultants to enable document uploads from various stages
            self::SUBMITTED->value => [self::DOCUMENTS_PENDING->value],
            self::MEET_AND_GREET_SCHEDULED->value => [self::DOCUMENTS_PENDING->value],
            self::MEET_AND_GREET_COMPLETED->value => [self::DOCUMENTS_PENDING->value],
            self::INITIAL_INSPECTION_SCHEDULED->value => [self::DOCUMENTS_PENDING->value],
            self::DOCUMENTS_SUBMITTED->value => [self::DOCUMENTS_PENDING->value],
            self::INITIAL_INSPECTION_COMPLETED->value => [
                self::DOCUMENTS_PENDING->value,
                self::SECOND_INSPECTION_COMPLETED->value,
            ],
            self::DOCUMENTS_APPROVED->value => [
                self::FINAL_INSPECTION_SCHEDULED->value,
                self::FINAL_INSPECTION_COMPLETED->value,
                self::FINAL_INSPECTION_FAILED->value,
            ],
            self::SECOND_INSPECTION_COMPLETED->value => [
                self::FINAL_INSPECTION_COMPLETED->value,
                self::FINAL_INSPECTION_SCHEDULED->value,
                self::FINAL_INSPECTION_FAILED->value,
            ],
            self::FINAL_INSPECTION_SCHEDULED->value => [
                self::FINAL_INSPECTION_COMPLETED->value,
                self::FINAL_INSPECTION_FAILED->value,
            ],
            // transitions from FINAL_INSPECTION_COMPLETED
            self::FINAL_INSPECTION_COMPLETED->value => [
                self::FINAL_INSPECTION_PASSED->value,
                self::FINAL_INSPECTION_FAILED->value,
                self::CONTRACT_SIGNING_SCHEDULED->value, 
            ],
            // transitions from FINAL_INSPECTION_PASSED
            self::FINAL_INSPECTION_PASSED->value => [
                self::CONTRACT_SIGNING_SCHEDULED->value, 
            ],
        ];

        // Special case: Allow transition to DOCUMENTS_PENDING from any status that's not terminal
        if ($newStatus === self::DOCUMENTS_PENDING) {
            $nonTerminalStatuses = [
                self::DRAFT, self::SUBMITTED,
                self::MEET_AND_GREET_SCHEDULED, self::MEET_AND_GREET_COMPLETED,
                self::INITIAL_INSPECTION_SCHEDULED, self::INITIAL_INSPECTION_COMPLETED,
                self::DOCUMENTS_PENDING, self::DOCUMENTS_SUBMITTED, self::DOCUMENTS_APPROVED,
                self::SECOND_INSPECTION_SCHEDULED, self::SECOND_INSPECTION_COMPLETED,
                self::FINAL_INSPECTION_SCHEDULED, self::FINAL_INSPECTION_COMPLETED,
                self::FINAL_INSPECTION_PASSED, self::FINAL_INSPECTION_FAILED,
                self::CONTRACT_SIGNING_SCHEDULED, self::CONTRACT_SIGNED,
            ];
            
            if (in_array($this, $nonTerminalStatuses)) {
                return true;
            }
        }

        return in_array($newStatus->value, $manualTransitions[$this->value] ?? []);
    }
}