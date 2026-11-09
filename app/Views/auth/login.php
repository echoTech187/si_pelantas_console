<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - SI-PELANTAS Mojokerto</title>
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
                            500: '#10B981',
                            accent: '#55E5A4'
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex items-center justify-center p-4">

    <!-- LOGIN CONTAINER -->
    <div class="w-full max-w-md">

        <!-- LOGO & BRANDING -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white shadow-lg shadow-emerald-500/25 mb-4">
                <i class="fa-solid fa-shield-halved text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">SI-PELANTAS</h1>
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mt-0.5">Admin Console &bull; Satlantas Polres Mojokerto</p>
        </div>

        <!-- LOGIN CARD -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xl shadow-slate-200/50 space-y-5 sm:space-y-6">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Masuk ke Sistem</h2>
                <p class="text-xs text-slate-400 mt-1">Silakan masukkan username dan kata sandi administrator Anda.</p>
            </div>

            <!-- ALERT ERROR -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-3.5 bg-rose-50 border border-rose-200/80 rounded-xl flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5"></i>
                    <div class="text-xs text-rose-700 font-medium">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ALERT SUCCESS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="p-3.5 bg-emerald-50 border border-emerald-200/80 rounded-xl flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                    <div class="text-xs text-emerald-800 font-medium">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <form action="<?= base_url('/login/process') ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>

                <!-- USERNAME -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Username Administrator</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="username" value="<?= esc(old('username') ?: 'admin') ?>" required autofocus
                               placeholder="Masukkan username admin..."
                               class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="password" name="password" id="passwordInput" required
                               placeholder="Masukkan kata sandi..."
                               class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-medium">
                        <button type="button" onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1 focus:outline-none">
                            <i id="passwordToggleIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 bg-[#55E5A4] hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2">
                        <span>Masuk ke Konsol</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <!-- HINT KREDENSIAL DEFAULT -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-emerald-500 text-xs"></i>
                    <span>Default: <code class="font-bold text-slate-700">admin</code> / <code class="font-bold text-slate-700">admin123</code></span>
                </span>
                <span class="text-[10px] text-slate-400 font-mono">v1.0-Mojokerto</span>
            </div>
        </div>

        <!-- FOOTER INFO -->
        <div class="mt-8 text-center text-xs text-slate-400">
            <p>&copy; <?= date('Y') ?> Unit Kamsel Satlantas Polres Mojokerto</p>
            <p class="text-[10px] text-slate-400 mt-1">Sistem Integrasi Penanganan Lalu Lintas Pelajar & Evaluasi Sekolah</p>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('passwordToggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

