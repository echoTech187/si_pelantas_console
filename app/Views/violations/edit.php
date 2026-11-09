<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="mx-auto space-y-6">
    <!-- BACK LINK -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('/violations') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Master Pasal Pelanggaran</span>
        </a>
    </div>

    <!-- EDIT FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Jenis Pelanggaran & Pasal Tilang</h2>
                    <p class="text-xs text-slate-400">Perbarui bunyi pasal UU LLAJ, bobot keparahan, atau tarif denda maksimal.</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg w-fit">
                Kode: <?= esc($violation['code']) ?>
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

        <form action="<?= base_url('/violations/update/' . $violation['id']) ?>" method="post" class="p-4 sm:p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- INFORMASI PASAL -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-book-bookmark text-emerald-500"></i>
                    <span>Rujukan Hukum & Uraian</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kode -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Unik Pelanggaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" value="<?= esc(old('code') ?: $violation['code']) ?>" required
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 font-mono uppercase">
                    </div>

                    <!-- Pasal UU LLAJ -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pasal Rujukan UU No. 22/2009 <span class="text-rose-500">*</span></label>
                        <input type="text" name="uu_llaj_article" value="<?= esc(old('uu_llaj_article') ?: $violation['uu_llaj_article']) ?>" required
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800">
                    </div>

                    <!-- Judul Pelanggaran -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul / Bentuk Pelanggaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="<?= esc(old('title') ?: $violation['title']) ?>" required
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800">
                    </div>
                </div>
            </div>

            <!-- BOBOT & SANKSI -->
            <div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-gavel text-emerald-500"></i>
                    <span>Tingkat Keparahan & Sanksi Denda</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Bobot Keparahan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Bobot Keparahan (Skala 1 - 10) <span class="text-rose-500">*</span></label>
                        <input type="number" name="severity_weight" min="1" max="10" value="<?= esc(old('severity_weight') ?: $violation['severity_weight']) ?>" required
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 font-mono">
                    </div>

                    <!-- Denda Maksimal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Maksimal Denda (Rupiah) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">Rp</span>
                            <input type="number" name="max_fine_idr" step="10000" min="0" value="<?= esc(old('max_fine_idr') ?: (int)$violation['max_fine_idr']) ?>" required
                                   class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 font-mono">
                        </div>
                    </div>

                    <!-- Status Aktif Switch -->
                    <div class="flex items-center pt-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= to_bool($violation['is_active']) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700">Tampilkan di Pilihan Tilang Mobile</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="<?= base_url('/violations') ?>" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
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

