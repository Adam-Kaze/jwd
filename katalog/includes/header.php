<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>DKV ROOM - Studio Kreatif · Desain · Ilustrasi · Foto · Video</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Playfair+Display:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* ========================================== */
        /* DKV ROOM - HEADER DARK THEME               */
        /* ========================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0b0b;
            color: #f5f5f5;
        }
        .app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* ========================================== */
        /* TOP HEADER - DKV ROOM DARK STYLE          */
        /* ========================================== */
        .top-header {
            background: rgba(11, 11, 11, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(212, 175, 55, 0.12);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
        }
        
        /* --- LOGO DKV ROOM DARK --- */
        .logo-area {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: #f5f5f5;
            transition: all 0.3s ease;
        }
        .logo-area:hover {
            transform: scale(1.02);
        }
        .logo-area .logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 14px;
            background: linear-gradient(135deg, #d4af37, #f5d77b);
            padding: 6px;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.25);
        }
        .logo-area i {
            color: #d4af37;
            font-size: 1.8rem;
        }
        .logo-area .highlight { 
            color: #d4af37;
        }
        .logo-area .logo-sub {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }
        .logo-area .logo-sub small {
            font-family: 'Inter', sans-serif;
            font-size: 0.55rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #d4af37;
            opacity: 0.7;
        }

        .mobile-menu-btn {
            display: none;
            font-size: 1.2rem;
            cursor: pointer;
            background: rgba(212, 175, 55, 0.08);
            padding: 0.5rem 0.9rem;
            border-radius: 12px;
            color: #f5f5f5;
            transition: all 0.2s;
            border: 1px solid rgba(212, 175, 55, 0.08);
        }
        .mobile-menu-btn:hover {
            background: rgba(212, 175, 55, 0.15);
        }
        
        .header-actions {
            display: flex;
            gap: 1.5rem;
            font-size: 1.2rem;
            cursor: pointer;
            color: #a0a0a0;
            align-items: center;
        }
        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 10px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: rgba(212, 175, 55, 0.75);
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
            white-space: nowrap;
        }
        .btn-back-home:hover {
            background: rgba(212, 175, 55, 0.08);
            border-color: rgba(212, 175, 55, 0.5);
            color: #d4af37;
            transform: translateY(-1px);
        }
        @media (max-width: 767px) {
            .btn-back-home span { display: none; }
            .btn-back-home { padding: 6px 10px; }
        }
        .header-actions i {
            transition: all 0.2s;
        }
        .header-actions i:hover {
            color: #d4af37;
            transform: translateY(-2px);
        }
        
        /* Search Container - Dark */
        .search-container {
            position: relative;
        }
        .search-input {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 10px;
            width: 300px;
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(212, 175, 55, 0.12);
            border-radius: 16px;
            padding: 0.5rem 1rem;
            display: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            z-index: 101;
        }
        .search-input.active {
            display: block;
        }
        .search-input input {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid rgba(212, 175, 55, 0.12);
            border-radius: 40px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.2s;
            background: rgba(255,255,255,0.04);
            color: #f5f5f5;
        }
        .search-input input::placeholder {
            color: #666;
        }
        .search-input input:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.08);
        }
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 8px;
            border-top: 1px solid rgba(212, 175, 55, 0.06);
        }
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            text-decoration: none;
            color: #f5f5f5;
            border-bottom: 1px solid rgba(212, 175, 55, 0.04);
            transition: background 0.2s;
            border-radius: 8px;
        }
        .search-result-item:hover {
            background: rgba(212, 175, 55, 0.06);
        }
        .search-result-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 10px;
            background: #1a1a1a;
        }
        .search-result-info {
            flex: 1;
        }
        .search-result-name {
            font-weight: 600;
            font-size: 0.85rem;
        }
        .search-result-price {
            font-size: 0.7rem;
            color: #d4af37;
            font-weight: 600;
        }
        .search-no-result {
            padding: 15px;
            text-align: center;
            color: #666;
            font-size: 0.8rem;
        }
        
        /* Cart & History Button Links - Dark */
        .cart-link, .history-link {
            position: relative;
            color: #a0a0a0;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        .cart-link:hover, .history-link:hover {
            color: #d4af37;
            transform: translateY(-2px);
        }
        .cart-badge {
            position: absolute;
            top: -10px;
            right: -14px;
            background: #d4af37;
            color: #0b0b0b;
            font-size: 0.55rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 30px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.25);
        }
        
        /* ========================================== */
        /* LAYOUT - DKV ROOM DARK                     */
        /* ========================================== */
        .layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            padding: 0.75rem 2rem 2rem;
            flex: 1;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Sidebar - Dark */
        .sidebar {
            background: rgba(20, 20, 20, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 0 0 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            height: fit-content;
            position: sticky;
            top: 90px;
            border: 1px solid rgba(212, 175, 55, 0.06);
        }
        .sidebar-header {
            font-weight: 700;
            padding: 1rem 1.5rem 1rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.06);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f5f5f5;
            font-family: 'Playfair Display', serif;
        }
        .sidebar-header i {
            color: #d4af37;
            font-size: 1.1rem;
        }
        .category-list {
            list-style: none;
            padding: 0.5rem 0;
        }
        .category-item {
            padding: 0.7rem 1.5rem;
            margin: 0.2rem 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #a0a0a0;
            transition: all 0.2s;
            position: relative;
        }
        .category-item a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        .category-item i { 
            width: 24px; 
            font-size: 1rem;
            color: #666;
        }
        .category-item.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.06) 0%, transparent 100%);
            color: #f5f5f5;
        }
        .category-item.active i {
            color: #d4af37;
        }
        .category-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #d4af37;
            border-radius: 0 3px 3px 0;
        }
        .category-item:hover:not(.active) { 
            background: rgba(212, 175, 55, 0.04);
            color: #f5f5f5;
        }
        .category-item:hover:not(.active) i {
            color: #d4af37;
        }
        .count {
            margin-left: auto;
            font-size: 0.65rem;
            background: rgba(212, 175, 55, 0.08);
            padding: 2px 8px;
            border-radius: 30px;
            color: #a0a0a0;
            font-weight: 500;
        }
        .sidebar-footer {
            margin-top: 2rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(212, 175, 55, 0.06);
            font-size: 0.7rem;
            color: #666;
        }
        .sidebar-footer strong {
            color: #d4af37;
        }
        
        .main-content { 
            background: transparent; 
        }
        
        .products-header { 
            margin-top: 1rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .products-header h2 { 
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem; 
            font-weight: 700;
            color: #f5f5f5;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        .products-header h2 i {
            color: #d4af37;
            margin-right: 8px;
        }
        .products-header p {
            color: #a0a0a0;
            font-size: 0.95rem;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.8rem;
        }
        
        /* Product Card - DKV ROOM Dark Style */
        .product-card {
            background: rgba(20, 20, 20, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
            border: 1px solid rgba(212, 175, 55, 0.06);
            position: relative;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37, #f5d77b);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
            z-index: 2;
        }
        
        .product-card:hover::before {
            transform: scaleX(1);
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px rgba(212, 175, 55, 0.06);
            border-color: rgba(212, 175, 55, 0.12);
        }
        
        .product-img-wrapper {
            overflow: hidden;
            position: relative;
            background: #0b0b0b;
        }
        
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
            filter: brightness(0.85);
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
            filter: brightness(1);
        }
        
        .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(135deg, #d4af37, #f5d77b);
            color: #0b0b0b;
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 0.65rem;
            font-weight: 700;
            z-index: 2;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
            letter-spacing: 0.3px;
        }
        
        .product-body { 
            padding: 1.2rem 1.2rem 1.2rem;
            position: relative;
        }
        
        .product-category {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #d4af37;
            background: rgba(212, 175, 55, 0.06);
            padding: 4px 14px;
            border-radius: 30px;
            margin-bottom: 0.8rem;
            border: 1px solid rgba(212, 175, 55, 0.06);
        }
        
        .product-title { 
            font-size: 1.1rem; 
            font-weight: 700; 
            margin-bottom: 0.6rem;
            color: #f5f5f5;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            display: box;
            line-clamp: 2;
            box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s;
        }
        
        .product-card:hover .product-title {
            color: #f5f5f5;
        }
        
        .product-desc { 
            font-size: 0.8rem; 
            color: #a0a0a0; 
            margin-bottom: 1rem; 
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            display: box;
            line-clamp: 2;
            box-orient: vertical;
            overflow: hidden;
        }
        
        .product-price { 
            font-weight: 800; 
            color: #d4af37; 
            font-size: 1.2rem; 
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
        }
        
        /* Tombol Container */
        .product-buttons {
            display: flex;
            gap: 0.8rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        
        /* Tombol Detail - Dark */
        .btn-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.04);
            color: #f5f5f5;
            padding: 0.6rem 0.8rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-align: center;
            flex: 1;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        
        .btn-detail i {
            font-size: 0.7rem;
        }
        
        .btn-detail:hover {
            background: rgba(212, 175, 55, 0.08);
            border-color: #d4af37;
            color: #f5f5f5;
            transform: translateY(-2px);
        }
        
        /* Tombol Simpan - Dark */
        .btn-cart {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.04);
            color: #a0a0a0;
            padding: 0.6rem 0.8rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-align: center;
            flex: 1;
            border: 2px solid rgba(255, 255, 255, 0.06);
            cursor: pointer;
            font-family: inherit;
        }
        
        .btn-cart i {
            font-size: 0.7rem;
        }
        
        .btn-cart:hover:not(:disabled) {
            background: rgba(212, 175, 55, 0.08);
            border-color: #d4af37;
            color: #d4af37;
            transform: translateY(-2px);
        }
        
        .btn-cart:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Tombol Konsultasi WhatsApp */
        .btn-wa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: linear-gradient(135deg, #25D366, #1da85e);
            color: #0b0b0b;
            padding: 0.6rem 0.8rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-align: center;
            flex: 1;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.2);
        }
        
        .btn-wa i {
            font-size: 0.7rem;
        }
        
        .btn-wa:hover {
            background: linear-gradient(135deg, #1da85e, #158a4d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }
        
        /* Toast Notification - DKV ROOM Dark Style */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #d4af37;
            color: #0b0b0b;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            z-index: 1000;
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.25);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .toast-notification i {
            color: #0b0b0b;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* ========================================== */
        /* MAIN FOOTER - DKV ROOM DARK                */
        /* ========================================== */
        .main-footer {
            background: linear-gradient(160deg, #0b0b0b, #161616);
            color: rgba(255, 255, 255, 0.3);
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            margin-top: 2rem;
            border-top: 2px solid #d4af37;
        }
        
        .main-footer strong {
            color: #d4af37;
        }
        
        /* ========== RESPONSIVE ========== */
        
        @media (min-width: 768px) and (max-width: 1024px) {
            .layout { 
                grid-template-columns: 240px 1fr; 
                gap: 1.5rem;
                padding: 1.5rem;
            }
            .products-grid { 
                grid-template-columns: repeat(2, 1fr);
                gap: 1.2rem;
            }
        }
        
        @media (max-width: 767px) {
            .top-header {
                padding: 0.6rem 1rem;
            }
            .logo-area {
                font-size: 1.1rem;
            }
            .logo-area .logo-img {
                width: 32px;
                height: 32px;
            }
            .logo-area i {
                font-size: 1.3rem;
            }
            .mobile-menu-btn { display: block; }
            .layout { 
                grid-template-columns: 1fr; 
                padding: 1rem; 
                gap: 1rem;
            }
            .sidebar {
                position: fixed;
                top: 60px;
                left: -300px;
                width: 280px;
                height: calc(100% - 60px);
                z-index: 999;
                border-radius: 0 20px 20px 0;
                transition: left 0.3s ease;
                overflow-y: auto;
                background: rgba(11, 11, 11, 0.95);
                backdrop-filter: blur(16px);
                box-shadow: 5px 0 30px rgba(0, 0, 0, 0.5);
            }
            .sidebar.open { left: 0; }
            
            .search-input {
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                width: 100%;
                border-radius: 0;
                margin-top: 0;
                background: rgba(11, 11, 11, 0.98);
            }
            
            .products-grid { 
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }
            
            .product-img {
                height: 130px;
            }
            .product-body {
                padding: 0.8rem;
            }
            .product-title {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }
            .product-desc {
                font-size: 0.65rem;
                margin-bottom: 0.5rem;
            }
            .product-price {
                font-size: 0.9rem;
                margin-bottom: 0.6rem;
            }
            .product-buttons {
                gap: 0.4rem;
            }
            .btn-detail, .btn-cart, .btn-wa {
                padding: 0.4rem 0.5rem;
                font-size: 0.6rem;
            }
            .btn-detail i, .btn-cart i, .btn-wa i {
                font-size: 0.55rem;
            }
            .products-header h2 {
                font-size: 1.3rem;
            }
        }
        
        @media (max-width: 480px) {
            .products-grid { 
                gap: 0.6rem;
            }
            .product-img {
                height: 110px;
            }
            .product-body {
                padding: 0.6rem;
            }
            .product-title {
                font-size: 0.75rem;
            }
            .product-price {
                font-size: 0.8rem;
            }
            .btn-detail, .btn-cart, .btn-wa {
                padding: 0.3rem 0.4rem;
                font-size: 0.55rem;
            }
        }
        
        @media (min-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (min-width: 1800px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        /* Overlay untuk mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 998;
        }
        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="app">
    <header class="top-header">
        <?php 
        // --- AMBIL DATA LOGO & NAMA DARI DATABASE (TETAP SAMA, HANYA TAMPILAN YANG DIUBAH) ---
        $logo_url = ''; 
        $site_name = 'DKV ROOM'; // Default tampilan DKV ROOM
        
        if (isset($conn)) {
            $stmt_logo = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
            
            $key_logo = 'site_logo';
            mysqli_stmt_bind_param($stmt_logo, "s", $key_logo);
            mysqli_stmt_execute($stmt_logo);
            $result_logo = mysqli_stmt_get_result($stmt_logo);
            if ($result_logo && mysqli_num_rows($result_logo) > 0) {
                $row_logo = mysqli_fetch_assoc($result_logo);
                $logo_url = $row_logo['setting_value'];
            }
            mysqli_stmt_close($stmt_logo);

            // Ambil Nama (tetap menggunakan data dari database, tapi tampilan diubah)
            $stmt_name = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
            $key_name = 'site_name';
            mysqli_stmt_bind_param($stmt_name, "s", $key_name);
            mysqli_stmt_execute($stmt_name);
            $result_name = mysqli_stmt_get_result($stmt_name);
            if ($result_name && mysqli_num_rows($result_name) > 0) {
                $row_name = mysqli_fetch_assoc($result_name);
                if (!empty($row_name['setting_value'])) {
                    $site_name = $row_name['setting_value'];
                }
            }
            mysqli_stmt_close($stmt_name);
        }
        ?>

        <!-- LOGO DKV ROOM - TAMPILAN DARK -->
        <a href="<?= SITE_URL ?? './' ?>" class="logo-area">
            <?php if (!empty($logo_url)): ?>
                <img src="<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($site_name) ?>" class="logo-img">
            <?php else: ?>
                <i class="fa-regular fa-gem"></i>
            <?php endif; ?>
            
            <span class="logo-sub">
                <span>DKV ROOM</span>
                <small>Desain · Ilustrasi · Foto · Video</small>
            </span>
        </a>

        <div class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </div>
        <div class="header-actions">
            <a href="../index.php" class="btn-back-home" title="Kembali ke Landing Page">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>

            <div class="search-container">
                <i class="fas fa-search" id="searchIcon"></i>
                <div class="search-input" id="searchInput">
                    <input type="text" id="searchKeyword" placeholder="Cari karya kreatif...">
                    <div id="searchResults" class="search-results"></div>
                </div>
            </div>
            
            <a href="<?= defined('SITE_URL') ? SITE_URL : '../' ?>cart/index.php" class="cart-link" title="Koleksi Simpanan Saya">
                <i class="fa-regular fa-star"></i>
                <span class="cart-badge" id="cartCount">0</span>
            </a>

            <a href="<?= defined('SITE_URL') ? SITE_URL : '../' ?>history/index.php" class="history-link" title="Riwayat Konsultasi">
                <i class="fas fa-clock-rotate-left"></i>
                <span class="cart-badge" id="historyCount"><?= isset($_SESSION['history']) ? count($_SESSION['history']) : 0 ?></span>
            </a>
        </div>
    </header>
    <div class="layout">