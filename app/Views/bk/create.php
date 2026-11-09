<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="mx-auto space-y-6">
    <!-- BACK LINK -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('/bk-accounts') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Master Guru BK</span>
        </a>
    </div>

    <!-- CREATE FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Akun Guru Bimbingan Konseling (BK)</h2>
                    <p class="text-xs text-slate-400">Pendaftaran akun resmi Guru BK sebagai PIC sekolah penerima pemberitahuan tilang & feedback sidak.</p>
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

        <form action="<?= base_url('/bk-accounts/store') ?>" method="post" class="p-4 sm:p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- PENUGASAN SEKOLAH -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-school text-emerald-500"></i>
                    <span>Sekolah Penugasan</span>
                </h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Sekolah Binaan <span class="text-rose-500">*</span></label>
                    <select name="school_id" required
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                        <option value="">-- Pilih Sekolah --</option>
                        <?php foreach ($schools as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= old('school_id') === $s['id'] ? 'selected' : '' ?>>
                                <?= esc($s['name']) ?> (NPSN: <?= esc($s['npsn']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- IDENTITAS GURU BK -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-500"></i>
                    <span>Identitas Guru BK</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIP / NUPTK -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">NIP / NUPTK <span class="text-rose-500">*</span></label>
                        <input type="text" name="nip_or_nuptk" value="<?= old('nip_or_nuptk') ?>" required
                               placeholder="Contoh: 198503152010012015"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="<?= old('full_name') ?>" required
                               placeholder="Contoh: Dra. Siti Aminah, M.Pd."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- WhatsApp -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Koordinasi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-500 text-sm"></i>
                            <input type="text" name="phone_wa" value="<?= old('phone_wa') ?>" required
                                   placeholder="Contoh: 081234567890 atau 6281234567890"
                                   class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Nomor ini akan dihubungi oleh petugas Satlantas saat koordinasi pelanggaran siswa.</span>
                    </div>
                </div>
            </div>

            <!-- KREDENSIAL LOGIN & ROLE -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-key text-emerald-500"></i>
                    <span>Kredensial Login & Status</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Username Login <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">@</span>
                            <input type="text" name="username" value="<?= old('username') ?>" required
                                   placeholder="Contoh: bk_sman1sooko"
                                   class="w-full pl-8 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required
                               placeholder="Minimal 6 karakter"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- PIC Utama Switch -->
                    <div class="flex items-center pt-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_primary_pic" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700">Jadikan PIC Utama Sekolah</span>
                        </label>
                    </div>

                    <!-- Status Aktif Switch -->
                    <div class="flex items-center pt-3">
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
                <a href="<?= base_url('/bk-accounts') ?>" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto justify-center px-6 py-2.5 bg-[#55E5A4] hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition shadow-sm shadow-emerald-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Simpan Akun Guru BK</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

