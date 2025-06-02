<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koverae POS Interface</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
            overflow-x: hidden;
        }
        a {
            color: #045054;
            text-decoration: none;
            transition: color 0.2s;
        }
        a:hover {
            color: #033a3f;
        }
        button, label {
            cursor: pointer;
        }

        /* Navbar */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            padding: 8px 16px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .navbar .navbar-brand img {
            height: 40px;
            width: auto;
        }
        .navbar .navbar-toggler {
            border: none;
            padding: 8px;
        }
        .navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23045054' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .navbar-nav .nav-link {
            font-size: 14px;
            font-weight: 600;
            color: #4b5563;
            padding: 8px 12px;
            position: relative;
        }
        .navbar-nav .nav-link:hover {
            color: #045054;
        }
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 12px;
            right: 12px;
            height: 2px;
            background-color: #045054;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .navbar-nav .nav-link:hover::after {
            opacity: 1;
        }
        .navbar .dropdown-menu {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 6px;
            margin-top: 8px;
        }
        .navbar .dropdown-item {
            font-size: 13px;
            padding: 8px 16px;
            color: #4b5563;
        }
        .navbar .dropdown-item:hover {
            background-color: #e6f2f3;
            color: #045054;
        }
        .navbar .avatar {
            width: 32px;
            height: 32px;
            background-size: cover;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
        }
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #ffffff;
                padding: 16px;
                border-top: 1px solid #e5e7eb;
            }
            .navbar-nav {
                margin-bottom: 16px;
            }
            .navbar-nav .nav-link {
                padding: 8px 0;
            }
            .navbar-nav.ms-auto {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar .dropdown-menu {
                box-shadow: none;
                border: 1px solid #e5e7eb;
                width: 100%;
                margin-top: 4px;
            }
            .navbar .avatar {
                margin-right: 8px;
            }
            .navbar .nav-link .ms-2 {
                display: inline !important;
            }
        }

        /* Main Content */
        .main {
            margin-top: 64px; /* Navbar height */
            padding: 16px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Search Bar */
        .search-bar {
            position: sticky;
            top: 64px;
            background-color: #ffffff;
            padding: 8px;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 12px;
            z-index: 800;
        }
        .search-bar input {
            width: 100%;
            padding: 10px 32px 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #045054;
        }
        .search-bar .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 16px;
        }

        /* Categories */
        .category_section_buttons {
            height: 48px;
            margin-bottom: 12px;
        }
        .section_buttons {
            overflow-x: auto;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .section_buttons::-webkit-scrollbar {
            display: none;
        }
        .category_button {
            font-weight: 500;
            height: 100%;
            padding: 8px 16px;
            margin: 0 4px;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 13px;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s, color 0.2s;
        }
        .category_button:hover {
            background-color: #045054;
            color: #ffffff;
            border-color: #045054;
        }
        .category_button i {
            font-size: 18px;
        }

        /* Product List */
        .product-list {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
            margin-bottom: 16px;
        }
        .product {
            background-color: #ffffff;
            border: 1px solid #f9fafb;
            border-radius: 6px;
            width: auto;
            max-width: 165px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            transition: transform 0.2s, border 0.2s;
            cursor: pointer;
        }
        .product:hover {
            transform: scale(1.03);
            border-color: #045054;
        }
        .product img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }
        .product-content {
            padding: 8px;
        }
        .product-name {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .price-tag {
            font-size: 13px;
            font-weight: 700;
            color: #045054;
            margin-top: 4px;
        }
        .badge-info {
            position: absolute;
            top: 8px;
            left: 8px;
            background-color: #e5e7eb;
            color: #1f2937;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .product-information-tag {
            position: absolute;
            top: 8px;
            right: 8px;
            color: #6b7280;
            font-size: 14px;
        }

        /* Checkout (Preserved) */
        .order-container-bg-view {
            height: 170px;
            min-height: auto;
            min-width: auto;
            background-color: #ffffff;
        }
        .order-container-bg-view ul {
            list-style: none;
            padding-left: 0;
        }
        .order-container-bg-view .orderline {
            text-align: left;
            height: auto;
            max-height: 100px;
            padding: 8px 8px 8px 8px;
            text-decoration: none;
            min-height: auto;
            min-width: auto;
            display: list-item;
        }
        .order-container-bg-view::-webkit-scrollbar {
            width: 3px;
        }
        .order-container-bg-view::-webkit-scrollbar-thumb:hover {
            background: #03565b;
        }
        .orderline.selected {
            background-color: #E6F2F3;
        }
        .orderline .product-name {
            text-align: left;
            white-space: nowrap;
            padding: 0 4px 0 0;
            height: 16px;
            min-height: auto;
            min-width: auto;
        }
        .orderline .product-price {
            text-align: right;
            height: 16px;
            width: 44px;
            display: block;
            min-height: auto;
            min-width: auto;
        }
        .price-per-unit {
            height: 17px;
            width: 80%;
            margin-left: 10px;
            display: list-item;
        }
        .order-summary {
            background-color: #F9FAFB;
            height: auto;
            max-height: auto;
            padding: 8px 16px 8px 16px;
            min-height: auto;
            min-width: auto;
        }
        .order-summary .subentry {
            font-size: 14px;
        }
        .empty-cart {
            text-align: center;
            height: 150px;
            background-color: #e7e7e7;
            padding: 16px 24px 16px 24px;
        }
        .empty-cart i {
            font-size: 56px;
            line-height: 56px;
            color: #737373;
            height: 56px;
            width: 52px;
        }
        .empty-cart h3 {
            font-size: 15px;
            font-weight: 500;
            margin: 8px 0 8px 0;
        }
        #cart-body {
            padding: 0;
        }
        .control_buttons {
            font-size: 14px;
            line-height: 21px;
            white-space: nowrap;
            word-spacing: 0px;
            background-color: #d8dadd;
            height: auto;
            border-top: 1px solid #d8dadd;
            border-bottom: 1px solid #d8dadd;
        }
        .control_buttons .k_price_list_button,
        .control_buttons .btn {
            background-color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            line-height: 21px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            width: 32.7%;
            border: 1px solid #e7e9ed;
            padding: 8px;
            margin: 0 1px 2px 1px;
            min-height: auto;
            min-width: auto;
        }
        .control_buttons #reset-cart {
            width: 100%;
        }
        .calculator_buttons {
            font-size: 14px;
            line-height: 21px;
            word-spacing: 0px;
            background-color: #D8DADD;
            height: auto;
            border-top: 1px solid #D8DADD;
            border-bottom: 1px solid #D8DADD;
            min-height: auto;
            min-width: auto;
        }
        #vertical_buttons .btn {
            width: 100%;
        }
        .calculator_buttons .btn {
            font-size: 14px;
            font-weight: 700;
            line-height: 21px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            height: 65px;
            width: 24.3%;
            border: 1px solid #E7E9ED;
            padding: 8px;
            margin: 0 1px 2px 1px;
            min-height: auto;
            min-width: auto;
        }
        .calculator_buttons .btn.selected {
            background-color: #e0edef;
            border: 1px solid #017E84;
        }
        .calculator_buttons #pay {
            background-color: #03565b;
            color: white;
        }
        .calculator_buttons #pay:hover {
            background-color: #044145;
            color: white;
        }
        .calculator_buttons .k_price_list_button[style*="background-color: #F5D976"] {
            background-color: #F5D976 !important;
        }
        .fixed-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #333;
            color: white;
            margin-top: 30px;
            z-index: 100;
            display: flex;
        }
        .fixed-bar .btn-switch_pane {
            text-align: center;
            vertical-align: middle;
            background-color: #f1f3f4;
            height: 58px;
            width: 50%;
            padding: 5px 10px;
            min-width: auto;
        }
        .fixed-bar .btn-switch_pane:hover {
            background-color: #ffffff;
        }
        .fixed-bar #pay-order {
            background-color: #026d72;
        }
        .fixed-bar #pay-order:hover {
            background-color: #035d62;
        }

        /* Responsive Design */
        @media screen and (max-width: 1024px) {
            .product-list {
                --bs-columns: 4;
            }
        }
        @media screen and (max-width: 768px) {
            .product-list {
                --bs-columns: 3;
            }
            .main {
                padding: 12px;
            }
            .navbar {
                padding: 8px 12px;
            }
            .navbar .navbar-brand img {
                height: 32px;
            }
            .category_section_buttons {
                height: auto;
            }
            .category_button {
                padding: 6px 12px;
                margin: 4px;
            }
        }
        @media screen and (max-width: 507px) {
            .product-list {
                --bs-columns: 2;
            }
            .product-name,
            .price-tag {
                font-size: 12px;
            }
            .main {
                padding: 8px;
            }
        }
        @media screen and (max-width: 380px) {
            .product-name,
            .price-tag {
                font-size: 11px;
            }
        }
        @media (min-width: 990px) {
            .fixed-bar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="bg-white navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/images/logo/logo-black.png') }}" alt="Ndako Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="mb-2 navbar-nav me-auto mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Products</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar" style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAABT0lEQVR4nO2VsUoDQRSGv4lYiI2Fpa1E7AIWIgpWFiKIrTVY2NhZiCDoL6DY+AOksLHyA7SwsLGyEAsLC0v3nJkzZ4Y38+7OOWfO4aQNAMeAnQCOAbgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuALgD4A6AOwDuAPg/wG8Ay9hK6vWAAAAAElFTkSuQmCC')"></span>
                            <span class="ms-2 d-none d-lg-inline">John Doe</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">Documentation</a></li>
                            <li><a class="dropdown-item" href="#">Support</a></li>
                            <li><a class="dropdown-item" href="#">Dark Mode</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Logout</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" aria-label="Translate">
                            <i class="bi bi-translate" style="font-size: 16px;"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main">
        @yield("content")
    </main>
</body>
</html>
