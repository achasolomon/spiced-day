<?php

namespace App\Enums;

enum ApplicationStage: string
{
    case Application = 'application';
    case MeetAndGreet = 'meet_and_greet';
    case InitialInspection = 'initial_inspection';
    case DocumentSubmission = 'document_submission';
    case SecondInspection = 'second_inspection';
    
    // NEW: Final Inspection Stage
    case FinalInspection = 'final_inspection';
    
    case ContractSigning = 'contract_signing';
    
    // NEW: Post-Approval Stages
    case Approval = 'approval';
    case Active = 'active';
    case ComplianceMonitoring = 'compliance_monitoring';
    case Suspended = 'suspended';

    /**
     * Get human-readable description for a stage.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::Application => 'Application Submission',
            self::MeetAndGreet => 'Meet and Greet',
            self::InitialInspection => 'Initial Inspection',
            self::DocumentSubmission => 'Document Submission',
            self::SecondInspection => 'Second Inspection',
            self::FinalInspection => 'Final Inspection',
            self::ContractSigning => 'Contract Signing',
            self::Approval => 'Approval',
            self::Active => 'Active Dayhome',
            self::ComplianceMonitoring => 'Compliance Monitoring',
            self::Suspended => 'Suspended',
        };
    }

    /**
     * Get all enum values as an array.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}