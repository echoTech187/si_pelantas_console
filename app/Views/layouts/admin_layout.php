<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - SI-PELANTAS Admin Console</title>
    <!-- CSRF Meta Tags for AJAX Requests -->
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash() ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        mint: {
                            50: '#F0FDF4',
                            100: '#DCFCE7',
                            200: '#BBF7D0',
                            300: '#86EFAC',
                            400: '#4ADE80',
                            500: '#10B981',
                            600: '#059669',
                            700: '#047857',
                        },
                        brand: {
                            sidebar: '#FFFFFF',
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Custom FluentDatatables Styling for Tailwind & Mint Theme */
        .dataTables_wrapper { position: relative; }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .dt-footer {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1.25rem;
            border-top: 1px solid #f1f5f9;
        }
        @media (min-width: 640px) {
            .dt-footer { flex-direction: row; }
        }
        .dt-footer-info {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
            text-align: center;
        }
        @media (min-width: 640px) {
            .dt-footer-info { text-align: left; }
        }
        .dt-footer-info strong {
            color: #0f172a;
            font-weight: 700;
        }
        .dt-pager-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .dt-pager-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.375rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            color: #334155;
            transition: all 0.15s ease;
        }
        .dt-pager-btn:hover:not(:disabled) {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }
        .dt-pager-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .dt-pager-numbers {
            display: none;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        @media (min-width: 640px) {
            .dt-pager-numbers {
                display: flex;
            }
        }
        .dt-pager-link {
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid transparent;
            background-color: transparent;
            color: #475569;
            transition: all 0.15s ease;
        }
        .dt-pager-link:hover:not(.active) {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .dt-pager-link.active {
            background-color: #55E5A4;
            color: #022c22;
            font-weight: 800;
            box-shadow: 0 1px 2px 0 rgba(16, 185, 129, 0.2);
        }
        .dt-pager-ellipsis {
            padding: 0 0.25rem;
            color: #94a3b8;
            font-size: 0.75rem;
        }
        .dt-processing-active {
            position: relative;
            opacity: 0.6;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .dt-loader-inner {
            position:absolute;
            bottom:0;
            left:0;
            right:0;
            top:0;
            z-index:9999;
            display: flex;
            align-items: center;
            justify-content:center;
            gap: 0.5rem;
            height:50px;
            max-width: 250px;
            margin: 20px auto;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            background-color: rgba(15, 23, 42, 0.9);
            color: #ffffff;
            font-size: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Custom Select2 Tailwind Theme */
        span.select2.select2-container {
            width: 100% !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            background-color: #f8fafc !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 0.85rem !important;
            transition: all 0.15s ease !important;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #10b981 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15) !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            color: #334155 !important;
            padding: 0 !important;
            line-height: normal !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
            right: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border: none !important;
            width: 14px !important;
            height: 14px !important;
            margin: 0 !important;
            top: auto !important;
            left: auto !important;
            position: relative !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-size: 14px !important;
            transition: transform 0.2s ease !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            transform: rotate(180deg) !important;
        }

        /* Dropdown Popup */
        .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.875rem !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
            margin-top: 3px !important;
            overflow: hidden !important;
            z-index: 99999 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .select2-search--dropdown {
            padding: 0.5rem 0.65rem 0.35rem 0.65rem !important;
            position: relative !important;
            display: block !important;
        }
        .select2-search--dropdown .select2-search__field {
            width: 100% !important;
            box-sizing: border-box !important;
            height: 34px !important;
            padding: 0.35rem 0.75rem 0.35rem 2rem !important;
            font-size: 0.75rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            color: #1e293b !important;
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.65rem !important;
            outline: none !important;
            transition: all 0.15s ease !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: 0.55rem center !important;
            background-size: 13px !important;
        }
        .select2-search--dropdown .select2-search__field:focus {
            border-color: #10b981 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15) !important;
        }
        .select2-results {
            padding: 0.25rem 0.35rem !important;
        }
        .select2-results__options {
            max-height: 220px !important;
            overflow-y: auto !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .select2-results__options::-webkit-scrollbar {
            width: 5px;
        }
        .select2-results__options::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .select2-results__option {
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            padding: 0.45rem 0.75rem !important;
            border-radius: 0.5rem !important;
            margin: 1px 0 !important;
            color: #334155 !important;
            cursor: pointer !important;
            transition: all 0.1s ease !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            font-weight: 600 !important;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
            background-color: #55E5A4 !important;
            color: #022c22 !important;
            font-weight: 700 !important;
        }
        .select2-container--default .select2-results__option[aria-disabled="true"] {
            color: #94a3b8 !important;
            background-color: transparent !important;
            cursor: not-allowed !important;
        }
    </style>
    <!-- Core JS: jQuery, Select2 & DataTables CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('assets/js/fluent-datatables.js') ?>"></script>
    <script>
        // Global Select2 Auto-Initializer
        $(document).ready(function() {
            function initAppSelect2() {
                $('select:not(.no-select2)').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        var optCount = $(this).find('option').length;
                        var placeholderText = $(this).find('option[value=""]').first().text().trim() || 'Pilih opsi...';
                        $(this).select2({
                            minimumResultsForSearch: optCount > 7 ? 0 : Infinity,
                            width: '100%',
                            placeholder: placeholderText,
                            allowClear: false,
                            language: {
                                noResults: function() {
                                    return "Tidak ada hasil yang cocok";
                                }
                            }
                        });
                    }
                });
            }
            initAppSelect2();
            $(document).ajaxComplete(function() {
                initAppSelect2();
            });
        });
    </script>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen flex flex-col lg:flex-row relative">

    <!-- MOBILE & TABLET BACKDROP OVERLAY -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden transition-opacity duration-300"></div>

    <!-- SIDEBAR DRAWER -->
    <aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white border-r border-slate-200/80 flex-shrink-0 flex flex-col justify-between h-full transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:w-64 lg:min-h-screen lg:shadow-none shadow-2xl">
        <div class="flex-1 overflow-y-auto">
            <!-- LOGO HEADER -->
            <div class="h-16 lg:h-20 flex items-center justify-between px-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-lg tracking-tight text-slate-900 block leading-tight">SI-PELANTAS</span>
                        <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Admin Console</span>
                    </div>
                </div>
                <!-- Close Button (Mobile / Tablet only) -->
                <button id="closeSidebarBtn" type="button" class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- NAVIGATION ITEMS -->
            <div class="px-4 py-5 space-y-1.5">
                <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Menu Utama
                </div>

                <!-- Dashboard -->
                <a href="<?= base_url('/') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'dashboard' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-house text-base <?= ($activeMenu ?? '') === 'dashboard' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Dashboard</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'dashboard'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <!-- Polantas & Kamsel Accounts -->
                <a href="<?= base_url('/police-accounts') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'police' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-user-shield text-base <?= ($activeMenu ?? '') === 'police' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Akun Polri (Kamsel)</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'police'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <!-- Admin Users Management -->
                <a href="<?= base_url('/admin-users') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'admin_users' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-users-gear text-base <?= ($activeMenu ?? '') === 'admin_users' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Akun Administrator</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'admin_users'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <!-- Schools & Districts -->
                <a href="<?= base_url('/schools') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'schools' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-school text-base <?= ($activeMenu ?? '') === 'schools' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Master Sekolah</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'schools'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <!-- School BK Accounts -->
                <a href="<?= base_url('/bk-accounts') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'bk' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chalkboard-user text-base <?= ($activeMenu ?? '') === 'bk' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Akun Guru BK</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'bk'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <!-- Violation Master Types -->
                <a href="<?= base_url('/violations') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'violations' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-scale-balanced text-base <?= ($activeMenu ?? '') === 'violations' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Pasal Pelanggaran</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'violations'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Audit & Monitoring
                </div>

                <!-- Inspections Log -->
                <a href="<?= base_url('/audit-logs/inspections') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'inspections' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-clipboard-check text-base <?= ($activeMenu ?? '') === 'inspections' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Log Sidak Kamsel</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'inspections'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>

                <!-- Violations Log -->
                <a href="<?= base_url('/audit-logs/violations') ?>" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($activeMenu ?? '') === 'violations_log' ? 'bg-[#55E5A4] text-slate-900 shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-base <?= ($activeMenu ?? '') === 'violations_log' ? 'text-slate-900' : 'text-slate-400' ?>"></i>
                        <span>Data Tilang Pelajar</span>
                    </div>
                    <?php if (($activeMenu ?? '') === 'violations_log'): ?>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- SIDEBAR FOOTER: USER PROFILE & LOGOUT -->
        <div class="p-3.5 m-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-900 to-slate-800 text-white flex items-center justify-center font-extrabold text-xs shadow-xs flex-shrink-0">
                    <?= strtoupper(substr(session()->get('admin_name') ?? 'AD', 0, 2)) ?>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-800 truncate" title="<?= esc(session()->get('admin_name') ?? 'Administrator') ?>">
                        <?= esc(session()->get('admin_name') ?? 'Administrator') ?>
                    </div>
                    <div class="text-[10px] text-emerald-600 font-semibold truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <?= esc(session()->get('admin_role') ?? 'SUPER_ADMIN') ?>
                    </div>
                </div>
            </div>
            <a href="<?= base_url('/logout') ?>" 
               title="Keluar dari Sistem (Logout)"
               class="p-2 rounded-xl bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200/80 hover:border-rose-200 transition shadow-xs flex-shrink-0">
                <i class="fa-solid fa-right-from-bracket text-xs"></i>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

        <!-- TOP BAR -->
        <header class="h-16 lg:h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 sticky top-0 z-30">
            <!-- Left: Mobile Toggle & Page Title -->
            <div class="flex items-center gap-3 min-w-0">
                <!-- Hamburger Button (< lg) -->
                <button id="mobileSidebarToggle" type="button" 
                        class="lg:hidden p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200/80 transition flex items-center justify-center flex-shrink-0"
                        title="Buka Navigasi">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>

                <div class="min-w-0">
                    <h1 class="text-base sm:text-lg lg:text-2xl font-extrabold text-slate-900 tracking-tight truncate">
                        <?= esc($pageTitle ?? 'Overview') ?>
                    </h1>
                </div>

                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/50 flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-[10px] mr-1.5 text-emerald-500"></i>
                    Presisi Mojokerto
                </span>
            </div>

            <!-- Right Controls: Date Pill Only -->
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <div class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl text-[11px] sm:text-xs font-semibold text-slate-600">
                    <i class="fa-regular fa-calendar text-slate-400"></i>
                    <span><?= date('d M Y') ?></span>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-5 sm:space-y-6 max-w-full min-w-0">

            <!-- FLASH ALERTS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm animate-fade-in">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                    <div class="flex-1">
                        <div class="font-bold text-sm">Berhasil!</div>
                        <div class="text-xs mt-0.5 text-emerald-700"><?= session()->getFlashdata('success') ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm animate-fade-in">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mt-0.5"></i>
                    <div class="flex-1">
                        <div class="font-bold text-sm">Terjadi Kesalahan!</div>
                        <div class="text-xs mt-0.5 text-rose-700"><?= session()->getFlashdata('error') ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- DYNAMIC VIEW INJECTION -->
            <?= $this->renderSection('content') ?>

        </main>
    </div>

    <!-- Responsive Sidebar Drawer Controller -->
    <script>
        $(document).ready(function() {
            var $sidebar = $('#appSidebar');
            var $backdrop = $('#sidebarBackdrop');

            function openSidebar() {
                $sidebar.removeClass('-translate-x-full').addClass('translate-x-0');
                $backdrop.removeClass('hidden');
                $('body').addClass('overflow-hidden lg:overflow-auto');
            }

            function closeSidebar() {
                $sidebar.addClass('-translate-x-full').removeClass('translate-x-0');
                $backdrop.addClass('hidden');
                $('body').removeClass('overflow-hidden lg:overflow-auto');
            }

            $('#mobileSidebarToggle').on('click', function(e) {
                e.stopPropagation();
                openSidebar();
            });

            $('#closeSidebarBtn, #sidebarBackdrop').on('click', function() {
                closeSidebar();
            });

            // Close when clicking nav links on mobile
            $('#appSidebar a').on('click', function() {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });

            // Close on Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });
    </script>

    <!-- PAGE SCRIPTS -->
    <?= $this->renderSection('scripts') ?>

</body>
</html>

