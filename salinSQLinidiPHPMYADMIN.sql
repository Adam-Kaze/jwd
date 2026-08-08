-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 07 Agu 2026 pada 14.46
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
-- Database: `latihan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Sertifikasi Kompetensi', '2026-08-03 17:04:49'),
(2, 'Pelatihan Profesional', '2026-08-03 17:04:49'),
(3, 'Konsultasi Bisnis', '2026-08-03 17:04:49'),
(4, 'Digital Marketing', '2026-08-03 17:04:49'),
(5, 'Manajemen SDM', '2026-08-03 17:04:49'),
(6, 'Test Kategori', '2026-08-05 12:56:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `client_phone` varchar(20) DEFAULT NULL,
  `client_address` text DEFAULT NULL,
  `service_name` varchar(200) NOT NULL,
  `service_description` text DEFAULT NULL,
  `guide_content` longtext DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','paid','overdue') NOT NULL DEFAULT 'draft',
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `unique_link` varchar(50) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `demo_link` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `wa_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `description`, `price`, `demo_link`, `image_url`, `wa_number`, `created_at`) VALUES
(1, 'Sertifikasi Ahli K3 Umum', 1, 'Program sertifikasi resmi untuk Ahli Keselamatan dan Kesehatan Kerja (K3) Umum yang diakui secara nasional.', 'Rp 3.500.000', '', 'https://placehold.co/400x300/1f2462/white?text=Sertifikasi+K3', '', '2026-08-03 17:04:49'),
(2, 'Pelatihan Digital Marketing Expert', 4, 'Pelatihan intensif digital marketing dari dasar hingga mahir, mencakup SEO, SEM, dan Social Media Ads.', 'Rp 2.750.000', 'demo/digital-marketing', 'https://placehold.co/400x300/1f2462/white?text=Digital+Marketing', '', '2026-08-03 17:04:49'),
(3, 'Konsultasi Bisnis & Strategi', 3, 'Pendampingan dan konsultasi strategi bisnis untuk UMKM dan korporasi. Dapatkan roadmap bisnis yang terukur.', 'Rp 5.000.000', '', 'https://placehold.co/400x300/1f2462/white?text=Konsultasi+Bisnis', '', '2026-08-03 17:04:49'),
(4, 'Sertifikasi Manajemen SDM', 5, 'Sertifikasi profesional di bidang Manajemen Sumber Daya Manusia (MSDM) untuk meningkatkan kompetensi HR.', 'Rp 4.200.000', '', 'https://placehold.co/400x300/1f2462/white?text=Manajemen+SDM', '', '2026-08-03 17:04:49'),
(5, 'Pelatihan Kepemimpinan & Coaching', 2, 'Pelatihan kepemimpinan tingkat lanjut dengan metode coaching untuk membangun tim yang solid dan produktif.', 'Rp 1.950.000', 'demo/leadership', 'https://placehold.co/400x300/1f2462/white?text=Leadership+Coaching', '', '2026-08-03 17:04:49'),
(6, 'Test Produk', 6, '', '500000', '', 'uploads/1785947326_9518.png', '6283170623532', '2026-08-05 13:18:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_logo', '', '2026-08-03 17:04:50'),
(2, 'site_name', 'LSP COACHPRO INDONESIA', '2026-08-03 17:04:50'),
(3, 'wa_number', '6281383796300', '2026-08-03 17:04:50'),
(4, 'footer_copyright', 'LSP COACHPRO INDONESIA – Lembaga Sertifikasi Profesi Terakreditasi', '2026-08-03 17:04:50'),
(5, 'footer_credit_left', '<i class=\"fas fa-mobile-alt\"></i> Mobile Friendly', '2026-08-03 17:04:50'),
(6, 'footer_credit_right', '<i class=\"fas fa-tachometer-alt\"></i> Kinerja Cepat', '2026-08-03 17:04:50'),
(7, 'cta_title', '💡 Konsultasi Gratis!', '2026-08-03 17:28:39'),
(8, 'cta_message', 'Butuh bantuan memilih program sertifikasi yang tepat untuk karir Anda? Tim kami siap membantu 24/7!', '2026-08-03 17:28:39'),
(9, 'cta_emoji', '🎁', '2026-08-03 17:28:39'),
(10, 'cta_btn_text', 'Hubungi Sekarang →', '2026-08-03 17:28:39'),
(11, 'cta_wa_text', 'Halo, saya butuh konsultasi mengenai program sertifikasi LSP COACHPRO.', '2026-08-03 17:28:39'),
(12, 'cta_footer_text', '⭐ 500+ klien puas | ⚡ Respon cepat | 🎯 Garansi 30 hari', '2026-08-03 17:28:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2a$12$WYVrEWoNyVIFbYvUqLZiweVcyEBpV.KaBpIQ/tYhCn/jm16B3lgU6', '2026-08-03 17:04:49');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD UNIQUE KEY `unique_link` (`unique_link`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
