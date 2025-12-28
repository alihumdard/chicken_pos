-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 28, 2025 at 11:05 AM
-- Server version: 11.4.9-MariaDB-cll-lve-log
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oraccmkl_chicken_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_forward` varchar(255) NOT NULL DEFAULT 'n',
  `type` varchar(255) NOT NULL,
  `body` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `display_date` datetime DEFAULT NULL,
  `deleted_at` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'unseen',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'customer',
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `type`, `contact_person`, `phone`, `address`, `current_balance`, `created_at`, `updated_at`) VALUES
(1, 'Rana Chicken Shop', 'shop_retail', NULL, '03457754960', 'kot momin', 401116.85, '2025-12-28 19:21:51', '2025-12-28 20:33:25'),
(2, 'B Saif', 'customer', NULL, '03457754961', 'kot Momin', 17609.90, '2025-12-28 19:25:20', '2025-12-28 20:14:45'),
(3, 'B Mushtaq', 'customer', NULL, '03457754965', '66 Chack', 6352.25, '2025-12-28 19:26:38', '2025-12-28 20:16:52'),
(4, 'Malik Hafeez', 'customer', NULL, '03457754969', 'kot Momin', 12654.57, '2025-12-28 19:27:09', '2025-12-28 20:19:35'),
(5, 'Malik Abdul Star', 'customer', NULL, '03457754966', '21 chack', 265.36, '2025-12-28 19:28:20', '2025-12-28 20:20:33'),
(6, 'Ferman Khan', 'customer', NULL, '03457754962', NULL, -345.50, '2025-12-28 19:29:09', '2025-12-28 20:21:37'),
(7, 'Muhammad Zakaullah', 'customer', NULL, '03457754951', NULL, 18.65, '2025-12-28 19:29:43', '2025-12-28 20:22:25'),
(8, 'Mudassar Iqbal', 'customer', NULL, '03457754952', NULL, 61981.02, '2025-12-28 19:30:21', '2025-12-28 20:22:59'),
(9, 'Sain Shokat', 'customer', NULL, '03457754953', NULL, 11543.04, '2025-12-28 19:34:09', '2025-12-28 20:23:52'),
(10, 'H. Nawaz', 'customer', NULL, '03457754954', NULL, 35989.00, '2025-12-28 19:34:58', '2025-12-28 19:34:58'),
(11, 'Nouman Babo', 'customer', NULL, '03457754955', NULL, 74837.59, '2025-12-28 19:36:01', '2025-12-28 20:27:19'),
(12, 'Ali Babo', 'customer', NULL, '03457754956', NULL, 42180.57, '2025-12-28 19:36:33', '2025-12-28 20:28:46'),
(13, 'Sargodha Chicken', 'customer', NULL, '03457754957', NULL, 155869.16, '2025-12-28 19:37:08', '2025-12-28 20:29:43'),
(14, 'Maher Naveed', 'customer', NULL, '03457754958', NULL, 209410.26, '2025-12-28 19:37:49', '2025-12-28 20:30:54'),
(15, 'Ali Jawaid', 'customer', NULL, '03457754959', NULL, 111485.70, '2025-12-28 19:38:22', '2025-12-28 20:31:26'),
(16, 'Atif Ali', 'customer', NULL, '03457754940', NULL, -9494.51, '2025-12-28 19:39:00', '2025-12-28 20:32:03'),
(17, 'Khalid Menhdi', 'customer', NULL, '03457754941', NULL, 103652.66, '2025-12-28 19:40:00', '2025-12-28 20:34:21'),
(18, 'Mohsin Skander', 'customer', NULL, '03457754942', NULL, 66017.75, '2025-12-28 19:40:38', '2025-12-28 20:35:05'),
(19, 'Jawaid Inayat', 'customer', NULL, '03457754943', NULL, 35471.70, '2025-12-28 19:41:14', '2025-12-28 20:35:38'),
(20, 'Imran Adnan', 'customer', NULL, '03457754944', NULL, 7559.56, '2025-12-28 19:41:45', '2025-12-28 20:36:06'),
(21, 'Ateeb Bhai', 'customer', NULL, '03457754945', NULL, 111753.80, '2025-12-28 19:42:23', '2025-12-28 20:36:29'),
(22, 'Kashi', 'customer', NULL, '03457754946', NULL, 30094.44, '2025-12-28 19:42:56', '2025-12-28 20:37:51'),
(23, 'Shahzad', 'customer', NULL, '03457754947', NULL, 20746.98, '2025-12-28 19:43:40', '2025-12-28 20:38:18'),
(24, 'Muneeb + Shabaz', 'customer', NULL, '03457754948', NULL, 13033.94, '2025-12-28 19:44:23', '2025-12-28 20:38:40'),
(25, 'Qari Sb', 'customer', NULL, '03457754949', NULL, 72596.76, '2025-12-28 19:44:54', '2025-12-28 20:39:07'),
(26, 'Saith Imran', 'customer', NULL, '03457754930', NULL, 80480.03, '2025-12-28 19:45:38', '2025-12-28 20:39:35'),
(27, 'Yasir Rana', 'customer', NULL, '03457754931', NULL, 16030.81, '2025-12-28 19:46:16', '2025-12-28 20:40:05'),
(28, 'Bilal', 'customer', NULL, '03457754932', NULL, 0.00, '2025-12-28 19:46:53', '2025-12-28 19:46:53'),
(29, 'Rana Saif ur Rehman', 'customer', NULL, '03457754933', NULL, 411901.68, '2025-12-28 19:47:40', '2025-12-28 20:41:12'),
(30, 'Khalid Tahir', 'customer', NULL, '03457754934', NULL, 0.00, '2025-12-28 19:48:06', '2025-12-28 19:48:06'),
(31, 'Bhati Keryana', 'customer', NULL, '03457754935', NULL, 0.00, '2025-12-28 19:48:39', '2025-12-28 19:48:39'),
(32, 'Rana Arshad', 'customer', NULL, '03457754936', NULL, 0.00, '2025-12-28 19:49:05', '2025-12-28 19:49:05'),
(33, 'Malik Serwar', 'customer', NULL, '03457754937', NULL, 0.00, '2025-12-28 19:49:29', '2025-12-28 19:49:29'),
(34, 'USAMA RANA', 'customer', NULL, '03218991304', 'P/O Same Chak No 09 SB,Kot momin', 0.00, '2025-12-28 20:44:00', '2025-12-28 20:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `daily_rates`
--

CREATE TABLE `daily_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `base_effective_cost` decimal(10,2) NOT NULL,
  `manual_base_cost` decimal(8,2) NOT NULL DEFAULT 0.00,
  `rate_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rate_values`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_rates`
--

INSERT INTO `daily_rates` (`id`, `supplier_id`, `base_effective_cost`, `manual_base_cost`, `rate_values`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 355.30, 0.00, '{\"wholesale_live_chicken_rate\":\"358.30\",\"wholesale_mix_34_rate\":\"519.53\",\"wholesale_mix_35_rate\":\"537.45\",\"wholesale_mix_36_rate\":\"573.28\",\"wholesale_mix_37_rate\":\"591.19\",\"wholesale_chest_leg_38_rate\":\"609.11\",\"wholesale_drum_sticks_rate\":\"680.77\",\"wholesale_chest_boneless_rate\":\"788.26\",\"wholesale_thigh_boneless_rate\":\"859.92\",\"wholesale_kalagi_pot_rate\":\"355.30\",\"wholesale_chick_paw_rate\":\"177.65\",\"retail_live_chicken_rate\":\"378.30\",\"retail_mix_34_rate\":\"548.53\",\"retail_mix_35_rate\":\"567.45\",\"retail_mix_36_rate\":\"605.28\",\"retail_mix_37_rate\":\"643.11\",\"retail_chest_leg_38_rate\":\"643.11\",\"retail_drum_sticks_rate\":\"718.77\",\"retail_chest_boneless_rate\":\"824.09\",\"retail_thigh_boneless_rate\":\"859.92\",\"retail_kalagi_pot_rate\":\"355.30\",\"retail_chick_paw_rate\":\"177.65\"}', 0, '2025-12-28 19:17:00', '2025-12-28 19:22:25'),
(2, 1, 355.30, 0.00, '{\"wholesale_live_chicken_rate\":\"358.30\",\"wholesale_mix_34_rate\":\"519.53\",\"wholesale_mix_35_rate\":\"537.45\",\"wholesale_mix_36_rate\":\"573.28\",\"wholesale_mix_37_rate\":\"591.19\",\"wholesale_chest_leg_38_rate\":\"609.11\",\"wholesale_drum_sticks_rate\":\"680.77\",\"wholesale_chest_boneless_rate\":\"788.26\",\"wholesale_thigh_boneless_rate\":\"859.92\",\"wholesale_kalagi_pot_rate\":\"355.30\",\"wholesale_chick_paw_rate\":\"177.65\",\"retail_live_chicken_rate\":\"378.30\",\"retail_mix_34_rate\":\"548.53\",\"retail_mix_35_rate\":\"567.45\",\"retail_mix_36_rate\":\"605.28\",\"retail_mix_37_rate\":\"624.19\",\"retail_chest_leg_38_rate\":\"643.11\",\"retail_drum_sticks_rate\":\"718.77\",\"retail_chest_boneless_rate\":\"788.26\",\"retail_thigh_boneless_rate\":\"859.92\",\"retail_kalagi_pot_rate\":\"355.30\",\"retail_chick_paw_rate\":\"177.65\"}', 1, '2025-12-28 19:22:25', '2025-12-28 19:22:25');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'General',
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_01_05_004450_create_alerts_table', 1),
(6, '2025_12_01_081421_create_suppliers_table', 1),
(7, '2025_12_01_081452_create_customers_table', 1),
(8, '2025_12_01_152052_create_purchases_table', 1),
(9, '2025_12_02_071026_create_sales_table', 1),
(10, '2025_12_02_075958_create_daily_rates_table', 1),
(11, '2025_12_02_180048_add_manual_base_cost_to_daily_rates_table', 1),
(12, '2025_12_03_082626_add_new_rate_columns_to_daily_rates_table', 1),
(13, '2025_12_03_100223_add_sale_channel_to_sales_table', 1),
(14, '2025_12_04_092437_create_rate_formulas_table', 1),
(15, '2025_12_05_052341_create_settings_table', 1),
(16, '2025_12_06_171240_create_transactions_table', 1),
(17, '2025_12_07_051500_create_poultries_table', 1),
(18, '2025_12_07_052827_create_expenses_table', 1),
(19, '2025_12_26_064907_add_extended_poultry_columns_to_daily_rates_table', 1),
(20, '2025_12_26_072925_add_total_kharch_to_purchases_table', 1),
(21, '2025_12_27_054808_add_type_to_suppliers_table', 1),
(22, '2025_12_27_054818_add_type_to_customers_table', 1),
(23, '2025_12_27_075019_add_address_to_contacts_tables', 1),
(24, '2025_12_27_145402_add_extra_fields_to_transactions_table', 1),
(25, '2025_12_28_130216_restructure_daily_rates_to_json', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poultries`
--

CREATE TABLE `poultries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entry_date` date NOT NULL,
  `batch_no` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_weight` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `driver_no` varchar(255) DEFAULT NULL,
  `gross_weight` decimal(10,2) NOT NULL,
  `dead_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `dead_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shrink_loss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_live_weight` decimal(10,2) NOT NULL,
  `buying_rate` decimal(10,2) NOT NULL,
  `total_kharch` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_payable` decimal(10,2) NOT NULL,
  `effective_cost` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL DEFAULT '2025-12-28',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `driver_no`, `gross_weight`, `dead_qty`, `dead_weight`, `shrink_loss`, `net_live_weight`, `buying_rate`, `total_kharch`, `total_payable`, `effective_cost`, `purchase_date`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 4080.00, 0, 0.00, 30.60, 4049.40, 347.00, 23000.00, 1415760.00, 355.30, '2025-12-28', '2025-12-28 18:46:43', '2025-12-28 18:46:43');

-- --------------------------------------------------------

--
-- Table structure for table `rate_formulas`
--

CREATE TABLE `rate_formulas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `rate_key` varchar(255) NOT NULL,
  `icon_url` varchar(255) DEFAULT NULL,
  `channel` enum('wholesale','retail') NOT NULL,
  `multiply` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `divide` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `plus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rate_formulas`
--

INSERT INTO `rate_formulas` (`id`, `title`, `rate_key`, `icon_url`, `channel`, `multiply`, `divide`, `plus`, `minus`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Wholesale Live Chicken', 'wholesale_live_chicken_rate', NULL, 'wholesale', 1.0000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:08:03'),
(2, 'Mix (No. 34)', 'wholesale_mix_34_rate', NULL, 'wholesale', 1.4500, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:08:19'),
(3, 'Mix (No. 35)', 'wholesale_mix_35_rate', NULL, 'wholesale', 1.5000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:08:33'),
(4, 'Mix (No. 36)', 'wholesale_mix_36_rate', NULL, 'wholesale', 1.6000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:08:45'),
(5, 'Mix (No. 37)', 'wholesale_mix_37_rate', NULL, 'wholesale', 1.6500, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:09:39'),
(6, 'Chest Leg (No. 38)', 'wholesale_chest_leg_38_rate', NULL, 'wholesale', 1.7000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:09:59'),
(7, 'Drum Sticks', 'wholesale_drum_sticks_rate', NULL, 'wholesale', 1.9000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:10:16'),
(8, 'Chest Boneless', 'wholesale_chest_boneless_rate', NULL, 'wholesale', 2.2000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:10:36'),
(9, 'Thigh Boneless', 'wholesale_thigh_boneless_rate', NULL, 'wholesale', 2.4000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:11:25'),
(10, 'Kalagi Pot', 'wholesale_kalagi_pot_rate', NULL, 'wholesale', 1.0000, 1.0000, 0.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 18:36:16'),
(11, 'Chick Paws', 'wholesale_chick_paw_rate', NULL, 'wholesale', 1.0000, 2.0000, 0.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:11:57'),
(12, 'Retail Live Chicken', 'retail_live_chicken_rate', NULL, 'retail', 1.0000, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:14:05'),
(13, 'Mix (No. 34)', 'retail_mix_34_rate', NULL, 'retail', 1.4500, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:14:29'),
(14, 'Mix (No. 35)', 'retail_mix_35_rate', NULL, 'retail', 1.5000, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:14:46'),
(15, 'Mix (No. 36)', 'retail_mix_36_rate', NULL, 'retail', 1.6000, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:15:05'),
(16, 'Mix (No. 37)', 'retail_mix_37_rate', NULL, 'retail', 1.6500, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:19:04'),
(17, 'Chest Leg (No. 38)', 'retail_chest_leg_38_rate', NULL, 'retail', 1.7000, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:15:38'),
(18, 'Drum Sticks', 'retail_drum_sticks_rate', NULL, 'retail', 1.9000, 1.0000, 23.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:15:51'),
(19, 'Chest Boneless', 'retail_chest_boneless_rate', NULL, 'retail', 2.2000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:20:22'),
(20, 'Thigh Boneless', 'retail_thigh_boneless_rate', NULL, 'retail', 2.4000, 1.0000, 3.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:16:27'),
(21, 'Kalagi Pot', 'retail_kalagi_pot_rate', NULL, 'retail', 1.0000, 1.0000, 0.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 18:36:16'),
(22, 'Chick Paws', 'retail_chick_paw_rate', NULL, 'retail', 1.0000, 2.0000, 0.00, 0.00, 1, '2025-12-28 18:36:16', '2025-12-28 19:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(20) NOT NULL DEFAULT 'credit',
  `sale_channel` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `total_amount`, `payment_status`, `sale_channel`, `created_at`, `updated_at`) VALUES
(1, 2, 15406.90, 'partial', NULL, '2025-12-28 20:14:45', '2025-12-28 20:14:45'),
(2, 3, 2687.25, 'paid', NULL, '2025-12-28 20:16:52', '2025-12-28 20:16:52'),
(3, 4, 6413.57, 'partial', NULL, '2025-12-28 20:19:35', '2025-12-28 20:19:35'),
(4, 5, 10462.36, 'partial', NULL, '2025-12-28 20:20:33', '2025-12-28 20:20:33'),
(5, 6, 5374.50, 'paid', NULL, '2025-12-28 20:21:37', '2025-12-28 20:21:37'),
(6, 7, 5553.65, 'partial', NULL, '2025-12-28 20:22:25', '2025-12-28 20:22:25'),
(7, 8, 46364.02, 'paid', NULL, '2025-12-28 20:22:59', '2025-12-28 20:22:59'),
(8, 9, 31817.04, 'partial', NULL, '2025-12-28 20:23:52', '2025-12-28 20:23:52'),
(9, 11, 38445.59, 'partial', NULL, '2025-12-28 20:26:21', '2025-12-28 20:26:21'),
(10, 12, 27911.57, 'paid', NULL, '2025-12-28 20:28:46', '2025-12-28 20:28:46'),
(11, 13, 55608.16, 'credit', NULL, '2025-12-28 20:29:43', '2025-12-28 20:29:43'),
(12, 14, 90363.26, 'partial', NULL, '2025-12-28 20:30:54', '2025-12-28 20:30:54'),
(13, 15, 39054.70, 'credit', NULL, '2025-12-28 20:31:26', '2025-12-28 20:31:26'),
(14, 16, 21605.49, 'paid', NULL, '2025-12-28 20:32:03', '2025-12-28 20:32:03'),
(15, 1, 401116.85, 'credit', NULL, '2025-12-28 20:33:25', '2025-12-28 20:33:25'),
(16, 17, 39484.66, 'credit', NULL, '2025-12-28 20:34:21', '2025-12-28 20:34:21'),
(17, 18, 29559.75, 'credit', NULL, '2025-12-28 20:35:05', '2025-12-28 20:35:05'),
(18, 19, 35471.70, 'credit', NULL, '2025-12-28 20:35:38', '2025-12-28 20:35:38'),
(19, 20, 4729.56, 'credit', NULL, '2025-12-28 20:36:06', '2025-12-28 20:36:06'),
(20, 21, 20064.80, 'credit', NULL, '2025-12-28 20:36:29', '2025-12-28 20:36:29'),
(21, 22, 23934.44, 'credit', NULL, '2025-12-28 20:37:51', '2025-12-28 20:37:51'),
(22, 23, 10963.98, 'credit', NULL, '2025-12-28 20:38:18', '2025-12-28 20:38:18'),
(23, 24, 4227.94, 'credit', NULL, '2025-12-28 20:38:40', '2025-12-28 20:38:40'),
(24, 25, 16911.76, 'credit', NULL, '2025-12-28 20:39:07', '2025-12-28 20:39:07'),
(25, 26, 44465.03, 'credit', NULL, '2025-12-28 20:39:35', '2025-12-28 20:39:35'),
(26, 27, 10999.81, 'credit', NULL, '2025-12-28 20:40:05', '2025-12-28 20:40:05'),
(27, 29, 411901.68, 'credit', NULL, '2025-12-28 20:41:12', '2025-12-28 20:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_category` varchar(50) NOT NULL,
  `weight_kg` decimal(8,3) NOT NULL,
  `rate_pkr` decimal(10,2) NOT NULL,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_category`, `weight_kg`, `rate_pkr`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 'Wholesale Live Chicken', 43.000, 358.30, 15406.90, '2025-12-28 20:14:45', '2025-12-28 20:14:45'),
(2, 2, 'Wholesale Live Chicken', 7.500, 358.30, 2687.25, '2025-12-28 20:16:52', '2025-12-28 20:16:52'),
(3, 3, 'Wholesale Live Chicken', 17.900, 358.30, 6413.57, '2025-12-28 20:19:35', '2025-12-28 20:19:35'),
(4, 4, 'Wholesale Live Chicken', 29.200, 358.30, 10462.36, '2025-12-28 20:20:33', '2025-12-28 20:20:33'),
(5, 5, 'Wholesale Live Chicken', 15.000, 358.30, 5374.50, '2025-12-28 20:21:37', '2025-12-28 20:21:37'),
(6, 6, 'Wholesale Live Chicken', 15.500, 358.30, 5553.65, '2025-12-28 20:22:25', '2025-12-28 20:22:25'),
(7, 7, 'Wholesale Live Chicken', 129.400, 358.30, 46364.02, '2025-12-28 20:22:59', '2025-12-28 20:22:59'),
(8, 8, 'Wholesale Live Chicken', 88.800, 358.30, 31817.04, '2025-12-28 20:23:52', '2025-12-28 20:23:52'),
(9, 9, 'Wholesale Live Chicken', 107.300, 358.30, 38445.59, '2025-12-28 20:26:21', '2025-12-28 20:26:21'),
(10, 10, 'Wholesale Live Chicken', 77.900, 358.30, 27911.57, '2025-12-28 20:28:46', '2025-12-28 20:28:46'),
(11, 11, 'Wholesale Live Chicken', 155.200, 358.30, 55608.16, '2025-12-28 20:29:43', '2025-12-28 20:29:43'),
(12, 12, 'Wholesale Live Chicken', 252.200, 358.30, 90363.26, '2025-12-28 20:30:54', '2025-12-28 20:30:54'),
(13, 13, 'Wholesale Live Chicken', 109.000, 358.30, 39054.70, '2025-12-28 20:31:26', '2025-12-28 20:31:26'),
(14, 14, 'Wholesale Live Chicken', 60.300, 358.30, 21605.49, '2025-12-28 20:32:03', '2025-12-28 20:32:03'),
(15, 15, 'Wholesale Live Chicken', 669.900, 358.30, 240025.17, '2025-12-28 20:33:25', '2025-12-28 20:33:25'),
(16, 15, 'Wholesale Live Chicken', 449.600, 358.30, 161091.68, '2025-12-28 20:33:25', '2025-12-28 20:33:25'),
(17, 16, 'Wholesale Live Chicken', 110.200, 358.30, 39484.66, '2025-12-28 20:34:21', '2025-12-28 20:34:21'),
(18, 17, 'Wholesale Live Chicken', 82.500, 358.30, 29559.75, '2025-12-28 20:35:05', '2025-12-28 20:35:05'),
(19, 18, 'Wholesale Live Chicken', 99.000, 358.30, 35471.70, '2025-12-28 20:35:38', '2025-12-28 20:35:38'),
(20, 19, 'Wholesale Live Chicken', 13.200, 358.30, 4729.56, '2025-12-28 20:36:06', '2025-12-28 20:36:06'),
(21, 20, 'Wholesale Live Chicken', 56.000, 358.30, 20064.80, '2025-12-28 20:36:29', '2025-12-28 20:36:29'),
(22, 21, 'Wholesale Live Chicken', 66.800, 358.30, 23934.44, '2025-12-28 20:37:51', '2025-12-28 20:37:51'),
(23, 22, 'Wholesale Live Chicken', 30.600, 358.30, 10963.98, '2025-12-28 20:38:18', '2025-12-28 20:38:18'),
(24, 23, 'Wholesale Live Chicken', 11.800, 358.30, 4227.94, '2025-12-28 20:38:40', '2025-12-28 20:38:40'),
(25, 24, 'Wholesale Live Chicken', 47.200, 358.30, 16911.76, '2025-12-28 20:39:07', '2025-12-28 20:39:07'),
(26, 25, 'Wholesale Live Chicken', 124.100, 358.30, 44465.03, '2025-12-28 20:39:35', '2025-12-28 20:39:35'),
(27, 26, 'Wholesale Live Chicken', 30.700, 358.30, 10999.81, '2025-12-28 20:40:05', '2025-12-28 20:40:05'),
(28, 27, 'Wholesale Live Chicken', 1149.600, 358.30, 411901.68, '2025-12-28 20:41:12', '2025-12-28 20:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL COMMENT 'Path to the uploaded logo image.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `shop_name`, `address`, `phone_number`, `logo_url`, `created_at`, `updated_at`) VALUES
(1, 'RANA POS', NULL, NULL, NULL, '2025-12-28 18:36:15', '2025-12-28 18:36:15');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'supplier',
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `type`, `contact_person`, `phone`, `address`, `current_balance`, `created_at`, `updated_at`) VALUES
(1, 'Zafer Iqbal', 'supplier', NULL, '03400602398', NULL, 1415760.00, '2025-12-28 18:45:09', '2025-12-28 18:46:43');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `gross_weight` decimal(10,2) DEFAULT NULL,
  `dead_weight` decimal(10,2) DEFAULT NULL,
  `shrink_loss` decimal(10,2) DEFAULT NULL,
  `net_live_weight` decimal(10,2) DEFAULT NULL,
  `total_kharch` decimal(12,2) DEFAULT NULL,
  `buying_rate` decimal(10,2) DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `supplier_id`, `customer_id`, `date`, `type`, `description`, `gross_weight`, `dead_weight`, `shrink_loss`, `net_live_weight`, `total_kharch`, `buying_rate`, `debit`, `credit`, `balance`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2025-12-28', 'purchase', 'Purchase #1', 4080.00, NULL, 30.60, 4049.40, 23000.00, NULL, 0.00, 1415760.00, 1415760.00, '2025-12-28 18:46:43', '2025-12-28 18:46:43'),
(2, NULL, 2, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 15203.00, 0.00, 15203.00, '2025-12-28 19:25:20', '2025-12-28 19:25:20'),
(3, NULL, 3, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 7665.00, 0.00, 2687.00, '2025-12-28 19:26:38', '2025-12-28 20:16:02'),
(4, NULL, 4, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 11741.00, 0.00, 6413.00, '2025-12-28 19:27:09', '2025-12-28 20:17:52'),
(5, NULL, 5, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 263.00, 0.00, 263.00, '2025-12-28 19:28:20', '2025-12-28 19:28:20'),
(6, NULL, 7, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 15.00, 0.00, 15.00, '2025-12-28 19:29:43', '2025-12-28 19:29:43'),
(7, NULL, 8, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 94617.00, 0.00, 94617.00, '2025-12-28 19:30:21', '2025-12-28 19:30:21'),
(8, NULL, 9, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 11543.00, 0.00, 11543.00, '2025-12-28 19:34:09', '2025-12-28 19:34:09'),
(9, NULL, 10, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 35989.00, 0.00, 35989.00, '2025-12-28 19:34:58', '2025-12-28 19:34:58'),
(10, NULL, 11, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 67392.00, 0.00, 67392.00, '2025-12-28 19:36:01', '2025-12-28 19:36:01'),
(11, NULL, 12, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 42269.00, 0.00, 42269.00, '2025-12-28 19:36:33', '2025-12-28 19:36:33'),
(12, NULL, 13, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 100261.00, 0.00, 100261.00, '2025-12-28 19:37:08', '2025-12-28 19:37:08'),
(13, NULL, 14, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 194767.00, 0.00, 194767.00, '2025-12-28 19:37:49', '2025-12-28 19:37:49'),
(14, NULL, 15, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 72431.00, 0.00, 72431.00, '2025-12-28 19:38:22', '2025-12-28 19:38:22'),
(16, NULL, 17, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 64168.00, 0.00, 64168.00, '2025-12-28 19:40:00', '2025-12-28 19:40:00'),
(17, NULL, 18, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 36458.00, 0.00, 36458.00, '2025-12-28 19:40:38', '2025-12-28 19:40:38'),
(18, NULL, 20, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 2830.00, 0.00, 2830.00, '2025-12-28 19:41:45', '2025-12-28 19:41:45'),
(19, NULL, 21, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 91689.00, 0.00, 91689.00, '2025-12-28 19:42:23', '2025-12-28 19:42:23'),
(20, NULL, 22, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 6160.00, 0.00, 6160.00, '2025-12-28 19:42:56', '2025-12-28 19:42:56'),
(21, NULL, 23, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 9783.00, 0.00, 9783.00, '2025-12-28 19:43:40', '2025-12-28 19:43:40'),
(22, NULL, 24, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 8806.00, 0.00, 8806.00, '2025-12-28 19:44:23', '2025-12-28 19:44:23'),
(23, NULL, 25, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 55685.00, 0.00, 55685.00, '2025-12-28 19:44:54', '2025-12-28 19:44:54'),
(24, NULL, 26, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 36015.00, 0.00, 36015.00, '2025-12-28 19:45:38', '2025-12-28 19:45:38'),
(25, NULL, 27, '2025-12-28', 'opening_balance', 'Opening Balance Entry', NULL, NULL, NULL, NULL, NULL, NULL, 5031.00, 0.00, 5031.00, '2025-12-28 19:46:16', '2025-12-28 19:46:16'),
(26, NULL, 2, '2025-12-28', 'sale', 'Sale #1 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 15406.90, 0.00, 30609.90, '2025-12-28 20:14:45', '2025-12-28 20:14:45'),
(27, NULL, 2, '2025-12-28', 'payment', 'Cash Received for Sale #1', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 13000.00, 17609.90, '2025-12-28 20:14:45', '2025-12-28 20:14:45'),
(28, NULL, 3, '2025-12-28', 'sale', 'Sale #2 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 2687.25, 0.00, 10352.25, '2025-12-28 20:16:52', '2025-12-28 20:16:52'),
(29, NULL, 3, '2025-12-28', 'payment', 'Cash Received for Sale #2', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 4000.00, 6352.25, '2025-12-28 20:16:52', '2025-12-28 20:16:52'),
(30, NULL, 4, '2025-12-28', 'sale', 'Sale #3 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 6413.57, 0.00, 18154.57, '2025-12-28 20:19:35', '2025-12-28 20:19:35'),
(31, NULL, 4, '2025-12-28', 'payment', 'Cash Received for Sale #3', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 5500.00, 12654.57, '2025-12-28 20:19:35', '2025-12-28 20:19:35'),
(32, NULL, 5, '2025-12-28', 'sale', 'Sale #4 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 10462.36, 0.00, 10725.36, '2025-12-28 20:20:33', '2025-12-28 20:20:33'),
(33, NULL, 5, '2025-12-28', 'payment', 'Cash Received for Sale #4', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 10460.00, 265.36, '2025-12-28 20:20:33', '2025-12-28 20:20:33'),
(34, NULL, 6, '2025-12-28', 'sale', 'Sale #5 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 5374.50, 0.00, 5374.50, '2025-12-28 20:21:37', '2025-12-28 20:21:37'),
(35, NULL, 6, '2025-12-28', 'payment', 'Cash Received for Sale #5', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 5720.00, -345.50, '2025-12-28 20:21:37', '2025-12-28 20:21:37'),
(36, NULL, 7, '2025-12-28', 'sale', 'Sale #6 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 5553.65, 0.00, 5568.65, '2025-12-28 20:22:25', '2025-12-28 20:22:25'),
(37, NULL, 7, '2025-12-28', 'payment', 'Cash Received for Sale #6', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 5550.00, 18.65, '2025-12-28 20:22:25', '2025-12-28 20:22:25'),
(38, NULL, 8, '2025-12-28', 'sale', 'Sale #7 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 46364.02, 0.00, 140981.02, '2025-12-28 20:22:59', '2025-12-28 20:22:59'),
(39, NULL, 8, '2025-12-28', 'payment', 'Cash Received for Sale #7', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 79000.00, 61981.02, '2025-12-28 20:22:59', '2025-12-28 20:22:59'),
(40, NULL, 9, '2025-12-28', 'sale', 'Sale #8 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 31817.04, 0.00, 43360.04, '2025-12-28 20:23:52', '2025-12-28 20:23:52'),
(41, NULL, 9, '2025-12-28', 'payment', 'Cash Received for Sale #8', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 31817.00, 11543.04, '2025-12-28 20:23:52', '2025-12-28 20:23:52'),
(42, NULL, 11, '2025-12-28', 'sale', 'Sale #9 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 38445.59, 0.00, 105837.59, '2025-12-28 20:26:21', '2025-12-28 20:26:21'),
(43, NULL, 11, '2025-12-28', 'payment', 'Cash Received for Sale #9', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 31000.00, 74837.59, '2025-12-28 20:26:21', '2025-12-28 20:26:21'),
(44, NULL, 12, '2025-12-28', 'sale', 'Sale #10 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 27911.57, 0.00, 70180.57, '2025-12-28 20:28:46', '2025-12-28 20:28:46'),
(45, NULL, 12, '2025-12-28', 'payment', 'Cash Received for Sale #10', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 28000.00, 42180.57, '2025-12-28 20:28:46', '2025-12-28 20:28:46'),
(46, NULL, 13, '2025-12-28', 'sale', 'Sale #11 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 55608.16, 0.00, 155869.16, '2025-12-28 20:29:43', '2025-12-28 20:29:43'),
(47, NULL, 14, '2025-12-28', 'sale', 'Sale #12 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 90363.26, 0.00, 285130.26, '2025-12-28 20:30:54', '2025-12-28 20:30:54'),
(48, NULL, 14, '2025-12-28', 'payment', 'Cash Received for Sale #12', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 75720.00, 209410.26, '2025-12-28 20:30:54', '2025-12-28 20:30:54'),
(49, NULL, 15, '2025-12-28', 'sale', 'Sale #13 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 39054.70, 0.00, 111485.70, '2025-12-28 20:31:26', '2025-12-28 20:31:26'),
(50, NULL, 16, '2025-12-28', 'sale', 'Sale #14 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 21605.49, 0.00, 38683.49, '2025-12-28 20:32:03', '2025-12-28 20:32:03'),
(51, NULL, 16, '2025-12-28', 'payment', 'Cash Received for Sale #14', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 31100.00, 7583.49, '2025-12-28 20:32:03', '2025-12-28 20:32:03'),
(52, NULL, 1, '2025-12-28', 'sale', 'Sale #15 (2 items)', NULL, NULL, NULL, NULL, NULL, NULL, 401116.85, 0.00, 401116.85, '2025-12-28 20:33:25', '2025-12-28 20:33:25'),
(53, NULL, 17, '2025-12-28', 'sale', 'Sale #16 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 39484.66, 0.00, 103652.66, '2025-12-28 20:34:21', '2025-12-28 20:34:21'),
(54, NULL, 18, '2025-12-28', 'sale', 'Sale #17 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 29559.75, 0.00, 66017.75, '2025-12-28 20:35:05', '2025-12-28 20:35:05'),
(55, NULL, 19, '2025-12-28', 'sale', 'Sale #18 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 35471.70, 0.00, 35471.70, '2025-12-28 20:35:38', '2025-12-28 20:35:38'),
(56, NULL, 20, '2025-12-28', 'sale', 'Sale #19 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 4729.56, 0.00, 7559.56, '2025-12-28 20:36:06', '2025-12-28 20:36:06'),
(57, NULL, 21, '2025-12-28', 'sale', 'Sale #20 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 20064.80, 0.00, 111753.80, '2025-12-28 20:36:29', '2025-12-28 20:36:29'),
(58, NULL, 22, '2025-12-28', 'sale', 'Sale #21 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 23934.44, 0.00, 30094.44, '2025-12-28 20:37:51', '2025-12-28 20:37:51'),
(59, NULL, 23, '2025-12-28', 'sale', 'Sale #22 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 10963.98, 0.00, 20746.98, '2025-12-28 20:38:18', '2025-12-28 20:38:18'),
(60, NULL, 24, '2025-12-28', 'sale', 'Sale #23 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 4227.94, 0.00, 13033.94, '2025-12-28 20:38:40', '2025-12-28 20:38:40'),
(61, NULL, 25, '2025-12-28', 'sale', 'Sale #24 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 16911.76, 0.00, 72596.76, '2025-12-28 20:39:07', '2025-12-28 20:39:07'),
(62, NULL, 26, '2025-12-28', 'sale', 'Sale #25 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 44465.03, 0.00, 80480.03, '2025-12-28 20:39:35', '2025-12-28 20:39:35'),
(63, NULL, 27, '2025-12-28', 'sale', 'Sale #26 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 10999.81, 0.00, 16030.81, '2025-12-28 20:40:05', '2025-12-28 20:40:05'),
(64, NULL, 29, '2025-12-28', 'sale', 'Sale #27 (1 items)', NULL, NULL, NULL, NULL, NULL, NULL, 411901.68, 0.00, 411901.68, '2025-12-28 20:41:12', '2025-12-28 20:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `user_pic` varchar(255) DEFAULT NULL,
  `com_name` varchar(255) DEFAULT NULL,
  `com_pic` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `otp` int(11) DEFAULT NULL,
  `reset_pswd_time` varchar(255) DEFAULT NULL,
  `reset_pswd_attempt` varchar(255) DEFAULT NULL,
  `subscribed_to_newsletter` tinyint(1) DEFAULT 0,
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `privacy_policy_accepted_at` timestamp NULL DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sadmin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT '2',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `role`, `user_pic`, `com_name`, `com_pic`, `country`, `zip_code`, `city`, `state`, `otp`, `reset_pswd_time`, `reset_pswd_attempt`, `subscribed_to_newsletter`, `terms_accepted_at`, `privacy_policy_accepted_at`, `staff_id`, `sadmin_id`, `status`, `created_by`, `updated_by`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin@example.com', '2025-12-28 18:36:16', '$2y$10$FBcl2LCcEwYqmvC3BnHxyObC/L5Xa9Z6KzsgT5boe7xQULhkjUMMm', NULL, NULL, 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2025-12-28 18:36:16', '2025-12-28 18:36:16', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`) USING HASH;

--
-- Indexes for table `daily_rates`
--
ALTER TABLE `daily_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daily_rates_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING HASH;

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`);

--
-- Indexes for table `poultries`
--
ALTER TABLE `poultries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `rate_formulas`
--
ALTER TABLE `rate_formulas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_phone_unique` (`phone`) USING HASH;

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_supplier_id_foreign` (`supplier_id`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `daily_rates`
--
ALTER TABLE `daily_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poultries`
--
ALTER TABLE `poultries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rate_formulas`
--
ALTER TABLE `rate_formulas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
