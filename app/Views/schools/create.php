<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="mx-auto space-y-6">
    <!-- BACK LINK -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('/schools') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Master Sekolah</span>
        </a>
    </div>

    <!-- CREATE FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-school"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Data Sekolah Binaan</h2>
                    <p class="text-xs text-slate-400">Pendaftaran sekolah baru ke dalam Radar Kamsel Satlantas Polres Mojokerto.</p>
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

        <form action="<?= base_url('/schools/store') ?>" method="post" class="p-4 sm:p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- IDENTITAS SEKOLAH -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-emerald-500"></i>
                    <span>Profil Sekolah</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NPSN -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Pokok Sekolah Nasional (NPSN) <span class="text-rose-500">*</span></label>
                        <input type="text" name="npsn" value="<?= old('npsn') ?>" required maxlength="8"
                               placeholder="Contoh: 20502724 (8 digit angka)"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                        <span class="text-[10px] text-slate-400 mt-1 block">Tepat 8 digit angka sesuai data Kemendikbud/Kemenag.</span>
                    </div>

                    <!-- Jenjang Pendidikan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenjang Satuan Pendidikan <span class="text-rose-500">*</span></label>
                        <select name="education_level" required
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="SMA" <?= old('education_level') === 'SMA' ? 'selected' : '' ?>>SMA - Sekolah Menengah Atas</option>
                            <option value="SMK" <?= old('education_level') === 'SMK' ? 'selected' : '' ?>>SMK - Sekolah Menengah Kejuruan</option>
                            <option value="MA" <?= old('education_level') === 'MA' ? 'selected' : '' ?>>MA - Madrasah Aliyah</option>
                        </select>
                    </div>

                    <!-- Nama Sekolah -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Resmi Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="<?= old('name') ?>" required
                               placeholder="Contoh: SMAN 1 Mojosari"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Wilayah Kecamatan <span class="text-rose-500">*</span></label>
                        <select name="district_id" required
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= old('district_id') === $d['id'] ? 'selected' : '' ?>>
                                    <?= esc($d['name']) ?> (<?= $d['region_type'] === 'KOTA_MOJOKERTO' ? 'Kota' : 'Kabupaten' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Telepon Resmi Sekolah -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Telepon Resmi / Tata Usaha</label>
                        <input type="text" name="official_phone" value="<?= old('official_phone') ?>"
                               placeholder="Contoh: 0321591410"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400 font-mono">
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap Sekolah <span class="text-rose-500">*</span></label>
                        <textarea name="address" rows="3" required
                                  placeholder="Contoh: Jl. Pemuda No. 55, Seduri, Kec. Mojosari, Kab. Mojokerto"
                                  class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400"><?= old('address') ?></textarea>
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700">Aktifkan Sekolah di Radar Kamsel</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="<?= base_url('/schools') ?>" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto justify-center px-6 py-2.5 bg-[#55E5A4] hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition shadow-sm shadow-emerald-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Simpan & Pantau di Radar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

