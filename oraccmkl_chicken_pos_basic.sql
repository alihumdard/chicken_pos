-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 26, 2025 at 01:06 AM
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

INSERT INTO `customers` (`id`, `name`, `contact_person`, `phone`, `address`, `current_balance`, `created_at`, `updated_at`) VALUES
(1, 'Usama Rana', NULL, '03218991304', 'kot momin', 188560.70, '2025-12-25 16:33:22', '2025-12-25 17:50:32'),
(2, 'Rana Chicken Shop', NULL, '03400602398', 'Kot Momin', 10608.90, '2025-12-25 18:05:37', '2025-12-25 18:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `daily_rates`
--

CREATE TABLE `daily_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `base_effective_cost` decimal(10,2) NOT NULL,
  `manual_base_cost` decimal(8,2) NOT NULL DEFAULT 0.00,
  `wholesale_rate` decimal(10,2) NOT NULL,
  `permanent_rate` decimal(10,2) NOT NULL,
  `live_chicken_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wholesale_mix_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wholesale_chest_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wholesale_thigh_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wholesale_customer_piece_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `retail_mix_rate` decimal(10,2) NOT NULL,
  `retail_chest_rate` decimal(10,2) NOT NULL,
  `retail_thigh_rate` decimal(10,2) NOT NULL,
  `retail_piece_rate` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_rates`
--

INSERT INTO `daily_rates` (`id`, `supplier_id`, `base_effective_cost`, `manual_base_cost`, `wholesale_rate`, `permanent_rate`, `live_chicken_rate`, `wholesale_mix_rate`, `wholesale_chest_rate`, `wholesale_thigh_rate`, `wholesale_customer_piece_rate`, `retail_mix_rate`, `retail_chest_rate`, `retail_thigh_rate`, `retail_piece_rate`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 353.00, 0, '2025-12-25 15:13:52', '2025-12-25 20:01:11'),
(2, 1, 353.00, 353.00, 363.00, 353.00, 373.00, 378.00, 478.00, 428.00, 353.00, 403.00, 503.00, 453.00, 343.00, 0, '2025-12-25 15:13:56', '2025-12-25 20:01:11'),
(3, 1, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 353.63, 0, '2025-12-25 15:14:03', '2025-12-25 20:01:11'),
(4, 1, 350.63, 350.63, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 561.01, 578.54, 0, '2025-12-25 16:27:48', '2025-12-25 20:01:11'),
(5, 1, 350.63, 350.63, 363.63, 350.63, 390.63, 544.66, 713.45, 681.01, 578.54, 600.91, 770.95, 721.01, 562.04, 0, '2025-12-25 16:28:06', '2025-12-25 20:01:11'),
(6, 1, 353.63, 353.63, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 565.81, 583.49, 0, '2025-12-25 16:29:01', '2025-12-25 20:01:11'),
(7, 1, 353.63, 353.63, 353.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 17:36:26', '2025-12-25 20:01:11'),
(8, 1, 353.63, 353.63, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:33:57', '2025-12-25 20:01:11'),
(9, 1, 353.63, 0.00, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:35:49', '2025-12-25 20:01:11'),
(10, 1, 353.63, 0.00, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:35:50', '2025-12-25 20:01:11'),
(11, 1, 353.63, 0.00, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:35:59', '2025-12-25 20:01:11'),
(12, 1, 353.63, 0.00, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:36:09', '2025-12-25 20:01:11'),
(13, 1, 353.63, 0.00, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:36:17', '2025-12-25 20:01:11'),
(14, 1, 353.63, 0.00, 356.63, 353.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 0, '2025-12-25 18:37:24', '2025-12-25 20:01:11'),
(15, 1, 350.00, 350.00, 353.00, 350.00, 370.00, 507.50, 525.00, 560.00, 577.50, 536.50, 555.00, 592.00, 610.50, 0, '2025-12-25 18:37:39', '2025-12-25 20:01:11'),
(16, 1, 350.00, 350.00, 363.00, 350.00, 390.00, 543.75, 712.50, 680.00, 577.50, 600.00, 770.00, 740.00, 581.00, 0, '2025-12-25 18:37:42', '2025-12-25 20:01:11'),
(17, 1, 350.00, 0.00, 353.00, 350.00, 370.00, 507.50, 525.00, 560.00, 577.50, 536.50, 555.00, 592.00, 610.50, 0, '2025-12-25 18:47:09', '2025-12-25 20:01:11'),
(18, 1, 0.00, 0.00, 13.00, 0.00, 40.00, 36.25, 187.50, 120.00, 0.00, 92.50, 245.00, 180.00, 3.50, 0, '2025-12-25 18:47:15', '2025-12-25 20:01:11'),
(19, 1, 0.00, 0.00, 3.00, 0.00, 20.00, 0.00, 0.00, 0.00, 0.00, 29.00, 30.00, 32.00, 33.00, 0, '2025-12-25 18:47:29', '2025-12-25 20:01:11'),
(20, 1, 0.00, 0.00, 13.00, 0.00, 40.00, 36.25, 187.50, 120.00, 0.00, 92.50, 245.00, 180.00, 3.50, 0, '2025-12-25 18:47:47', '2025-12-25 20:01:11'),
(21, 1, 400.00, 400.00, 403.00, 400.00, 420.00, 580.00, 600.00, 640.00, 660.00, 609.00, 630.00, 672.00, 693.00, 0, '2025-12-25 18:48:38', '2025-12-25 20:01:11'),
(22, 1, 400.00, 0.00, 403.00, 400.00, 420.00, 580.00, 600.00, 640.00, 660.00, 609.00, 630.00, 672.00, 693.00, 0, '2025-12-25 18:51:36', '2025-12-25 20:01:11'),
(23, 1, 400.00, 0.00, 403.00, 400.00, 420.00, 580.00, 600.00, 640.00, 660.00, 609.00, 630.00, 672.00, 693.00, 0, '2025-12-25 18:52:47', '2025-12-25 20:01:11'),
(24, 1, 350.63, 350.63, 353.63, 350.63, 370.63, 508.42, 525.95, 561.01, 578.54, 537.42, 555.95, 593.01, 611.54, 0, '2025-12-25 18:54:01', '2025-12-25 20:01:11'),
(25, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 18:54:12', '2025-12-25 20:01:11'),
(26, 1, 0.00, 0.00, 13.00, 0.00, 40.00, 36.25, 187.50, 120.00, 0.00, 92.50, 245.00, 180.00, 3.50, 0, '2025-12-25 18:54:17', '2025-12-25 20:01:11'),
(27, 1, 0.00, 0.00, 3.00, 0.00, 20.00, 0.00, 0.00, 0.00, 0.00, 29.00, 30.00, 32.00, 33.00, 0, '2025-12-25 18:55:21', '2025-12-25 20:01:11'),
(28, 1, 350.63, 350.63, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 18:56:54', '2025-12-25 20:01:11'),
(29, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 18:57:39', '2025-12-25 20:01:11'),
(30, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 18:58:19', '2025-12-25 20:01:11'),
(31, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 18:58:42', '2025-12-25 20:01:11'),
(32, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 19:00:37', '2025-12-25 20:01:11'),
(33, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 508.41, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 19:00:56', '2025-12-25 20:01:11'),
(34, 1, 350.63, 0.00, 363.63, 350.63, 390.63, 544.66, 713.45, 681.01, 578.54, 600.91, 770.95, 741.01, 582.04, 0, '2025-12-25 19:01:19', '2025-12-25 20:01:11'),
(35, 1, 350.63, 0.00, 353.63, 350.63, 370.63, 530.44, 525.94, 561.01, 578.54, 537.41, 555.94, 593.01, 611.54, 0, '2025-12-25 19:04:06', '2025-12-25 20:01:11'),
(36, 1, 350.63, 0.00, 353.63, 350.63, 373.63, 512.76, 530.44, 565.81, 583.49, 541.76, 560.44, 597.81, 616.49, 1, '2025-12-25 20:01:11', '2025-12-25 20:01:11');

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
(18, '2025_12_07_052827_create_expenses_table', 1);

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
  `total_payable` decimal(10,2) NOT NULL,
  `effective_cost` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL DEFAULT '2025-12-25',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `driver_no`, `gross_weight`, `dead_qty`, `dead_weight`, `shrink_loss`, `net_live_weight`, `buying_rate`, `total_payable`, `effective_cost`, `purchase_date`, `created_at`, `updated_at`) VALUES
(3, 1, NULL, 4037.00, 0, 35.00, 0.00, 4002.00, 340.00, 1372580.00, 350.63, '2025-12-25', '2025-12-25 18:37:09', '2025-12-25 18:37:09'),
(4, 1, NULL, 4037.00, 0, 0.00, 35.00, 4002.00, 340.00, 1372580.00, 350.63, '2025-12-25', '2025-12-25 18:45:13', '2025-12-25 18:45:13'),
(5, 1, NULL, 4037.00, 0, 0.00, 35.00, 4002.00, 340.00, 1372580.00, 351.89, '2025-12-25', '2025-12-26 10:40:06', '2025-12-26 10:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `rate_formulas`
--

CREATE TABLE `rate_formulas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rate_key` varchar(255) NOT NULL COMMENT 'The key for the rate being modified (e.g., wholesale_rate)',
  `multiply` decimal(8,4) NOT NULL DEFAULT 1.0000,
  `divide` decimal(8,4) NOT NULL DEFAULT 1.0000,
  `plus` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `minus` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rate_formulas`
--

INSERT INTO `rate_formulas` (`id`, `rate_key`, `multiply`, `divide`, `plus`, `minus`, `created_at`, `updated_at`) VALUES
(1, 'wholesale_rate', 1.0000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 18:33:01'),
(2, 'live_chicken_rate', 1.0000, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:06:16'),
(3, 'wholesale_mix_rate', 1.4500, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:04:37'),
(4, 'wholesale_chest_rate', 1.5000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:05:03'),
(5, 'wholesale_thigh_rate', 1.6000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:05:12'),
(6, 'wholesale_customer_piece_rate', 1.6500, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:05:38'),
(7, 'retail_mix_rate', 1.4500, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:06:33'),
(8, 'retail_chest_rate', 1.5000, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:06:41'),
(9, 'retail_thigh_rate', 1.6000, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:06:50'),
(10, 'retail_piece_rate', 1.6500, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:07:01'),
(11, 'purchase_effective_cost', 1.0000, 1.0000, 0.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 14:40:35'),
(12, 'permanent_rate', 1.0000, 1.0000, 0.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 14:40:35'),
(14, 'wholesale_chest_and_leg_pieces', 1.7000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:42:51'),
(15, 'wholesale_drum_sticks', 1.9000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:44:20'),
(16, 'wholesale_chest_boneless', 2.2000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:44:42'),
(17, 'wholesale_thigh_boneless', 2.4000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:45:03'),
(18, 'wholesale_kalagi_pot_gardan', 1.0000, 1.0000, 0.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 14:40:35'),
(19, 'retail_chest_and_leg_pieces', 1.7000, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:45:31'),
(20, 'retail_drum_sticks', 1.9000, 1.0000, 23.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:45:48'),
(21, 'retail_chest_boneless', 2.2000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:46:27'),
(22, 'retail_thigh_boneless', 2.4000, 1.0000, 3.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 19:46:39'),
(23, 'retail_kalagi_pot_gardan', 1.0000, 1.0000, 0.0000, 0.0000, '2025-12-25 14:40:35', '2025-12-25 14:40:35');

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
(1, 1, 188560.70, 'credit', NULL, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(2, 2, 10608.90, 'credit', NULL, '2025-12-25 18:06:24', '2025-12-25 18:06:24');

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
(1, 1, 'Mix (No.37)', 30.000, 616.49, 18494.70, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(2, 1, 'Mix (No.36)', 30.000, 597.81, 17934.30, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(3, 1, 'Mix (No.35)', 30.000, 560.44, 16813.20, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(4, 1, 'Mix (No.34)', 30.000, 541.76, 16252.80, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(5, 1, 'Live', 30.000, 373.63, 11208.90, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(6, 1, 'Mix (No.37)', 30.000, 583.49, 17504.70, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(7, 1, 'Mix (No.36)', 30.000, 565.81, 16974.30, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(8, 1, 'Mix (No.35)', 50.000, 530.44, 26522.00, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(9, 1, 'Mix (No.34)', 50.000, 512.76, 25638.00, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(10, 1, 'live', 60.000, 353.63, 21217.80, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(11, 2, 'live', 30.000, 353.63, 10608.90, '2025-12-25 18:06:24', '2025-12-25 18:06:24');

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
(1, 'RANA POS', NULL, NULL, NULL, '2025-12-25 14:40:35', '2025-12-25 14:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `current_balance`, `created_at`, `updated_at`) VALUES
(1, 'WALEED POLTRY', NULL, NULL, 6851000.00, '2025-12-25 14:45:34', '2025-12-26 10:40:06');

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
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `supplier_id`, `customer_id`, `date`, `type`, `description`, `debit`, `credit`, `balance`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2025-12-25', 'purchase', 'Purchase #1 (Driver: )', 0.00, 1360680.00, 1360680.00, '2025-12-25 15:06:50', '2025-12-25 15:06:50'),
(2, NULL, 1, '2025-12-25', 'sale', 'Sale #1 (10 items)', 188560.70, 0.00, 188560.70, '2025-12-25 17:49:00', '2025-12-25 17:49:00'),
(3, NULL, 1, '2025-12-25', 'opening_balance', 'opening balance', 50000.00, 0.00, 238560.70, '2025-12-25 17:50:01', '2025-12-25 17:50:01'),
(4, NULL, 1, '2025-12-25', 'payment', 'cash dia', 0.00, 50000.00, 188560.70, '2025-12-25 17:50:32', '2025-12-25 17:50:32'),
(5, NULL, 2, '2025-12-25', 'sale', 'Sale #2 (1 items)', 10608.90, 0.00, 10608.90, '2025-12-25 18:06:24', '2025-12-25 18:06:24'),
(6, 1, NULL, '2025-12-25', 'purchase', 'Purchase #2 (Driver: )', 0.00, 1372580.00, 2733260.00, '2025-12-25 18:21:29', '2025-12-25 18:21:29'),
(7, 1, NULL, '2025-12-25', 'purchase', 'Purchase #3 (Driver: )', 0.00, 1372580.00, 4105840.00, '2025-12-25 18:37:09', '2025-12-25 18:37:09'),
(8, 1, NULL, '2025-12-25', 'purchase', 'Purchase #4 (Driver: )', 0.00, 1372580.00, 5478420.00, '2025-12-25 18:45:13', '2025-12-25 18:45:13'),
(9, 1, NULL, '2025-12-26', 'purchase', 'Purchase #5 (Driver: )', 0.00, 1372580.00, 6851000.00, '2025-12-26 10:40:06', '2025-12-26 10:40:06');

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
(1, 'Admin', 'admin@example.com', '2025-12-25 14:40:35', '$2y$10$qL5oEuNRhyUaFcZn8zD7je5mkjk4VoBwZ.c5qurGt/LbBd0eprR9S', NULL, NULL, 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2025-12-25 14:40:35', '2025-12-25 14:40:35', NULL);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rate_formulas_rate_key_unique` (`rate_key`) USING HASH;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `daily_rates`
--
ALTER TABLE `daily_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rate_formulas`
--
ALTER TABLE `rate_formulas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
