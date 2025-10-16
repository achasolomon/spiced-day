<?php

namespace App\Enums;

enum ApplicationStage: string
{
    case Application = 'application';
    case MeetAndGreet = 'meet_and_greet';
    case InitialInspection = 'initial_inspection';
    case DocumentSubmission = 'document_submission';
    case SecondInspection = 'second_inspection';
    case ContractSigning = 'contract_signing';

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
            self::ContractSigning => 'Contract Signing',
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