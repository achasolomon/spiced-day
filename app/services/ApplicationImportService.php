<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationImport;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ApplicationImportService
{
    /**
     * Column mapping from Excel to database fields
     */
    protected $columnMapping = [
        'DayhomeEducatorApplication_Id' => 'legacy_id',
        'Name_First' => 'educator_first_name',
        'Name_Last' => 'educator_last_name',
        'DayhomeAddress_Line1' => 'address_line_1',
        'DayhomeAddress_City' => 'city',
        'DayhomeAddress_State' => 'province',
        'DayhomeAddress_PostalCode' => 'postal_code',
        'Email' => 'email',
        'Phone' => 'phone',
        'ChildcareLevel' => 'childcare_level',
        'WereYouReferredByASPICEdEducatorIfSoPleaseStateWhoFirstAndLastName_1' => 'referred_by',
        'DoYouHaveAClearCriminalRecordCheckDatedWithinTheLast6Months_2' => 'has_criminal_record_check',
        'DoYouHaveAValidFirstAidCPRCertificate_3' => 'has_first_aid_cpr',
        'LanguagesSpoken_4' => 'languages_spoken',
        'ListAnyRelevantChildcareEducationtraining' => 'childcare_education',
        'DayhomeDetails__5HowManyOtherPeopleResideInTheHome' => 'home_residents_count',
        'DayhomeDetails__6WhatAreTheFullNamesAgeAndOccupationOfOtherPeopleResidingInTheHome' => 'home_residents_details',
        'DayhomeDetails__7DoYouOrAnybodyListedAboveSmokeYesNoPleaseIndicateWho' => 'smoking_details',
        'DayhomeDetails__8DoYouHaveAnyPetsInTheHomeIfSoWhatKindOfAnimalsAndHowManyDoYouHave' => 'pets_details',
        'DayhomeDetails__9AreYouCurrentlyRunningADayhomeIfSoHowManyChildrenDoYouHaveHowOldAreTheyAreYouCurrentlyProvidingMealsWhatAreYourCurrentHours' => 'current_operation_details',
        'DayhomeDetails__10WouldYouBeAvailableForEveningovernightCarecareAfter6PM' => 'evening_overnight_care',
        'DayhomeDetails__11WhatTypeOfHomeDoYouLiveInApartmentDuplexHouseTownhouseDoYouRentOrOwnTheSpace' => 'home_type_ownership',
        'DayhomeDetails__12WhatDateAreYouHopingToStartRunningYourApprovedDayhome' => 'desired_start_date',
        'DayhomeDetails__13AreYouComfortableProvidingCareForChildrenWithSpecialNeeds' => 'comfortable_special_needs',
        'DayhomeDetails__14WhyDidYouChooseToBecomeADayhomeEducator' => 'motivation',
        'DayhomeDetails__15WhyAreYouInterestedInJoiningSPICEdDayhomeAgencyHowDidYouHearAboutUs' => 'why_spiced',
        'DayhomeDetails__16WhatIsYourPhilosophyOnEarlyChildhoodEducationAndWhy' => 'education_philosophy',
        'DayhomeDetails__17DoYouHaveAFullyFencedBackyard' => 'fenced_backyard',
        'DayhomeDetails__18WhatDoesYourProgramPlanningProcessLooksLike' => 'program_planning_process',
        'Entry_Status' => 'entry_status',
        'Entry_DateCreated' => 'entry_date_created',
        'Entry_DateSubmitted' => 'entry_date_submitted',
        'Entry_DateUpdated' => 'entry_date_updated',
    ];

    /**
     * Process the uploaded Excel file
     */
    public function processImport(ApplicationImport $import)
    {
        try {
            $import->update([
                'status' => 'processing',
                'started_at' => now()
            ]);

            $filePath = Storage::path($import->file_path);
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // First row is headers
            $headers = array_shift($rows);
            $import->update(['total_rows' => count($rows)]);

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we removed header row

                try {
                    $mappedData = $this->mapRowToApplication($headers, $row);
                    
                    // Skip empty rows
                    if (empty($mappedData['email']) || empty($mappedData['educator_first_name'])) {
                        continue;
                    }

                    $this->createApplication($mappedData);
                    $successCount++;

                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'data' => $this->sanitizeRowData($row),
                        'error' => $e->getMessage()
                    ];

                    Log::error('Failed to import application row', [
                        'row' => $rowNumber,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $import->update([
                'status' => 'completed',
                'successful_imports' => $successCount,
                'failed_imports' => $failedCount,
                'errors' => $errors,
                'completed_at' => now()
            ]);

            return [
                'success' => true,
                'imported' => $successCount,
                'failed' => $failedCount
            ];

        } catch (\Exception $e) {
            $import->update([
                'status' => 'failed',
                'completed_at' => now()
            ]);

            Log::error('Application import failed', [
                'import_id' => $import->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Map Excel row to application data
     */
    protected function mapRowToApplication($headers, $row)
    {
        $data = [];

        foreach ($headers as $index => $header) {
            $value = $row[$index] ?? null;
            
            // Map column to database field
            if (isset($this->columnMapping[$header])) {
                $field = $this->columnMapping[$header];
                $data[$field] = $this->transformValue($field, $value);
            }
        }

        // Post-processing
        $data = $this->postProcessData($data);

        return $data;
    }

    /**
     * Transform values to appropriate format
     */
    protected function transformValue($field, $value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Boolean fields
        if (in_array($field, [
            'has_criminal_record_check',
            'has_first_aid_cpr',
            'has_pets',
            'evening_overnight_care',
            'comfortable_special_needs',
            'fenced_backyard',
            'currently_operating'
        ])) {
            return $this->parseBoolean($value);
        }

        // Date fields
        if (in_array($field, ['desired_start_date', 'entry_date_created', 'entry_date_submitted', 'entry_date_updated'])) {
            return $this->parseDate($value);
        }

        // Integer fields
        if ($field === 'home_residents_count') {
            return (int) $value;
        }

        // String fields - clean up
        return trim($value);
    }

    /**
     * Parse boolean values from Excel
     */
    protected function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'y']);
    }

    /**
     * Parse date values from Excel
     */
    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Excel dates are stored as numbers (days since 1900-01-01)
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            }

            // Try parsing as string
            return date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Post-process data
     */
    protected function postProcessData($data)
    {
        // Extract home type and ownership from combined field
        if (isset($data['home_type_ownership'])) {
            $this->parseHomeTypeOwnership($data);
        }

        // Parse smoking status
        if (isset($data['smoking_details'])) {
            $data['smoking_status'] = empty($data['smoking_details']) || 
                                      strtolower($data['smoking_details']) === 'no' 
                                      ? 'no' 
                                      : 'yes_please_specify';
        }

        // Determine if has pets
        if (isset($data['pets_details'])) {
            $data['has_pets'] = !empty($data['pets_details']) && 
                                strtolower($data['pets_details']) !== 'no';
        }

        // Determine if currently operating
        if (isset($data['current_operation_details'])) {
            $data['currently_operating'] = !empty($data['current_operation_details']) && 
                                           strtolower(substr($data['current_operation_details'], 0, 3)) === 'yes';
        }

        // Set default status
        $data['status'] = $this->determineStatus($data);
        $data['current_stage'] = $this->determineStage($data);

        // Generate application number
        $data['application_number'] = Application::generateApplicationNumber($data['postal_code'] ?? '000');

        return $data;
    }

    /**
     * Parse home type and ownership from combined field
     */
    protected function parseHomeTypeOwnership(&$data)
    {
        $combined = strtolower($data['home_type_ownership']);
        
        // Extract home type
        if (str_contains($combined, 'apartment')) {
            $data['home_type'] = 'apartment';
        } elseif (str_contains($combined, 'duplex')) {
            $data['home_type'] = 'duplex';
        } elseif (str_contains($combined, 'townhouse')) {
            $data['home_type'] = 'townhouse';
        } elseif (str_contains($combined, 'house')) {
            $data['home_type'] = 'house';
        }

        // Extract ownership
        if (str_contains($combined, 'rent')) {
            $data['home_ownership'] = 'rent';
        } elseif (str_contains($combined, 'own')) {
            $data['home_ownership'] = 'own';
        }

        unset($data['home_type_ownership']);
    }

    /**
     * Determine application status based on entry status
     */
    protected function determineStatus($data)
    {
        $entryStatus = strtolower($data['entry_status'] ?? '');
        
        if (str_contains($entryStatus, 'approved')) {
            return ApplicationStatus::APPROVED->value;
        }
        
        if (str_contains($entryStatus, 'rejected')) {
            return ApplicationStatus::REJECTED->value;
        }
        
        if (str_contains($entryStatus, 'cancelled')) {
            return ApplicationStatus::CANCELLED->value;
        }

        if (!empty($data['entry_date_submitted'])) {
            return ApplicationStatus::SUBMITTED->value;
        }

        return ApplicationStatus::DRAFT->value;
    }

    /**
     * Determine current stage
     */
    protected function determineStage($data)
    {
        $status = $data['status'];
        
        if ($status === ApplicationStatus::APPROVED->value) {
            return 'approved';
        }
        
        return 'intake';
    }

    /**
     * Create application from mapped data
     */
    protected function createApplication($data)
    {
        DB::beginTransaction();
        try {
            // Check if application already exists with this email
            $existing = Application::where('email', $data['email'])->first();
            
            if ($existing) {
                throw new \Exception("Application with email {$data['email']} already exists");
            }

            // Set timestamps from legacy data
            $timestamps = [];
            if (isset($data['entry_date_submitted'])) {
                $timestamps['submitted_at'] = $data['entry_date_submitted'];
            }
            if ($data['status'] === ApplicationStatus::APPROVED->value && isset($data['entry_date_updated'])) {
                $timestamps['approved_at'] = $data['entry_date_updated'];
            }

            // Remove temporary fields
            unset($data['legacy_id'], $data['entry_status'], $data['entry_date_created'], 
                  $data['entry_date_submitted'], $data['entry_date_updated']);

            // Create application without user (legacy import)
            $application = Application::create(array_merge($data, $timestamps, [
                'user_id' => null, // Legacy applications don't have users yet
                'anonymous_token' => \Str::random(64), // Generate token for future linking
            ]));

            \App\Models\AuditLog::log(
                'application_imported',
                $application,
                'Legacy application imported from Excel'
            );

            DB::commit();

            return $application;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Sanitize row data for error logging (remove sensitive info)
     */
    protected function sanitizeRowData($row)
    {
        // Only return first few columns for error reporting
        return array_slice($row, 0, 5);
    }
}