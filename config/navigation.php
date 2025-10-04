<?php
// config/navigation.php

return [
    'applicant' => [
        'dashboard' => [
            'name' => 'Dashboard',
            'route' => 'applicant.dashboard',
            'icon' => 'home',
            'badge' => null,
        ],
        'application' => [
            'name' => 'My Application',
            'route' => 'applicant.applications.show',
            'icon' => 'file-text',
            'badge' => 'completion_percentage',
            'submenu' => [
                'view' => [
                    'name' => 'View Application',
                    'route' => 'applicant.applications.show',
                    'icon' => 'eye',
                ],
                'edit' => [
                    'name' => 'Edit Application', 
                    'route' => 'applicant.applications.edit',
                    'icon' => 'edit',
                    'condition' => 'canBeEdited',
                ],
                'status' => [
                    'name' => 'Application Status',
                    'route' => 'applicant.status',
                    'icon' => 'clock',
                ],
            ],
        ],
        'documents' => [
            'name' => 'Documents',
            'route' => 'applicant.documents.index',
            'icon' => 'folder',
            'badge' => 'pending_documents_count',
        ],
        'appointments' => [
            'name' => 'Appointments',
            'route' => 'applicant.appointments.index', 
            'icon' => 'calendar',
            'badge' => 'upcoming_appointments_count',
        ],
        'help' => [
            'name' => 'Help & Support',
            'route' => 'applicant.help',
            'icon' => 'help-circle',
        ],
    ],

    'consultant' => [
        'dashboard' => [
            'name' => 'Dashboard',
            'route' => 'consultant.dashboard',
            'icon' => 'home',
        ],
        'applications' => [
            'name' => 'Applications',
            'route' => 'consultant.applications.index',
            'icon' => 'file-text',
            'badge' => 'active_applications_count',
            'submenu' => [
                'active' => [
                    'name' => 'Active Applications',
                    'route' => 'consultant.applications.index',
                    'icon' => 'activity',
                    'params' => ['status' => 'active'],
                ],
                'pending_review' => [
                    'name' => 'Pending Review',
                    'route' => 'consultant.applications.index',
                    'icon' => 'clock',
                    'params' => ['status' => 'pending'],
                ],
                'completed' => [
                    'name' => 'Completed',
                    'route' => 'consultant.applications.index',
                    'icon' => 'check-circle',
                    'params' => ['status' => 'completed'],
                ],
            ],
        ],
        'appointments' => [
            'name' => 'Appointments',
            'route' => 'consultant.appointments.index',
            'icon' => 'calendar',
            'badge' => 'todays_appointments_count',
            'submenu' => [
                'schedule' => [
                    'name' => 'Schedule Appointment',
                    'route' => 'consultant.appointments.create',
                    'icon' => 'plus',
                ],
                'today' => [
                    'name' => 'Today\'s Appointments',
                    'route' => 'consultant.appointments.index',
                    'icon' => 'today',
                    'params' => ['date' => 'today'],
                ],
                'calendar' => [
                    'name' => 'Calendar View',
                    'route' => 'consultant.calendar',
                    'icon' => 'calendar',
                ],
            ],
        ],
        'inspections' => [
            'name' => 'Inspections',
            'route' => 'consultant.inspections.index',
            'icon' => 'search',
            'submenu' => [
                'pending' => [
                    'name' => 'Pending Inspections',
                    'route' => 'consultant.inspections.index',
                    'icon' => 'clock',
                    'params' => ['status' => 'pending'],
                ],
                'completed' => [
                    'name' => 'Completed Inspections',
                    'route' => 'consultant.inspections.index',
                    'icon' => 'check',
                    'params' => ['status' => 'completed'],
                ],
            ],
        ],
        'documents' => [
            'name' => 'Document Review',
            'route' => 'consultant.documents.pending-review',
            'icon' => 'folder',
            'badge' => 'documents_to_review_count',
        ],
        'reports' => [
            'name' => 'Reports',
            'route' => 'consultant.reports.index',
            'icon' => 'bar-chart',
            'submenu' => [
                'performance' => [
                    'name' => 'My Performance',
                    'route' => 'consultant.reports.performance',
                    'icon' => 'trending-up',
                ],
                'applications' => [
                    'name' => 'Application Reports',
                    'route' => 'consultant.reports.applications',
                    'icon' => 'file-text',
                ],
            ],
        ],
    ],

    'admin' => [
        'dashboard' => [
            'name' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'home',
        ],
        'applications' => [
            'name' => 'Applications',
            'route' => 'admin.applications.index',
            'icon' => 'file-text',
            'badge' => 'pending_applications_count',
            'submenu' => [
                'all' => [
                    'name' => 'All Applications',
                    'route' => 'admin.applications.index',
                    'icon' => 'list',
                ],
                'pending' => [
                    'name' => 'Pending Review',
                    'route' => 'admin.applications.index',
                    'icon' => 'clock',
                    'params' => ['status' => 'pending'],
                ],
                'approved' => [
                    'name' => 'Approved',
                    'route' => 'admin.applications.index',
                    'icon' => 'check-circle',
                    'params' => ['status' => 'approved'],
                ],
                'unassigned' => [
                    'name' => 'Unassigned',
                    'route' => 'admin.applications.index',
                    'icon' => 'user-x',
                    'params' => ['filter' => 'unassigned'],
                ],
            ],
        ],
        'users' => [
            'name' => 'User Management',
            'route' => 'admin.users.index',
            'icon' => 'users',
            'submenu' => [
                'all_users' => [
                    'name' => 'All Users',
                    'route' => 'admin.users.index',
                    'icon' => 'users',
                ],
                'create_user' => [
                    'name' => 'Create User',
                    'route' => 'admin.users.create',
                    'icon' => 'user-plus',
                ],
                'applicants' => [
                    'name' => 'Applicants',
                    'route' => 'admin.users.index',
                    'icon' => 'user',
                    'params' => ['type' => 'applicant'],
                ],
                'consultants' => [
                    'name' => 'Consultants',
                    'route' => 'admin.consultants.index',
                    'icon' => 'user-check',
                ],
            ],
        ],
        'system' => [
            'name' => 'System Config',
            'route' => 'admin.system.settings',
            'icon' => 'settings',
            'submenu' => [
                'settings' => [
                    'name' => 'General Settings',
                    'route' => 'admin.system.settings',
                    'icon' => 'sliders',
                ],
                'document_requirements' => [
                    'name' => 'Document Requirements',
                    'route' => 'admin.system.document-requirements.index',
                    'icon' => 'file-plus',
                ],
                'inspection_checklists' => [
                    'name' => 'Inspection Checklists',
                    'route' => 'admin.system.inspection-checklists.index',
                    'icon' => 'check-square',
                ],
                'email_templates' => [
                    'name' => 'Email Templates',
                    'route' => 'admin.system.email-templates',
                    'icon' => 'mail',
                ],
            ],
        ],
        'reports' => [
            'name' => 'Reports & Analytics',
            'route' => 'admin.reports.index',
            'icon' => 'bar-chart',
            'submenu' => [
                'overview' => [
                    'name' => 'System Overview',
                    'route' => 'admin.reports.overview',
                    'icon' => 'pie-chart',
                ],
                'applications' => [
                    'name' => 'Application Reports',
                    'route' => 'admin.reports.applications',
                    'icon' => 'file-text',
                ],
                'consultants' => [
                    'name' => 'Consultant Performance',
                    'route' => 'admin.reports.consultants',
                    'icon' => 'users',
                ],
                'documents' => [
                    'name' => 'Document Reports',
                    'route' => 'admin.reports.documents',
                    'icon' => 'folder',
                ],
            ],
        ],
        'audit' => [
            'name' => 'Audit Logs',
            'route' => 'admin.audit.index',
            'icon' => 'shield',
        ],
        'bulk_operations' => [
            'name' => 'Bulk Operations',
            'route' => 'admin.import-export.index',
            'icon' => 'download',
            'submenu' => [
                'import_export' => [
                    'name' => 'Import/Export',
                    'route' => 'admin.import-export.index',
                    'icon' => 'upload',
                ],
                'bulk_assign' => [
                    'name' => 'Bulk Assign Consultants',
                    'route' => 'admin.applications.index',
                    'icon' => 'user-plus',
                    'params' => ['view' => 'bulk'],
                ],
            ],
        ],
    ],

    'public' => [
        'home' => [
            'name' => 'Home',
            'route' => 'home',
            'icon' => 'home',
        ],
        'about' => [
            'name' => 'About Us',
            'route' => 'about',
            'icon' => 'info',
        ],
        'services' => [
            'name' => 'Services',
            'route' => 'services', 
            'icon' => 'briefcase',
        ],
        'contact' => [
            'name' => 'Contact',
            'route' => 'contact',
            'icon' => 'phone',
        ],
        'faq' => [
            'name' => 'FAQ',
            'route' => 'faq',
            'icon' => 'help-circle',
        ],
        'divider' => '---',
        'login' => [
            'name' => 'Login',
            'route' => 'login',
            'icon' => 'log-in',
            'guest_only' => true,
        ],
        'register' => [
            'name' => 'Apply Now',
            'route' => 'register',
            'icon' => 'user-plus',
            'guest_only' => true,
            'highlight' => true,
        ],
    ]
];

// Helper function to get navigation for current user
if (!function_exists('getNavigation')) {
    function getNavigation($userType = null)
    {
        $userType = $userType ?? (auth()->check() ? auth()->user()->user_type : 'public');
        $navigation = config('navigation.' . $userType, []);
        
        // Add dynamic badge counts
        if (auth()->check()) {
            $user = auth()->user();
            $counts = getUserNavigationCounts($user);
            
            foreach ($navigation as $key => &$item) {
                if (isset($item['badge']) && isset($counts[$item['badge']])) {
                    $item['badge_count'] = $counts[$item['badge']];
                }
            }
        }
        
        return $navigation;
    }
}

// Helper function to get badge counts
if (!function_exists('getUserNavigationCounts')) {
    function getUserNavigationCounts($user)
    {
        $counts = [];
        
        switch ($user->user_type) {
            case 'applicant':
                $activeApp = $user->getActiveApplication();
                $counts = [
                    'completion_percentage' => $activeApp?->completion_percentage ?? 0,
                    'pending_documents_count' => $activeApp ? $activeApp->getRequiredDocumentsForStage()->count() - $activeApp->documents()->approved()->count() : 0,
                    'upcoming_appointments_count' => $user->appointments()->where('scheduled_at', '>', now())->where('status', '!=', 'cancelled')->count(),
                ];
                break;
                
            case 'consultant':
                $counts = [
                    'active_applications_count' => $user->assignedApplications()->active()->count(),
                    'todays_appointments_count' => $user->consultantAppointments()->whereDate('scheduled_at', today())->count(),
                    'documents_to_review_count' => \App\Models\Document::whereHas('application', function($query) use ($user) {
                        $query->where('consultant_id', $user->id);
                    })->pendingReview()->count(),
                ];
                break;
                
            case 'admin':
                $counts = [
                    'pending_applications_count' => \App\Models\Application::whereIn('status', [
                        'submitted', 'under_review', 'final_review'
                    ])->count(),
                ];
                break;
        }
        
        return $counts;
    }
}