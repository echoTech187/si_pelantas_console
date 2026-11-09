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

    <!-- EDIT FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Akun Administrator</h2>
                    <p class="text-xs text-slate-400">Perbarui profil administrator, email dinas, hak akses peran, atau reset kata sandi.</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg w-fit">
                @<?= esc($admin['username']) ?>
            </span>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="m-4 sm:m-6 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                <div class="flex items-center gap-2 text-rose-700 font-bold text-xs mb-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Terdapat kesalahan pada input form:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] text-rose-600 space-y-0.5">
                    <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/admin-users/update/' . $admin['id']) ?>" method="post" class="p-4 sm:p-6 space-y-6">
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
                            <input type="text" name="username" value="<?= esc(old('username') ?: $admin['username']) ?>" required
                                   class="w-full pl-8 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 font-mono">
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Administrator <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="<?= esc(old('full_name') ?: $admin['full_name']) ?>" required
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Administrator</label>
                        <input type="email" name="email" value="<?= esc(old('email') ?: ($admin['email'] ?? '')) ?>"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 font-mono">
                    </div>

                    <!-- Peran (Role) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Peran / Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="role" required
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <option value="SUPER_ADMIN" <?= (old('role') ?: $admin['role']) === 'SUPER_ADMIN' ? 'selected' : '' ?>>SUPER_ADMIN (Akses Penuh Kelola Admin & Seluruh Master)</option>
                            <option value="ADMIN_OPERATOR" <?= (old('role') ?: $admin['role']) === 'ADMIN_OPERATOR' ? 'selected' : '' ?>>ADMIN_OPERATOR (Operator Input & Pemantauan)</option>
                        </select>
                    </div>

                    <!-- Ganti Password -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Ganti Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="password"
                               placeholder="Isi hanya jika ingin mereset kata sandi admin..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= to_bool($admin['is_active']) ? 'checked' : '' ?> <?= ($admin['id'] === session()->get('admin_id')) ? 'disabled' : '' ?>>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700">Status Akun Aktif</span>
                        </label>
                        <?php if ($admin['id'] === session()->get('admin_id')): ?>
                            <input type="hidden" name="is_active" value="1">
                            <span class="text-[10px] text-slate-400 ml-2">(Akun yang sedang aktif digunakan tidak dapat dinonaktifkan)</span>
                        <?php endif; ?>
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
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

