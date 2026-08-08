-- ============================================================
-- 1. INSERT DATA USER (ADMIN)
-- ============================================================
-- Password: admin123 (sudah di-hash menggunakan password_hash)
INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- ============================================================
-- 2. INSERT DATA KATEGORI
-- ============================================================
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Sertifikasi Kompetensi', NOW()),
(2, 'Pelatihan Profesional', NOW()),
(3, 'Konsultasi Bisnis', NOW()),
(4, 'Digital Marketing', NOW()),
(5, 'Manajemen SDM', NOW());

-- ============================================================
-- 3. INSERT DATA PRODUK
-- ============================================================
INSERT INTO `products` (`id`, `name`, `category_id`, `description`, `price`, `demo_link`, `image_url`, `wa_number`, `created_at`) VALUES
(1, 'Sertifikasi Ahli K3 Umum', 1, 'Program sertifikasi resmi untuk Ahli Keselamatan dan Kesehatan Kerja (K3) Umum yang diakui secara nasional.', 'Rp 3.500.000', '', 'https://placehold.co/400x300/1f2462/white?text=Sertifikasi+K3', '', NOW()),
(2, 'Pelatihan Digital Marketing Expert', 4, 'Pelatihan intensif digital marketing dari dasar hingga mahir, mencakup SEO, SEM, dan Social Media Ads.', 'Rp 2.750.000', 'demo/digital-marketing', 'https://placehold.co/400x300/1f2462/white?text=Digital+Marketing', '', NOW()),
(3, 'Konsultasi Bisnis & Strategi', 3, 'Pendampingan dan konsultasi strategi bisnis untuk UMKM dan korporasi. Dapatkan roadmap bisnis yang terukur.', 'Rp 5.000.000', '', 'https://placehold.co/400x300/1f2462/white?text=Konsultasi+Bisnis', '', NOW()),
(4, 'Sertifikasi Manajemen SDM', 5, 'Sertifikasi profesional di bidang Manajemen Sumber Daya Manusia (MSDM) untuk meningkatkan kompetensi HR.', 'Rp 4.200.000', '', 'https://placehold.co/400x300/1f2462/white?text=Manajemen+SDM', '', NOW()),
(5, 'Pelatihan Kepemimpinan & Coaching', 2, 'Pelatihan kepemimpinan tingkat lanjut dengan metode coaching untuk membangun tim yang solid dan produktif.', 'Rp 1.950.000', 'demo/leadership', 'https://placehold.co/400x300/1f2462/white?text=Leadership+Coaching', '', NOW());

-- ============================================================
-- 4. INSERT DATA SETTINGS (FRONTEND CONFIGURATION + CTA)
-- ============================================================
INSERT IGNORE INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_logo', '', NOW()),
(2, 'site_name', 'LSP COACHPRO INDONESIA', NOW()),
(3, 'wa_number', '6281383796300', NOW()),
(4, 'footer_copyright', 'LSP COACHPRO INDONESIA – Lembaga Sertifikasi Profesi Terakreditasi', NOW()),
(5, 'footer_credit_left', '<i class="fas fa-mobile-alt"></i> Mobile Friendly', NOW()),
(6, 'footer_credit_right', '<i class="fas fa-tachometer-alt"></i> Kinerja Cepat', NOW()),
-- ============================================================
-- TAMBAHAN DATA CTA (SMART PROMO FLOATING CARD)
-- ============================================================
(7, 'cta_title', '💡 Konsultasi Gratis!', NOW()),
(8, 'cta_message', 'Butuh bantuan memilih program sertifikasi yang tepat untuk karir Anda? Tim kami siap membantu 24/7!', NOW()),
(9, 'cta_emoji', '🎁', NOW()),
(10, 'cta_btn_text', 'Hubungi Sekarang →', NOW()),
(11, 'cta_wa_text', 'Halo, saya butuh konsultasi mengenai program sertifikasi LSP COACHPRO.', NOW()),
(12, 'cta_footer_text', '⭐ 500+ klien puas | ⚡ Respon cepat | 🎯 Garansi 30 hari', NOW());