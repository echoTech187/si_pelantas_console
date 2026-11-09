<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="mx-auto space-y-6">
    <!-- BACK LINK -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('/admin-users') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Administrator</span>
        </a>
    </div>

    <!-- CREATE FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Akun Administrator Baru</h2>
                    <p class="text-xs text-slate-400">Pembuatan akun admin baru untuk mengelola konsol SI-PELANTAS Mojokerto.</p>
                </div>
            </div>
            <span class="text-[11px] font-bold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg w-fit">Wajib Diisi Lengkap</span>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="m-4 sm:m-6 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                <div class="flex items-center gap-2 text-rose-700 font-bold text-xs mb-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Terdapat beberapa kesalahan pengisian form:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] text-rose-600 space-y-0.5">
                    <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/admin-users/store') ?>" method="post" class="p-4 sm:p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- INFORMASI ADMINISTRATOR -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-500"></i>
                    <span>Profil Administrator</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Username Login <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">@</span>
                            <input type="text" name="username" value="<?= old('username') ?>" required
                                   placeholder="Contoh: admin_kamsel"
                                   class="w-full pl-8 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Minimal 4 karakter, huruf kecil, angka, atau underscore.</span>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Administrator <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="<?= old('full_name') ?>" required
                               placeholder="Contoh: Bripka Heru Santoso"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Administrator</label>
                        <input type="email" name="email" value="<?= old('email') ?>"
                               placeholder="Contoh: heru.satlantas@polresmojokerto.id"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                    </div>

                    <!-- Peran (Role) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Peran / Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="role" required
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <option value="SUPER_ADMIN" <?= old('role') === 'SUPER_ADMIN' ? 'selected' : '' ?>>SUPER_ADMIN (Akses Penuh Kelola Admin & Seluruh Master)</option>
                            <option value="ADMIN_OPERATOR" <?= old('role') === 'ADMIN_OPERATOR' ? 'selected' : '' ?>>ADMIN_OPERATOR (Operator Input & Pemantauan)</option>
                        </select>
                    </div>

                    <!-- Kata Sandi -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required
                               placeholder="Minimal 6 karakter..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700">Akun Langsung Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="<?= base_url('/admin-users') ?>" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto justify-center px-6 py-2.5 bg-[#55E5A4] hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition shadow-sm shadow-emerald-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Simpan Administrator</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

