-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Nov 2025 pada 08.16
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lims_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(191) NOT NULL,
  `model_type` varchar(191) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'User login successful', '2025-10-13 17:52:17', '2025-10-13 17:52:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `certificates`
--

CREATE TABLE `certificates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `certificate_number` varchar(191) NOT NULL,
  `sample_id` bigint(20) UNSIGNED NOT NULL,
  `template_type` varchar(191) NOT NULL DEFAULT 'standard',
  `certificate_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`certificate_data`)),
  `file_path` varchar(191) DEFAULT NULL,
  `status` enum('draft','issued','cancelled','expired') NOT NULL,
  `issued_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `digital_signature` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `code_sequences`
--

CREATE TABLE `code_sequences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(20) NOT NULL,
  `reset_date` date NOT NULL,
  `current_number` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `configurations`
--

CREATE TABLE `configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `type` enum('text','number','boolean','email','url','json') NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_person` varchar(191) NOT NULL,
  `company_name` varchar(191) DEFAULT NULL,
  `whatsapp_number` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `city` varchar(191) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(191) NOT NULL,
  `sample_request_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('draft','sent','paid','overdue','cancelled') NOT NULL,
  `issued_at` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000000_create_parameters_table', 1),
(5, '2024_01_01_000001_create_users_table', 1),
(6, '2024_01_01_000002_add_username_to_users_table', 1),
(7, '2024_01_01_000002_create_customers_table', 1),
(8, '2024_01_01_000003_create_sample_types_table', 1),
(9, '2024_01_01_000003_make_name_nullable_in_users_table', 1),
(10, '2024_01_01_000004_create_test_parameters_table', 1),
(11, '2024_01_01_000005_create_sample_requests_table', 1),
(12, '2024_01_01_000006_create_samples_table', 1),
(13, '2024_01_01_000007_create_sample_tests_table', 1),
(14, '2024_01_01_000008_create_certificates_table', 1),
(15, '2024_01_01_000009_create_invoices_table', 1),
(16, '2024_01_01_000010_create_audit_logs_table', 1),
(17, '2024_01_01_000011_create_sample_type_parameters_table', 1),
(18, '2024_01_02_000000_create_sample_types_table', 1),
(19, '2024_01_02_000001_create_parameter_sample_type_pivot_table', 1),
(20, '2024_01_02_000002_create_audit_logs_table', 1),
(21, '2024_01_02_000003_add_category_to_sample_types_table', 1),
(22, '2024_01_02_000004_add_missing_columns_to_parameters_table', 1),
(23, '2024_12_10_000001_create_code_sequences_table', 1),
(24, '2024_12_10_000005_create_sample_request_parameters_table', 1),
(25, '2024_12_10_000009_create_sample_results_table', 1),
(26, '2024_12_10_000011_create_sample_reviews_table', 1),
(27, '2024_12_10_000012_create_audit_logs_table', 1),
(28, '2024_12_10_000013_add_lims_fields_to_existing_tables', 1),
(29, '2024_12_10_000014_cleanup_migration_conflicts', 1),
(30, '2024_01_02_000005_update_parameters_value_columns', 2),
(31, '2024_01_02_000010_create_configurations_table', 3),
(32, '2024_01_02_000020_create_roles_table', 3),
(33, '2024_01_02_000021_create_modules_table', 3),
(34, '2024_01_02_000030_add_columns_to_users_table', 4),
(35, '2024_01_02_000031_fix_users_table_username_column', 5),
(36, '2024_01_02_000032_fix_users_table_full_name_column', 6),
(37, '2024_01_02_000033_add_security_columns_to_users_table', 7),
(38, '2024_01_02_000040_add_assignment_columns_to_samples_table', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `route_name` varchar(191) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `modules`
--

INSERT INTO `modules` (`id`, `name`, `code`, `description`, `icon`, `route_name`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'List New Sample', 'sample_list', 'View and manage new sample requests', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(2, 'Codification', 'codification', 'Sample codification process', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(3, 'Assignment', 'assignment', 'Assign samples to analysts', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(4, 'Testing', 'testing', 'Laboratory testing process', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(5, 'Review', 'review', 'Review test results', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(6, 'Certificates', 'certificates', 'Generate and manage certificates', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(7, 'Invoice', 'invoice', 'Billing and invoicing', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(8, 'Parameter', 'parameter', 'Manage test parameters', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(9, 'User Management', 'user_management', 'Manage system users', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(10, 'System Settings', 'system_settings', 'System configuration', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(11, 'Archives', 'archives', 'View and manage archive of sample', NULL, NULL, 0, 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `parameters`
--

CREATE TABLE `parameters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `category` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `min_value` decimal(15,4) DEFAULT NULL,
  `max_value` decimal(15,4) DEFAULT NULL,
  `data_type` varchar(191) NOT NULL DEFAULT 'numeric',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `parameters`
--

INSERT INTO `parameters` (`id`, `name`, `code`, `category`, `description`, `unit`, `min_value`, `max_value`, `data_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Kekeruhan', 'TURBIDITY', 'Fisik', 'Tingkat kekeruhan air', 'NTU', 0.0000, 1000.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(2, 'Warna', 'COLOR', 'Fisik', 'Tingkat warna air', 'TCU', 0.0000, 500.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(3, 'Total Suspended Solid (TSS)', 'TSS', 'Fisik', 'Padatan tersuspensi total', 'mg/L', 0.0000, 10000.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(4, 'pH', 'PH', 'Kimia', 'Derajat keasaman', 'unit pH', 0.0000, 14.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(5, 'Dissolved Oxygen (DO)', 'DO', 'Kimia', 'Oksigen terlarut', 'mg/L', 0.0000, 20.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(6, 'Biochemical Oxygen Demand (BOD)', 'BOD', 'Kimia', 'Kebutuhan oksigen biokimia', 'mg/L', 0.0000, 1000.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(7, 'Chemical Oxygen Demand (COD)', 'COD', 'Kimia', 'Kebutuhan oksigen kimia', 'mg/L', 0.0000, 2000.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(8, 'Ammonia (NH3-N)', 'AMMONIA', 'Kimia', 'Kandungan ammonia', 'mg/L', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(9, 'Nitrat (NO3-N)', 'NITRAT', 'Kimia', 'Kandungan nitrat', 'mg/L', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(10, 'Klorin Bebas', 'FREE_CHLORINE', 'Kimia', 'Sisa klorin bebas', 'mg/L', 0.0000, 10.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(11, 'Salmonella', 'SALMONELLA', 'Mikrobiologi', 'Bakteri Salmonella', 'CFU/g', NULL, NULL, 'text', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(12, 'Protein', 'PROTEIN', 'Nutrisi', 'Kandungan protein', '%', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(13, 'Lemak', 'FAT', 'Nutrisi', 'Kandungan lemak', '%', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:41', '2025-10-13 17:12:41'),
(14, 'Karbohidrat', 'CARBOHYDRATE', 'Nutrisi', 'Kandungan karbohidrat', '%', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:42', '2025-10-13 17:12:42'),
(15, 'Kadar Air', 'MOISTURE', 'Fisik', 'Kandungan air', '%', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:42', '2025-10-13 17:12:42'),
(16, 'Timbal (Pb)', 'LEAD', 'Logam Berat', 'Kandungan timbal', 'mg/L', 0.0000, 100.0000, 'numeric', 1, '2025-10-13 17:12:42', '2025-10-13 17:12:42'),
(17, 'Merkuri (Hg)', 'MERCURY', 'Logam Berat', 'Kandungan merkuri', 'mg/L', 0.0000, 10.0000, 'numeric', 1, '2025-10-13 17:12:42', '2025-10-13 17:12:42'),
(18, 'Kadmium (Cd)', 'CADMIUM', 'Logam Berat', 'Kandungan kadmium', 'mg/L', 0.0000, 10.0000, 'numeric', 1, '2025-10-13 17:12:42', '2025-10-13 17:12:42'),
(19, 'Escherichia Coli', 'E_COLI', 'Mikrobiologi', 'Bakteri E. Coli', 'CFU/100mL', 0.0000, 100000.0000, 'numeric', 1, '2025-10-13 17:15:05', '2025-10-13 17:15:05'),
(20, 'Coliform Total', 'TOTAL_COLIFORM', 'Mikrobiologi', 'Total bakteri coliform', 'CFU/100mL', 0.0000, 100000.0000, 'numeric', 1, '2025-10-13 17:15:05', '2025-10-13 17:15:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `parameter_sample_type`
--

CREATE TABLE `parameter_sample_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parameter_id` bigint(20) UNSIGNED NOT NULL,
  `sample_type_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `parameter_sample_type`
--

INSERT INTO `parameter_sample_type` (`id`, `parameter_id`, `sample_type_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 2, NULL, NULL),
(3, 1, 3, NULL, NULL),
(4, 1, 4, NULL, NULL),
(5, 2, 1, NULL, NULL),
(6, 2, 2, NULL, NULL),
(7, 2, 3, NULL, NULL),
(8, 3, 2, NULL, NULL),
(9, 3, 3, NULL, NULL),
(10, 4, 1, NULL, NULL),
(11, 4, 2, NULL, NULL),
(12, 4, 3, NULL, NULL),
(13, 4, 4, NULL, NULL),
(14, 5, 2, NULL, NULL),
(15, 5, 3, NULL, NULL),
(16, 6, 2, NULL, NULL),
(17, 7, 2, NULL, NULL),
(18, 8, 1, NULL, NULL),
(19, 8, 2, NULL, NULL),
(20, 8, 3, NULL, NULL),
(21, 9, 1, NULL, NULL),
(22, 9, 2, NULL, NULL),
(23, 9, 3, NULL, NULL),
(24, 10, 4, NULL, NULL),
(25, 10, 1, NULL, NULL),
(26, 11, 5, NULL, NULL),
(27, 11, 7, NULL, NULL),
(28, 11, 8, NULL, NULL),
(29, 12, 5, NULL, NULL),
(30, 12, 7, NULL, NULL),
(31, 12, 8, NULL, NULL),
(32, 13, 5, NULL, NULL),
(33, 13, 7, NULL, NULL),
(34, 13, 8, NULL, NULL),
(35, 14, 5, NULL, NULL),
(36, 15, 9, NULL, NULL),
(37, 15, 5, NULL, NULL),
(38, 16, 1, NULL, NULL),
(39, 16, 2, NULL, NULL),
(40, 16, 9, NULL, NULL),
(41, 17, 1, NULL, NULL),
(42, 17, 2, NULL, NULL),
(43, 17, 9, NULL, NULL),
(44, 18, 1, NULL, NULL),
(45, 18, 2, NULL, NULL),
(46, 18, 9, NULL, NULL),
(47, 19, 1, NULL, NULL),
(48, 19, 2, NULL, NULL),
(49, 19, 4, NULL, NULL),
(50, 20, 1, NULL, NULL),
(51, 20, 2, NULL, NULL),
(52, 20, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 99,
  `permissions` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `description`, `level`, `permissions`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SUPERVISOR', 'Kepala Laboratorium', 'Full access to all laboratory operations', 1, 'all', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(2, 'TECH_AUDITOR', 'Koordinator Teknis', 'Technical review and coordination', 2, '1,3,5,8,9', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(3, 'QUALITY_AUDITOR', 'Koordinator Mutu', 'Quality review and assurance', 2, '1,3,5,8,9', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(4, 'SUPERVISOR_ANALYST', 'Penyelia', 'Supervision and senior analyst duties', 3, '1,3,5,8,9', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(5, 'ANALYST', 'Teknisi Laboratorium', 'Laboratory testing and analysis', 4, '4', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(6, 'ADMIN', 'Customer Service', 'Administrative and customer service', 3, '1,2,6,7,8,9', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34'),
(7, 'DEVEL', 'Developer', 'System developer with full access', 0, 'all', 1, '2025-10-13 17:33:34', '2025-10-13 17:33:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `samples`
--

CREATE TABLE `samples` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_request_id` bigint(20) UNSIGNED NOT NULL,
  `tracking_code` varchar(20) NOT NULL,
  `sample_code` varchar(191) NOT NULL,
  `customer_name` varchar(191) NOT NULL,
  `company_name` varchar(191) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `sample_type_id` bigint(20) UNSIGNED NOT NULL,
  `custom_sample_type` varchar(191) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `customer_requirements` text DEFAULT NULL,
  `status` enum('pending','registered','codified','assigned','testing','review_tech','review_quality','validated','certificated','completed','archived','retesting') NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_analyst_id` bigint(20) UNSIGNED DEFAULT NULL,
  `registered_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `codified_at` timestamp NULL DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `testing_started_at` timestamp NULL DEFAULT NULL,
  `testing_completed_at` timestamp NULL DEFAULT NULL,
  `tech_reviewed_at` timestamp NULL DEFAULT NULL,
  `quality_reviewed_at` timestamp NULL DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `certificated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `codified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `testing_started_by` bigint(20) UNSIGNED DEFAULT NULL,
  `testing_completed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `tech_reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `quality_reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `codification_notes` text DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `testing_notes` text DEFAULT NULL,
  `certificate_required` tinyint(1) NOT NULL DEFAULT 1,
  `retesting_reason` text DEFAULT NULL,
  `retesting_required` text DEFAULT NULL,
  `parent_sample_id` bigint(20) UNSIGNED DEFAULT NULL,
  `archived_reason` varchar(191) DEFAULT NULL,
  `revision_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`revision_history`)),
  `revision_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_files`
--

CREATE TABLE `sample_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_parameter_requests`
--

CREATE TABLE `sample_parameter_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_request_id` bigint(20) UNSIGNED NOT NULL,
  `test_parameter_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_additional` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_requests`
--

CREATE TABLE `sample_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tracking_code` varchar(191) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','registered','verified','assigned','testing','review','validated','certificated','completed','archived') NOT NULL,
  `customer_requirements` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `registered_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `feedback_completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_request_parameters`
--

CREATE TABLE `sample_request_parameters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_request_id` bigint(20) UNSIGNED NOT NULL,
  `test_parameter_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_reviews`
--

CREATE TABLE `sample_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `review_type` enum('technical','quality') NOT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `notes` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `correction_required` text DEFAULT NULL,
  `reviewed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_tests`
--

CREATE TABLE `sample_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_id` bigint(20) UNSIGNED NOT NULL,
  `test_parameter_id` bigint(20) UNSIGNED NOT NULL,
  `result_value` varchar(191) DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `method` varchar(191) DEFAULT NULL,
  `status` enum('pending','testing','completed','review_tech','review_quality','validated','rejected') NOT NULL,
  `notes` text DEFAULT NULL,
  `instrument_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`instrument_files`)),
  `tested_at` timestamp NULL DEFAULT NULL,
  `tested_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_by_tech` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_by_quality` bigint(20) UNSIGNED DEFAULT NULL,
  `tech_review_at` timestamp NULL DEFAULT NULL,
  `quality_review_at` timestamp NULL DEFAULT NULL,
  `tech_review_notes` text DEFAULT NULL,
  `quality_review_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_types`
--

CREATE TABLE `sample_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `required_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_parameters`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sample_types`
--

INSERT INTO `sample_types` (`id`, `name`, `code`, `description`, `category`, `is_active`, `required_parameters`, `created_at`, `updated_at`) VALUES
(1, 'Air Minum', 'AIR_MINUM', 'Sampel air untuk konsumsi manusia', 'Air dan Lingkungan', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(2, 'Air Limbah', 'AIR_LIMBAH', 'Sampel air limbah industri atau domestik', 'Air dan Lingkungan', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(3, 'Air Tanah', 'AIR_TANAH', 'Sampel air dari sumur atau sumber air tanah', 'Air dan Lingkungan', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(4, 'Air Kolam Renang', 'AIR_KOLAM', 'Sampel air untuk kolam renang dan rekreasi', 'Air dan Lingkungan', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(5, 'Makanan Olahan', 'MAKANAN_OLAHAN', 'Produk makanan yang telah diproses', 'Makanan dan Minuman', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(6, 'Minuman Kemasan', 'MINUMAN_KEMASAN', 'Minuman dalam kemasan siap konsumsi', 'Makanan dan Minuman', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(7, 'Produk Susu', 'PRODUK_SUSU', 'Susu dan produk turunannya', 'Makanan dan Minuman', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(8, 'Daging dan Hasil Ternak', 'DAGING_TERNAK', 'Daging segar dan produk olahannya', 'Makanan dan Minuman', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(9, 'Kosmetik', 'KOSMETIK', 'Produk perawatan kulit dan kecantikan', 'Kosmetik dan Farmasi', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(10, 'Obat-obatan', 'OBAT_OBATAN', 'Produk farmasi dan suplemen', 'Kosmetik dan Farmasi', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(11, 'Bahan Kimia', 'BAHAN_KIMIA', 'Bahan kimia industri dan laboratorium', 'Kimia dan Material', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37'),
(12, 'Material Konstruksi', 'MATERIAL_KONSTRUKSI', 'Bahan bangunan dan konstruksi', 'Kimia dan Material', 1, NULL, '2025-10-13 17:12:37', '2025-10-13 17:12:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sample_type_parameters`
--

CREATE TABLE `sample_type_parameters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sample_type_id` bigint(20) UNSIGNED NOT NULL,
  `test_parameter_id` bigint(20) UNSIGNED NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `default_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('P2n6V7bPX42Oc8L7RLXiGNes8j3KNBhktZt4hkTx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ1d4VzdUelM5S3J3bmI3UVJQQTgzeTR5dGRhV3lpbGpFYzA4ZVZpcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762864909),
('pZHeHrkJvmGHMlrFeOXsghKblJQtPlJrczW6C8Vj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYWUwalF4bklQUVRTcWREblVJWFNlR0FNUWdIUWFQMHpmYlhVUnJLOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762873056);

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 'lab_name', 'Laboratorium Pengujian', 'Nama Laboratorium', 1, '2025-10-25 19:01:57', '2025-10-25 19:06:06'),
(2, 'lab_address', 'Jl. Contoh No. 123, Kota, Provinsi 12345', 'Alamat Laboratorium', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(3, 'lab_phone', '(021) 1234567', 'Telepon Laboratorium', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(4, 'lab_email', 'info@lab.com', 'Email Laboratorium', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(5, 'lab_accreditation', 'KAN-LP-123-IDN', 'Nomor Akreditasi', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(6, 'certificate_validity_days', '365', 'Masa berlaku sertifikat (hari)', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(7, 'invoice_due_days', '30', 'Jatuh tempo invoice (hari)', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(8, 'session_timeout', '1800', 'Session timeout (detik)', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(9, 'max_file_size', '10485760', 'Ukuran file maksimal (bytes)', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06'),
(10, 'backup_retention_days', '30', 'Retensi backup (hari)', NULL, '2025-09-24 14:30:15', '2025-10-25 19:06:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_parameters`
--

CREATE TABLE `test_parameters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `category` varchar(191) NOT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `method` varchar(191) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `specialist_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specialist_roles`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(191) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `full_name` varchar(191) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('ADMIN','SUPERVISOR','SUPERVISOR_ANALYST','ANALYST','TECH_AUDITOR','QUALITY_AUDITOR','DEVEL') NOT NULL,
  `department` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) DEFAULT NULL,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `full_name`, `email_verified_at`, `password`, `role`, `department`, `phone`, `permissions`, `is_active`, `last_login_at`, `locked_at`, `last_login_ip`, `failed_login_attempts`, `locked_until`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'pandu', 'Pandu', 'pandu@lims.com', 'Pandu Developer', '2025-10-13 17:44:03', '$2y$12$kU0L0J1PComD4GDcfKSqh.SHldkzRlwWWICL0u6kYT92Dfa3SqIge', 'DEVEL', 'IT Development', '081234567890', NULL, 1, '2025-11-07 02:02:43', NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-11-07 02:02:43'),
(2, 'trihandoyo', 'Tri Handoyo', 'trihandoyo@lims.com', 'Tri Handoyo, S.T., M.Sc.', '2025-10-13 17:44:03', '$2y$12$F0wcPuAeQuaAf4oDQLJTdeUaP27plxn7.Up1jgSCnqRXQcqJMHfZG', 'SUPERVISOR', 'Laboratory Management', '081234567891', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(3, 'dinda', 'Dinda', 'dinda@lims.com', 'Dinda Sari', '2025-10-13 17:44:04', '$2y$12$YbBxiuwhoHpM7YtTnDf8feLURqqzbBLGW7SYyiJsg5hdm7MkW6HEK', 'ADMIN', 'Customer Service', '081234567892', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(4, 'arin', 'Arin', 'arin@lims.com', 'Arin Supervisor', '2025-10-13 17:44:05', '$2y$12$vXnwsFJNdqy6.3ONo7zcsuIgd/V0TRPLACdB2n9AHKzv8gOAqVhk.', 'SUPERVISOR_ANALYST', 'Laboratory Analysis', '081234567893', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(5, 'aryo', 'Aryo', 'aryo@lims.com', 'Aryo Technician', '2025-10-13 17:44:05', '$2y$12$RMEPF6j0.78LzZk/nGrFfu5QYOtgMHTzGEbltx5UPVDh2RdZo3fL2', 'ANALYST', 'Laboratory Testing', '081234567894', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(6, 'purwo', 'Purwo', 'purwo@lims.com', 'Purwo Analyst', '2025-10-13 17:44:06', '$2y$12$5cr1cls1f2ZClhVc7quNrunJEgBI2oEmR/UnqJb0k.FJgrnbOxlF.', 'ANALYST', 'Laboratory Testing', '081234567895', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(7, 'rizki', 'Rizki', 'rizki@lims.com', 'Rizki Laboratory', '2025-10-13 17:44:06', '$2y$12$VArcd/RziF99rn/s3MstmepzjrwEVtSVzbri6M9G3vu57Ex6sOUsK', 'ANALYST', 'Laboratory Testing', '081234567896', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(8, 'wulan', 'Wulan', 'wulan@lims.com', 'Wulan Technician', '2025-10-13 17:44:07', '$2y$12$piI0Xx.zVwmS32hpDidEkOFieSrhwu/oaUrbobpHWkAlI6h1zlATO', 'ANALYST', 'Laboratory Testing', '081234567897', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(9, 'fafan', 'Fafan', 'fafan@lims.com', 'Fafan Technical Coordinator', '2025-10-13 17:44:07', '$2y$12$HjQ623jDAEY9gfK879qqH.g0SV4czoGRqpltkashd8zLGrEXzcTcy', 'TECH_AUDITOR', 'Technical Coordination', '081234567898', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08'),
(10, 'parawita', 'Parawita', 'parawita@lims.com', 'Parawita Quality Coordinator', '2025-10-13 17:44:08', '$2y$12$at31E4.qfgBSGeWJZyE7EOVxgTqvd6qBde5Iw72s/xqzIXentEn0y', 'QUALITY_AUDITOR', 'Quality Assurance', '081234567899', NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, '2025-10-13 17:44:08', '2025-10-13 17:44:08');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificates_certificate_number_unique` (`certificate_number`),
  ADD KEY `certificates_sample_id_foreign` (`sample_id`),
  ADD KEY `certificates_issued_by_foreign` (`issued_by`);

--
-- Indeks untuk tabel `code_sequences`
--
ALTER TABLE `code_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_sequences_type_reset_date_unique` (`type`,`reset_date`),
  ADD KEY `code_sequences_type_reset_date_index` (`type`,`reset_date`);

--
-- Indeks untuk tabel `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `configurations_key_unique` (`key`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_sample_request_id_foreign` (`sample_request_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_code_unique` (`code`);

--
-- Indeks untuk tabel `parameters`
--
ALTER TABLE `parameters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parameters_code_unique` (`code`);

--
-- Indeks untuk tabel `parameter_sample_type`
--
ALTER TABLE `parameter_sample_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parameter_sample_type_parameter_id_sample_type_id_unique` (`parameter_id`,`sample_type_id`),
  ADD KEY `parameter_sample_type_sample_type_id_foreign` (`sample_type_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_code_unique` (`code`);

--
-- Indeks untuk tabel `samples`
--
ALTER TABLE `samples`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `samples_sample_code_unique` (`sample_code`),
  ADD KEY `samples_sample_request_id_foreign` (`sample_request_id`),
  ADD KEY `samples_sample_type_id_foreign` (`sample_type_id`),
  ADD KEY `samples_registered_by_foreign` (`registered_by`),
  ADD KEY `samples_codified_by_foreign` (`codified_by`),
  ADD KEY `samples_testing_started_by_foreign` (`testing_started_by`),
  ADD KEY `samples_testing_completed_by_foreign` (`testing_completed_by`),
  ADD KEY `samples_tech_reviewed_by_foreign` (`tech_reviewed_by`),
  ADD KEY `samples_quality_reviewed_by_foreign` (`quality_reviewed_by`),
  ADD KEY `samples_parent_sample_id_foreign` (`parent_sample_id`),
  ADD KEY `samples_status_created_at_index` (`status`,`created_at`),
  ADD KEY `samples_tracking_code_sample_code_index` (`tracking_code`,`sample_code`),
  ADD KEY `samples_assigned_analyst_id_status_index` (`assigned_analyst_id`,`status`),
  ADD KEY `samples_tracking_code_index` (`tracking_code`),
  ADD KEY `samples_sample_code_index` (`sample_code`),
  ADD KEY `samples_assigned_to_foreign` (`assigned_to`),
  ADD KEY `samples_assigned_by_foreign` (`assigned_by`);

--
-- Indeks untuk tabel `sample_files`
--
ALTER TABLE `sample_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sample_files_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `sample_files_sample_id_uploaded_at_index` (`sample_id`,`uploaded_at`);

--
-- Indeks untuk tabel `sample_parameter_requests`
--
ALTER TABLE `sample_parameter_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sample_param_req_unique` (`sample_request_id`,`test_parameter_id`),
  ADD KEY `sample_parameter_requests_test_parameter_id_foreign` (`test_parameter_id`);

--
-- Indeks untuk tabel `sample_requests`
--
ALTER TABLE `sample_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sample_requests_tracking_code_unique` (`tracking_code`),
  ADD KEY `sample_requests_customer_id_foreign` (`customer_id`),
  ADD KEY `sample_requests_registered_by_foreign` (`registered_by`),
  ADD KEY `sample_requests_tracking_code_index` (`tracking_code`),
  ADD KEY `sample_requests_status_created_at_index` (`status`,`created_at`);

--
-- Indeks untuk tabel `sample_request_parameters`
--
ALTER TABLE `sample_request_parameters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sample_request_parameter_unique` (`sample_request_id`,`test_parameter_id`),
  ADD KEY `sample_request_parameters_test_parameter_id_foreign` (`test_parameter_id`);

--
-- Indeks untuk tabel `sample_reviews`
--
ALTER TABLE `sample_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sample_reviews_sample_id_review_type_index` (`sample_id`,`review_type`),
  ADD KEY `sample_reviews_reviewer_id_reviewed_at_index` (`reviewer_id`,`reviewed_at`);

--
-- Indeks untuk tabel `sample_tests`
--
ALTER TABLE `sample_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sample_tests_sample_id_foreign` (`sample_id`),
  ADD KEY `sample_tests_test_parameter_id_foreign` (`test_parameter_id`),
  ADD KEY `sample_tests_tested_by_foreign` (`tested_by`),
  ADD KEY `sample_tests_reviewed_by_tech_foreign` (`reviewed_by_tech`),
  ADD KEY `sample_tests_reviewed_by_quality_foreign` (`reviewed_by_quality`);

--
-- Indeks untuk tabel `sample_types`
--
ALTER TABLE `sample_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sample_types_code_unique` (`code`);

--
-- Indeks untuk tabel `sample_type_parameters`
--
ALTER TABLE `sample_type_parameters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sample_type_parameters_sample_type_id_test_parameter_id_unique` (`sample_type_id`,`test_parameter_id`),
  ADD KEY `sample_type_parameters_test_parameter_id_foreign` (`test_parameter_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indeks untuk tabel `test_parameters`
--
ALTER TABLE `test_parameters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `test_parameters_code_unique` (`code`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `code_sequences`
--
ALTER TABLE `code_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `configurations`
--
ALTER TABLE `configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `parameters`
--
ALTER TABLE `parameters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `parameter_sample_type`
--
ALTER TABLE `parameter_sample_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `samples`
--
ALTER TABLE `samples`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_files`
--
ALTER TABLE `sample_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_parameter_requests`
--
ALTER TABLE `sample_parameter_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_requests`
--
ALTER TABLE `sample_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_request_parameters`
--
ALTER TABLE `sample_request_parameters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_reviews`
--
ALTER TABLE `sample_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_tests`
--
ALTER TABLE `sample_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sample_types`
--
ALTER TABLE `sample_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `sample_type_parameters`
--
ALTER TABLE `sample_type_parameters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `test_parameters`
--
ALTER TABLE `test_parameters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `certificates_sample_id_foreign` FOREIGN KEY (`sample_id`) REFERENCES `samples` (`id`);

--
-- Ketidakleluasaan untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_sample_request_id_foreign` FOREIGN KEY (`sample_request_id`) REFERENCES `sample_requests` (`id`);

--
-- Ketidakleluasaan untuk tabel `parameter_sample_type`
--
ALTER TABLE `parameter_sample_type`
  ADD CONSTRAINT `parameter_sample_type_parameter_id_foreign` FOREIGN KEY (`parameter_id`) REFERENCES `parameters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parameter_sample_type_sample_type_id_foreign` FOREIGN KEY (`sample_type_id`) REFERENCES `sample_types` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `samples`
--
ALTER TABLE `samples`
  ADD CONSTRAINT `samples_assigned_analyst_id_foreign` FOREIGN KEY (`assigned_analyst_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `samples_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `samples_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `samples_codified_by_foreign` FOREIGN KEY (`codified_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `samples_parent_sample_id_foreign` FOREIGN KEY (`parent_sample_id`) REFERENCES `samples` (`id`),
  ADD CONSTRAINT `samples_quality_reviewed_by_foreign` FOREIGN KEY (`quality_reviewed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `samples_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `samples_sample_request_id_foreign` FOREIGN KEY (`sample_request_id`) REFERENCES `sample_requests` (`id`),
  ADD CONSTRAINT `samples_sample_type_id_foreign` FOREIGN KEY (`sample_type_id`) REFERENCES `sample_types` (`id`),
  ADD CONSTRAINT `samples_tech_reviewed_by_foreign` FOREIGN KEY (`tech_reviewed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `samples_testing_completed_by_foreign` FOREIGN KEY (`testing_completed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `samples_testing_started_by_foreign` FOREIGN KEY (`testing_started_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `sample_files`
--
ALTER TABLE `sample_files`
  ADD CONSTRAINT `sample_files_sample_id_foreign` FOREIGN KEY (`sample_id`) REFERENCES `samples` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sample_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sample_parameter_requests`
--
ALTER TABLE `sample_parameter_requests`
  ADD CONSTRAINT `sample_parameter_requests_sample_request_id_foreign` FOREIGN KEY (`sample_request_id`) REFERENCES `sample_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sample_parameter_requests_test_parameter_id_foreign` FOREIGN KEY (`test_parameter_id`) REFERENCES `test_parameters` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sample_requests`
--
ALTER TABLE `sample_requests`
  ADD CONSTRAINT `sample_requests_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sample_requests_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `sample_request_parameters`
--
ALTER TABLE `sample_request_parameters`
  ADD CONSTRAINT `sample_request_parameters_sample_request_id_foreign` FOREIGN KEY (`sample_request_id`) REFERENCES `sample_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sample_request_parameters_test_parameter_id_foreign` FOREIGN KEY (`test_parameter_id`) REFERENCES `test_parameters` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sample_reviews`
--
ALTER TABLE `sample_reviews`
  ADD CONSTRAINT `sample_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sample_reviews_sample_id_foreign` FOREIGN KEY (`sample_id`) REFERENCES `samples` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sample_tests`
--
ALTER TABLE `sample_tests`
  ADD CONSTRAINT `sample_tests_reviewed_by_quality_foreign` FOREIGN KEY (`reviewed_by_quality`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sample_tests_reviewed_by_tech_foreign` FOREIGN KEY (`reviewed_by_tech`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sample_tests_sample_id_foreign` FOREIGN KEY (`sample_id`) REFERENCES `samples` (`id`),
  ADD CONSTRAINT `sample_tests_test_parameter_id_foreign` FOREIGN KEY (`test_parameter_id`) REFERENCES `test_parameters` (`id`),
  ADD CONSTRAINT `sample_tests_tested_by_foreign` FOREIGN KEY (`tested_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `sample_type_parameters`
--
ALTER TABLE `sample_type_parameters`
  ADD CONSTRAINT `sample_type_parameters_sample_type_id_foreign` FOREIGN KEY (`sample_type_id`) REFERENCES `sample_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sample_type_parameters_test_parameter_id_foreign` FOREIGN KEY (`test_parameter_id`) REFERENCES `test_parameters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
