<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Mail\ApplicationActivationEmail;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\AuditLog;
use App\Services\CertificateService;
use App\Observers\ApplicationStageMap;
use Carbon\Carbon;

class ApplicationImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('file');
            
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $header = array_shift($rows);
            $headerMap = array_flip($header);
            
            $imported = 0;
            $failed = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $get = function($key) use ($row, $headerMap) {
                        return isset($headerMap[$key]) && isset($row[$headerMap[$key]]) 
                            ? trim($row[$headerMap[$key]]) 
                            : null;
                    };

                    // Extract data from Excel
                    $firstName = $get('Name_First');
                    $lastName = $get('Name_Last');
                    $email = $get('Email');
                    $phone = $get('Phone');
                    $addressLine1 = $get('DayhomeAddress_Line1');
                    $city = $get('DayhomeAddress_City');
                    $province = $get('DayhomeAddress_State');
                    $postalCode = $get('DayhomeAddress_PostalCode');

                    // Validate required fields
                    if (empty($firstName)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Missing first name";
                        continue;
                    }
                    
                    if (empty($lastName)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Missing last name";
                        continue;
                    }
                    
                    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Invalid or missing email";
                        continue;
                    }

                    if (empty($addressLine1)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Missing address";
                        continue;
                    }

                    if (empty($city)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Missing city";
                        continue;
                    }

                    if (empty($province)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Missing province";
                        continue;
                    }

                    if (empty($postalCode)) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Missing postal code";
                        continue;
                    }

                    // Check for duplicate application
                    if (Application::where('email', $email)->exists()) {
                        $failed++;
                        $errors[] = "Row " . ($index + 2) . ": Duplicate email - {$email}";
                        continue;
                    }

                    // Parse boolean values
                    $parseBool = function($value) {
                        if (is_bool($value)) return $value;
                        $value = strtolower(trim($value));
                        return in_array($value, ['yes', 'true', '1', 'y']);
                    };

                    // Parse integer values
                    $parseInt = function($value) {
                        if (empty($value)) return 0;
                        preg_match('/\d+/', strval($value), $matches);
                        return isset($matches[0]) ? (int)$matches[0] : 0;
                    };

                    // Parse date
                    $parseDate = function($value) {
                        if (empty($value)) return null;
                        try {
                            return Carbon::parse($value)->format('Y-m-d');
                        } catch (\Exception $e) {
                            return null;
                        }
                    };

                    // ✅ Generate unique anonymous token for account creation
                    $anonymousToken = Str::random(64);
                    $status = $this->normalizeStatus($get('Entry_Status'));
                    $currentStage = ApplicationStageMap::fromStatus($status);


                    // Create application
                    $applicationData = [
                        'application_number' => $get('DayhomeEducatorApplication_Id') ?? 'APP-' . strtoupper(Str::random(8)),
                        'user_id' => null, // Will be linked after account creation
                        'consultant_id' => auth()->id(),
                        
                        // ✅ Track import metadata
                        'imported_by_consultant' => auth()->id(),
                        'imported_at' => now(),
                        
                        // Educator Info
                        'educator_first_name' => $firstName,
                        'educator_last_name' => $lastName,
                        'email' => $email,
                        'phone' => $phone ?? '',
                        
                        // Address
                        'address_line_1' => $addressLine1,
                        'city' => $city,
                        'province' => $province,
                        'postal_code' => $postalCode,
                        
                        // Professional Details
                        'childcare_level' => $get('ChildcareLevel'),
                        'referred_by' => $get('WereYouReferredByASPICEdEducatorIfSoPleaseStateWhoFirstAndLastName'),
                        'has_criminal_record_check' => $parseBool($get('_1DoYouHaveAClearCriminalRecordCheckDatedWithinTheLast6Months')),
                        'has_first_aid_cpr' => $parseBool($get('_2DoYouHaveAValidFirstAidCPRCertificate')),
                        'languages_spoken' => $get('_3LanguagesSpoken'),
                        'childcare_education' => $get('_4ListAnyRelevantChildcareEducationtraining'),
                        
                        // Home Details
                        'home_residents_count' => $parseInt($get('DayhomeDetails__5HowManyOtherPeopleResideInTheHome')),
                        'home_residents_details' => $get('DayhomeDetails__6WhatAreTheFullNamesAgeAndOccupationOfOtherPeopleResidingInTheHome'),
                        'smoking_status' => $parseBool($get('DayhomeDetails__7DoYouOrAnybodyListedAboveSmokeYesNoPleaseIndicateWho')) ? 'yes_please_specify' : 'no',
                        'smoking_details' => $get('DayhomeDetails__7DoYouOrAnybodyListedAboveSmokeYesNoPleaseIndicateWho'),
                        'has_pets' => $parseBool($get('DayhomeDetails__8DoYouHaveAnyPetsInTheHomeIfSoWhatKindOfAnimalsAndHowManyDoYouHave')),
                        'pets_details' => $get('DayhomeDetails__8DoYouHaveAnyPetsInTheHomeIfSoWhatKindOfAnimalsAndHowManyDoYouHave'),
                        
                        // Current Operation
                        'currently_operating' => $parseBool($get('DayhomeDetails__9AreYouCurrentlyRunningADayhomeIfSoHowManyChildrenDoYouHaveHowOldAreTheyAreYouCurrentlyProvidingMealsWhatAreYourCurrentHours')),
                        'current_operation_details' => $get('DayhomeDetails__9AreYouCurrentlyRunningADayhomeIfSoHowManyChildrenDoYouHaveHowOldAreTheyAreYouCurrentlyProvidingMealsWhatAreYourCurrentHours'),
                        'evening_overnight_care' => $parseBool($get('DayhomeDetails__10WouldYouBeAvailableForEveningovernightCarecareAfter6PM')),
                        'home_type' => $this->parseHomeType($get('DayhomeDetails__11WhatTypeOfHomeDoYouLiveInApartmentDuplexHouseTownhouseDoYouRentOrOwnTheSpace')),
                        'home_ownership' => $this->parseHomeOwnership($get('DayhomeDetails__11WhatTypeOfHomeDoYouLiveInApartmentDuplexHouseTownhouseDoYouRentOrOwnTheSpace')),
                        
                        // Goals
                        'desired_start_date' => $parseDate($get('DayhomeDetails__12WhatDateAreYouHopingToStartRunningYourApprovedDayhome')),
                        'comfortable_special_needs' => $parseBool($get('DayhomeDetails__13AreYouComfortableProvidingCareForChildrenWithSpecialNeeds')),
                        'motivation' => $get('DayhomeDetails__14WhyDidYouChooseToBecomeADayhomeEducator'),
                        'why_spiced' => $get('DayhomeDetails__15WhyAreYouInterestedInJoiningSPICEdDayhomeAgencyHowDidYouHearAboutUs'),
                        'education_philosophy' => $get('DayhomeDetails__16WhatIsYourPhilosophyOnEarlyChildhoodEducationAndWhy'),
                        'fenced_backyard' => $parseBool($get('DayhomeDetails__17DoYouHaveAFullyFencedBackyard')),
                        'program_planning_process' => $get('DayhomeDetails__18WhatDoesYourProgramPlanningProcessLooksLike'),

                        'legacy_import' => $isLegacy ?? true,
                        'workflow_concluded' => $isLegacy ?? true,

                        // ✅ Set proper lifecycle status
                        'status' => $get('Entry_Status'),
                        'current_stage' => $currentStage,
                        'submitted_at' => $get('Entry_DateSubmitted') 
                            ? Carbon::parse($get('Entry_DateSubmitted')) 
                            : now(),
                        
                        // ✅ Anonymous token for account creation (uses existing column)
                        'anonymous_token' => $anonymousToken,
                        'account_created' => false,
                    ];

                    $application = Application::create($applicationData);
                    
                    // Force update completion percentage
                    $application->updateQuietly(['completion_percentage' => 100.00]);
                   
                    // ✅ Auto-approve & generate certificate for imported APPROVED applications
                   if ($status === 'approved') {
                        try {
                            // Ensure approval metadata
                            $application->updateQuietly([
                                'approved_at' => $application->approved_at ?? now(),
                                'license_expires_at' => $application->license_expires_at ?? now()->addYear(),
                            ]);

                            $certificateService = new CertificateService();
                            $certificate = $certificateService->generateCertificate(
                                $application,
                                auth()->id()
                            );

                            AuditLog::log(
                                'certificate_generated',
                                $application,
                                "Certificate auto-generated during import: {$certificate->certificate_number}"
                            );
                        } catch (\Throwable $e) {
                            Log::error('Auto certificate generation failed', [
                                'application_id' => $application->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    
                    // ✅ Send activation email using existing anonymous registration flow
                    try {
                        Mail::to($application->email)->send(new ApplicationActivationEmail($application));
                        
                        Log::info('Activation email sent for imported application', [
                            'application_id' => $application->id,
                            'application_number' => $application->application_number,
                            'email' => $application->email
                        ]);
                        
                        AuditLog::log(
                            'application_imported',
                            $application,
                            "Application imported by consultant. Activation email sent to {$application->email}"
                        );
                    } catch (\Exception $e) {
                        Log::error('Failed to send activation email for imported application', [
                            'application_id' => $application->id,
                            'email' => $application->email,
                            'error' => $e->getMessage()
                        ]);
                        // Don't fail the import if email fails
                    }
                    
                    $imported++;

                } catch (\Exception $e) {
                    $failed++;
                    $errorMsg = $e->getMessage();
                    
                    if (str_contains($errorMsg, 'current_stage')) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid stage value";
                    } elseif (str_contains($errorMsg, 'status')) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid status value";
                    } elseif (str_contains($errorMsg, 'Duplicate entry')) {
                        $errors[] = "Row " . ($index + 2) . ": Duplicate application number";
                    } else {
                        $errors[] = "Row " . ($index + 2) . ": " . substr($errorMsg, 0, 100);
                    }
                    
                    Log::error('Import error for row ' . ($index + 2), [
                        'error' => $errorMsg,
                        'row_data' => array_slice($row, 0, 10),
                    ]);
                }
            }

            Log::info('Import Summary', [
                'imported' => $imported,
                'failed' => $failed,
                'activation_emails_sent' => $imported,
            ]);

            return response()->json([
                'success' => $imported > 0,
                'message' => $imported > 0 
                    ? "Import completed! {$imported} imported with activation emails sent, {$failed} failed" 
                    : "No valid records imported. Check errors below.",
                'imported' => $imported,
                'failed' => $failed,
                'errors' => array_slice($errors, 0, 50),
            ]);

        } catch (\Exception $e) {
            Log::error('Import failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'imported' => 0,
                'failed' => 0,
            ], 500);
        }
    }

    private function parseHomeType($value)
    {
        if (empty($value)) return null;
        $value = strtolower(trim($value));
        
        if (str_contains($value, 'apartment')) return 'apartment';
        if (str_contains($value, 'duplex')) return 'duplex';
        if (str_contains($value, 'townhouse')) return 'townhouse';
        if (str_contains($value, 'house') || str_contains($value, 'single family')) return 'house';
        
        return null;
    }

    private function parseHomeOwnership($value)
    {
        if (empty($value)) return null;
        $value = strtolower(trim($value));
        
        if (str_contains($value, 'rent')) return 'rent';
        if (str_contains($value, 'own')) return 'own';
        
        return null;
    }

    private function normalizeStatus(?string $status): ?string
{
    if (!$status) return null;

    return match (strtolower(trim($status))) {
        'approved', 'Approved', 'final approved', 'completed' => 'approved',
        'documents pending' => 'documents_pending',
        'documents approved' => 'documents_approved',
        default => strtolower(trim($status)),
    };
}

}