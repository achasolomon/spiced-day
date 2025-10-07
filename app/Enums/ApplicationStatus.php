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
    case CONTRACT_SIGNING_SCHEDULED = 'contract_signing_scheduled';
    case CONTRACT_SIGNED = 'contract_signed';
    case APPROVED = 'approved';
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
            self::CONTRACT_SIGNING_SCHEDULED => 'Contract Signing Scheduled',
            self::CONTRACT_SIGNED => 'Contract Signed',
            self::APPROVED => 'Approved',
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
            self::CONTRACT_SIGNING_SCHEDULED => 'yellow',
            self::MEET_AND_GREET_COMPLETED,
            self::INITIAL_INSPECTION_COMPLETED,
            self::DOCUMENTS_SUBMITTED,
            self::SECOND_INSPECTION_COMPLETED => 'indigo',
            self::DOCUMENTS_PENDING => 'orange',
            self::DOCUMENTS_APPROVED => 'teal',
            self::CONTRACT_SIGNED,
            self::APPROVED => 'green',
            self::REJECTED,
            self::CANCELLED => 'red',
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
            self::SECOND_INSPECTION_COMPLETED => self::CONTRACT_SIGNING_SCHEDULED,
            self::CONTRACT_SIGNING_SCHEDULED => self::CONTRACT_SIGNED,
            self::CONTRACT_SIGNED => self::APPROVED,
            default => null,
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        // Can always reject or cancel
        if (in_array($newStatus, [self::REJECTED, self::CANCELLED])) {
            return true;
        }

        // Can't change if already in terminal state
        if (in_array($this, [self::APPROVED, self::REJECTED, self::CANCELLED])) {
            return false;
        }

        // Check if it's the next status in sequence
        return $this->nextStatus() === $newStatus;
    }
}