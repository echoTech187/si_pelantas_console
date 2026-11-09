<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="mx-auto space-y-6">
    <!-- BACK LINK -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('/police-accounts') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Personel</span>
        </a>
    </div>

    <!-- EDIT FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Data Personel Polri</h2>
                    <p class="text-xs text-slate-400">Ubah informasi kedinasan, peran penugasan, nomor kontak, atau reset sandi akun.</p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-[11px] font-mono font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg w-fit inline-block">
                    NRP: <?= esc($account['nrp']) ?>
                </span>
            </div>
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

        <form action="<?= base_url('/police-accounts/update/' . $account['id']) ?>" method="post" class="p-4 sm:p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- SECTION 1: IDENTITAS -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-500"></i>
                    <span>Identitas Kedinasan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NRP (Readonly) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Registrasi Pokok (NRP)</label>
                        <input type="text" value="<?= esc($account['nrp']) ?>" disabled
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-mono cursor-not-allowed">
                        <span class="text-[10px] text-slate-400 mt-1 block">NRP terkunci untuk integritas rekonsiliasi database.</span>
                    </div>

                    <!-- Username (Readonly) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Username Login</label>
                        <input type="text" value="<?= esc($account['username']) ?>" disabled
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-mono cursor-not-allowed">
                        <span class="text-[10px] text-slate-400 mt-1 block">Username tidak dapat diubah setelah terdaftar.</span>
                    </div>

                    <!-- Pangkat / Gelar -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pangkat Kedinasan <span class="text-rose-500">*</span></label>
                        <select name="rank_title" required
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <?php 
                            $ranks = ['AKP', 'IPTU', 'IPDA', 'AIPTU', 'AIPDA', 'BRIPKA', 'BRIGADIR', 'BRIPTU', 'BRIPDA'];
                            foreach ($ranks as $r): 
                            ?>
                                <option value="<?= $r ?>" <?= (old('rank_title') ?: $account['rank_title']) === $r ? 'selected' : '' ?>><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="<?= esc(old('full_name') ?: $account['full_name']) ?>" required
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PENUGASAN -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                    <span>Penugasan & Peran (Role)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Hak Akses / Peran Utama <span class="text-rose-500">*</span></label>
                        <select name="role" required id="roleSelect"
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <option value="UNIT_KAMSEL" <?= (old('role') ?: $account['role']) === 'UNIT_KAMSEL' ? 'selected' : '' ?>>UNIT_KAMSEL (Penyuluhan & Radar Sidak Sekolah)</option>
                            <option value="POLANTAS" <?= (old('role') ?: $account['role']) === 'POLANTAS' ? 'selected' : '' ?>>POLANTAS (Penindakan Tilang Lapangan / Gakkum / Turjawali)</option>
                        </select>
                    </div>

                    <!-- Divisi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kesatuan / Divisi Operasional <span class="text-rose-500">*</span></label>
                        <select name="division" required id="divisionSelect"
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <option value="UNIT_KAMSEL" <?= (old('division') ?: $account['division']) === 'UNIT_KAMSEL' ? 'selected' : '' ?>>Unit Kamsel (Keamanan & Keselamatan Lalu Lintas)</option>
                            <option value="GAKKUM_LANTAS" <?= (old('division') ?: $account['division']) === 'GAKKUM_LANTAS' ? 'selected' : '' ?>>Gakkum Lantas (Penegakan Hukum Lalu Lintas)</option>
                            <option value="TURJAWALI_LANTAS" <?= (old('division') ?: $account['division']) === 'TURJAWALI_LANTAS' ? 'selected' : '' ?>>Turjawali Lantas (Pengaturan, Penjagaan, Pengawalan & Patroli)</option>
                        </select>
                    </div>

                    <!-- Wilayah Tugas -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Wilayah / Sektor Penugasan</label>
                        <input type="text" name="sector_area" value="<?= esc(old('sector_area') ?: ($account['sector_area'] ?? 'Polres Mojokerto')) ?>"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: KONTAK & RESET PASSWORD -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-key text-emerald-500"></i>
                    <span>Kontak & Keamanan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Koordinasi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-500 text-sm"></i>
                            <input type="text" name="phone_wa" value="<?= esc(old('phone_wa') ?: $account['phone_wa']) ?>" required
                                   class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 font-mono">
                        </div>
                    </div>

                    <!-- Reset Password (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Ganti Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password"
                               placeholder="Isi hanya jika ingin mereset password..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= to_bool($account['is_active']) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700">Status Akun Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="<?= base_url('/police-accounts') ?>" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
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

<script>
    $('#roleSelect').on('change', function() {
        if (this.value === 'UNIT_KAMSEL') {
            $('#divisionSelect').val('UNIT_KAMSEL').trigger('change');
        } else if ($('#divisionSelect').val() === 'UNIT_KAMSEL') {
            $('#divisionSelect').val('GAKKUM_LANTAS').trigger('change');
        }
    });
</script>

<?= $this->endSection() ?>

