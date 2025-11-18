<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LIMS System Configuration - Laravel 12 Compatible
    |--------------------------------------------------------------------------
    */

    'version' => '2.0.0',
    'name' => 'Laboratory Information Management System',
    'short_name' => 'LIMS',
    'framework' => 'Laravel 12',
    'php_version' => PHP_VERSION,
    'build_tools' => 'Pure PHP - Zero Node.js Dependencies',

    /*
    |--------------------------------------------------------------------------
    | CSS & JavaScript Resources (Pure PHP - No Build Tools Required)
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'css' => [
            'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        ],
        'js' => [
            'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
            'jquery' => 'https://code.jquery.com/jquery-3.7.1.min.js',
            'chartjs' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js',
            'alpinejs' => 'https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js',
            'sweetalert' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11'
        ],
        'integrity' => [
            'bootstrap_css' => 'sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN',
            'bootstrap_js' => 'sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Code Generation Settings
    |--------------------------------------------------------------------------
    */
    'codes' => [
        'public_prefix' => 'UNEJ',           // Public tracking code prefix
        'internal_prefix' => '',             // Internal sample code prefix  
        'year_format' => 'Y',                // Year format (Y = 2025, y = 25)
        'public_format' => 'YYYYMMXXXXXX',   // UNEJ202510XXXXXX (4+4+2+6)
        'internal_format' => 'YYYYMMDDXXXXXX', // 20251010XXXXXX (4+4+2+2+6)
        'revision_suffix' => 'REV',          // Retesting suffix
        'sequence_length' => 6,              // Number of digits for sequence
        'reset_sequence' => 'monthly'        // yearly, monthly, daily
    ],

    /*
    |--------------------------------------------------------------------------
    | Laboratory Information
    |--------------------------------------------------------------------------
    */
    'laboratory' => [
        'name' => 'Laboratorium Analisis Universitas Jember',
        'address' => 'Jl. Kalimantan No. 37, Kampus Tegalboto, Jember',
        'city' => 'Jember',
        'postal_code' => '68121',
        'phone' => '(0331) 334293',
        'email' => 'lab@unej.ac.id',
        'website' => 'https://unej.ac.id',
        'accreditation_number' => 'LP-696-IDN',
        'accreditation_date' => '2024-01-15',
        'iso_certificate' => 'ISO/IEC 17025:2017',
        'established_year' => '2020',
        'logo_path' => 'images/unej-logo.png'
    ],

    /*
    |--------------------------------------------------------------------------
    | System Settings
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'session_timeout' => 30, // minutes
        'max_file_size' => 10240, // KB (10MB)
        'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'xls', 'doc', 'docx', 'zip', 'rar'],
        'certificate_validity_days' => 365,
        'invoice_due_days' => 30,
        'default_tax_rate' => 11, // percentage
        'analyst_max_workload' => 10,
        'backup_retention_days' => 90,
        'file_cleanup_days' => 30,
        'auto_assignment' => true, // Automatic sample assignment
        'require_approval' => false, // Manual approval for registrations
        'enable_retesting' => true,
        'max_retesting_attempts' => 3
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel 12 Specific Configurations
    |--------------------------------------------------------------------------
    */
    'laravel12' => [
        'health_check_enabled' => true,
        'health_check_route' => '/up',
        'custom_health_route' => '/api/health',
        'middleware_optimization' => true,
        'route_caching' => env('ROUTE_CACHE_ENABLED', true),
        'view_caching' => env('VIEW_CACHE_ENABLED', true),
        'config_caching' => env('CONFIG_CACHE_ENABLED', true),
        'event_caching' => env('EVENT_CACHE_ENABLED', true),
        'bootstrap_performance' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Pure PHP Development (No Node.js Required)
    |--------------------------------------------------------------------------
    */
    'development' => [
        'no_nodejs_required' => true,
        'no_npm_required' => true,
        'no_webpack_required' => true,
        'no_vite_required' => true,
        'pure_php_solution' => true,
        'cdn_assets_only' => true,
        'inline_styles_allowed' => true,
        'vanilla_js_preferred' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Configuration
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'ADMIN' => [
            'name' => 'Administrasi (Customer Service)',
            'permissions' => [1, 2, 6, 7, 8, 9],
            'description' => 'Customer service and administrative tasks',
            'color' => 'primary'
        ],
        'SUPERVISOR' => [
            'name' => 'Laboratory Supervisor (Management Access)',
            'permissions' => 'all',
            'description' => 'Full laboratory management access',
            'color' => 'danger'
        ],
        'SUPERVISOR_ANALYST' => [
            'name' => 'Senior Analyst (Supervision + Testing)',
            'permissions' => [1, 3, 4, 5, 8, 9],
            'description' => 'Senior analyst with supervision capabilities',
            'color' => 'warning'
        ],
        'ANALYST' => [
            'name' => 'Laboratory Technician (Testing Only)',
            'permissions' => [4],
            'description' => 'Laboratory testing and result entry',
            'color' => 'info'
        ],
        'TECH_AUDITOR' => [
            'name' => 'Technical Coordinator (QC Access)',
            'permissions' => [5],
            'description' => 'Technical review and quality control',
            'color' => 'success'
        ],
        'QUALITY_AUDITOR' => [
            'name' => 'Quality Coordinator (QA Access)',
            'permissions' => [5],
            'description' => 'Quality assurance and final validation',  
            'color' => 'success'
        ],
        'DEVEL' => [
            'name' => 'Developer Full Access',
            'permissions' => 'all',
            'description' => 'System development and maintenance',
            'color' => 'dark'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Permissions
    |--------------------------------------------------------------------------
    */
    'modules' => [
        1 => 'Daftar Sampel Baru',
        2 => 'Kodifikasi Barang Uji',
        3 => 'Penugasan Analis',
        4 => 'Pencatatan Hasil',
        5 => 'Review & Validasi',
        6 => 'Penerbitan Sertifikat',
        7 => 'Pelanggan & Invoice',
        8 => 'Manajemen Parameter',
        9 => 'Manajemen User'
    ],

    /*
    |--------------------------------------------------------------------------
    | Sample Status Flow
    |--------------------------------------------------------------------------
    */
    'sample_status' => [
        'pending' => [
            'name' => 'Menunggu',
            'color' => 'warning'
        ],
        'registered' => [
            'name' => 'Terdaftar',
            'color' => 'primary'
        ],
        'assigned' => [
            'name' => 'Ditugaskan',
            'color' => 'info'
        ],
        'testing' => [
            'name' => 'Dalam Pengujian',
            'color' => 'info'
        ],
        'review' => [
            'name' => 'Review',
            'color' => 'warning'
        ],
        'completed' => [
            'name' => 'Selesai',
            'color' => 'success'
        ],
        'rejected' => [
            'name' => 'Ditolak',
            'color' => 'danger'
        ],
        'archived' => [
            'name' => 'Arsip',
            'color' => 'secondary'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'whatsapp_enabled' => true,
        'notify_customer_on_completion' => true,
        'notify_staff_on_assignment' => true,
        'reminder_days_before_due' => 3,
        'whatsapp_api_url' => env('WHATSAPP_API_URL'),
        'whatsapp_api_key' => env('WHATSAPP_API_KEY')
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'max_login_attempts' => 3,
        'lockout_duration' => 30, // minutes
        'password_min_length' => 6,
        'require_password_change_days' => 90,
        'audit_retention_days' => 2555, // 7 years
        'session_encryption' => true,
        'csrf_protection' => true,
        'xss_protection' => true,
        'sql_injection_protection' => true,
        'file_upload_scanning' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificate Templates
    |--------------------------------------------------------------------------
    */
    'certificate_templates' => [
        'standard' => [
            'name' => 'Sertifikat Standar',
            'template_file' => 'certificates/standard.blade.php'
        ],
        'water_quality' => [
            'name' => 'Sertifikat Kualitas Air',
            'template_file' => 'certificates/water-quality.blade.php'
        ],
        'soil_analysis' => [
            'name' => 'Sertifikat Analisis Tanah',
            'template_file' => 'certificates/soil-analysis.blade.php'
        ],
        'food_safety' => [
            'name' => 'Sertifikat Keamanan Pangan',
            'template_file' => 'certificates/food-safety.blade.php'
        ],
        'fertilizer' => [
            'name' => 'Sertifikat Analisis Pupuk',
            'template_file' => 'certificates/fertilizer.blade.php'
        ],
        'environment' => [
            'name' => 'Sertifikat Analisis Lingkungan',
            'template_file' => 'certificates/environment.blade.php'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Matrix Configuration
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'roles' => [
            'SUPERVISOR' => [
                'name' => 'Kepala Laboratorium',
                'level' => 1,
                'color' => 'danger',
                'modules' => 'all'
            ],
            'TECH_AUDITOR' => [
                'name' => 'Koordinator Teknis', 
                'level' => 2,
                'color' => 'warning',
                'modules' => [1,3,5,8,9]
            ],
            'QUALITY_AUDITOR' => [
                'name' => 'Koordinator Mutu',
                'level' => 2, 
                'color' => 'info',
                'modules' => [1,3,5,8,9]
            ],
            'SUPERVISOR_ANALYST' => [
                'name' => 'Penyelia',
                'level' => 3,
                'color' => 'success', 
                'modules' => [1,3,5,8,9]
            ],
            'ANALYST' => [
                'name' => 'Teknisi Laboratorium',
                'level' => 4,
                'color' => 'primary',
                'modules' => [4]
            ],
            'ADMIN' => [
                'name' => 'Customer Service',
                'level' => 3,
                'color' => 'secondary',
                'modules' => [1,2,6,7,8,9]
            ],
            'DEVEL' => [
                'name' => 'Developer',
                'level' => 0,
                'color' => 'dark',
                'modules' => 'all'
            ]
        ],

        'permission_matrix' => [
            // Module ID => [Roles that have access]
            1 => ['ADMIN', 'SUPERVISOR', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'SUPERVISOR_ANALYST', 'DEVEL'],      // List New Sample
            2 => ['ADMIN', 'DEVEL'],                                                                               // Codification
            3 => ['SUPERVISOR', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'SUPERVISOR_ANALYST', 'DEVEL'],              // Assignment
            4 => ['ANALYST', 'SUPERVISOR_ANALYST', 'DEVEL'],                                                      // Testing
            5 => ['SUPERVISOR', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'SUPERVISOR_ANALYST', 'DEVEL'],              // Review
            6 => ['ADMIN', 'SUPERVISOR', 'DEVEL'],                                                                // Certificates
            7 => ['ADMIN', 'SUPERVISOR', 'DEVEL'],                                                                // Invoice
            8 => ['SUPERVISOR', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'SUPERVISOR_ANALYST', 'DEVEL'],              // Parameter
            9 => ['SUPERVISOR', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'SUPERVISOR_ANALYST', 'ADMIN', 'DEVEL'],     // User Management
            10 => ['DEVEL']                                                                                       // System Settings
        ]
    ]
];