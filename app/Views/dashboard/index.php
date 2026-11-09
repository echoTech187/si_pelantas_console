<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- 4 TOP KPI CARDS (MENYERUPAI REFERENSI GAMBAR) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

    <!-- Card 1: Total Polisi -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Personel Polri</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
            <div class="text-2xl font-extrabold text-slate-900"><?= number_format($totalPolice) ?></div>
            <span class="inline-flex items-center text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                <i class="fa-solid fa-arrow-trend-up text-[9px] mr-1"></i> <?= $kamselCount ?> Kamsel
            </span>
        </div>
        <div class="mt-2 text-xs text-slate-400 font-medium">
            <?= $gakkumCount ?> Gakkum &bull; <?= $turjawaliCount ?> Turjawali
        </div>
    </div>

    <!-- Card 2: Total Sekolah Binaan -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sekolah Terhubung</span>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-school"></i>
            </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
            <div class="text-2xl font-extrabold text-slate-900"><?= number_format($totalSchools) ?></div>
            <span class="inline-flex items-center text-[11px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full">
                18 Kecamatan
            </span>
        </div>
        <div class="mt-2 text-xs text-slate-400 font-medium">
            <?= $smaCount ?> SMA &bull; <?= $smkCount ?> SMK &bull; <?= $maCount ?> MA
        </div>
    </div>

    <!-- Card 3: Total Guru BK -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akun Guru BK</span>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
            <div class="text-2xl font-extrabold text-slate-900"><?= number_format($totalBk) ?></div>
            <span class="inline-flex items-center text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                100% Aktif
            </span>
        </div>
        <div class="mt-2 text-xs text-slate-400 font-medium">
            Penerima Disposisi & Sidak
        </div>
    </div>

    <!-- Card 4: Total Kasus & Sidak -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kegiatan Sidak Kamsel</span>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
            <div class="text-2xl font-extrabold text-slate-900"><?= number_format($totalInspections) ?></div>
            <span class="inline-flex items-center text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                ★ <?= esc($avgRating) ?> / 5.0
            </span>
        </div>
        <div class="mt-2 text-xs text-slate-400 font-medium">
            <?= $totalReviews ?> Ulasan Guru BK Tersimpan
        </div>
    </div>

</div>

<!-- MIDDLE SECTION: CHARTS / METRICS & RECENT ACTIVITIES -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- LEFT 2 COLS: RECENT SIDAK KAMSEL DENGAN ULASAN BK -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-4 sm:p-6 border border-slate-100 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Riwayat Sidak & Arahan Kamsel Terkini</h2>
                <p class="text-xs text-slate-400 mt-0.5">Catatan audit log kunjungan penyuluhan ke sekolah & respon feedback Guru BK</p>
            </div>
            <a href="<?= base_url('/audit-logs/inspections') ?>" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition flex items-center gap-1">
                Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto table-responsive">
            <table class="w-full text-left text-xs min-w-[580px]">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="pb-3">Sidak</th>
                        <th class="pb-3">Asal Sekolah</th>
                        <th class="pb-3">Petugas Kamsel</th>
                        <th class="pb-3">Topik Arahan</th>
                        <th class="pb-3">Rating BK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($recentInspections)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat sidak tersimpan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentInspections as $ins): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 font-bold text-emerald-600">
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-[10px]">
                                        Ke-<?= esc($ins['inspection_sequence']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 font-extrabold text-slate-800">
                                    <?= esc($ins['school_name'] ?? 'Sekolah') ?>
                                </td>
                                <td class="py-3.5 text-slate-600">
                                    <?= esc(($ins['rank_title'] ?? '') . ' ' . ($ins['officer_name'] ?? 'Petugas')) ?>
                                </td>
                                <td class="py-3.5 text-slate-500 max-w-[200px] truncate">
                                    <?= esc($ins['counseling_topic'] ?? '-') ?>
                                </td>
                                <td class="py-3.5">
                                    <?php if (!empty($ins['rating_stars'])): ?>
                                        <div class="flex items-center gap-1 text-amber-500 font-extrabold">
                                            <i class="fa-solid fa-star text-[10px]"></i>
                                            <span><?= esc($ins['rating_stars']) ?>.0</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-[10px]">Menunggu ulasan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIGHT 1 COL: MASTER DATA SHORTCUTS & TARGET STATUS -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900">Akses Cepat CRUD</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Master Data</span>
            </div>

            <div class="mt-4 space-y-2.5">
                <a href="<?= base_url('/police-accounts/create') ?>" 
                   class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-emerald-600 shadow-xs">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Tambah Akun Polisi</div>
                            <div class="text-[10px] text-slate-400">NRP, Pangkat & Unit Kamsel</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-emerald-500"></i>
                </a>

                <a href="<?= base_url('/schools/create') ?>" 
                   class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-100 hover:border-sky-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-sky-600 shadow-xs">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-sky-700">Tambah Sekolah Baru</div>
                            <div class="text-[10px] text-slate-400">NPSN, Jenjang & Kecamatan</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-sky-500"></i>
                </a>

                <a href="<?= base_url('/bk-accounts/create') ?>" 
                   class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-indigo-600 shadow-xs">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-700">Tambah Guru BK</div>
                            <div class="text-[10px] text-slate-400">NIP, Sekolah & No. WA Disposisi</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-indigo-500"></i>
                </a>

                <a href="<?= base_url('/violations/create') ?>" 
                   class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 hover:border-amber-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-amber-600 shadow-xs">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-amber-700">Tambah Pasal Tilang</div>
                            <div class="text-[10px] text-slate-400">UU LLAJ, Bobot & Denda Max</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-amber-500"></i>
                </a>

                <a href="<?= base_url('/admin-users') ?>" 
                   class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-purple-50 border border-slate-100 hover:border-purple-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-purple-600 shadow-xs">
                            <i class="fa-solid fa-users-gear text-xs"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-purple-700">Kelola Akun Admin</div>
                            <div class="text-[10px] text-slate-400">Super Admin & Operator Konsol</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-purple-500"></i>
                </a>
            </div>
        </div>

        <div class="mt-6 p-4 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider">Indeks Presisi</span>
                <i class="fa-solid fa-certificate text-base"></i>
            </div>
            <div class="text-xl font-extrabold mt-2">100% Terintegrasi</div>
            <p class="text-[11px] text-emerald-50 mt-1">Seluruh data disinkronkan real-time dengan Aplikasi Mobile Polri & BK Sekolah.</p>
        </div>
    </div>

</div>

<!-- BOTTOM SECTION: TILANG TERBARU LAPANGAN -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Data Penindakan Pelanggaran Pelajar Terbaru</h2>
            <p class="text-xs text-slate-400 mt-0.5">Laporan tilang langsung dari personel di lapangan yang otomatis terdisposisi ke Guru BK</p>
        </div>
        <a href="<?= base_url('/audit-logs/violations') ?>" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition flex items-center gap-1">
            Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="overflow-x-auto table-responsive">
        <table class="w-full text-left text-xs min-w-[650px]">
            <thead>
                <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                    <th class="pb-3">No. Registrasi Tilang</th>
                    <th class="pb-3">Nama Siswa</th>
                    <th class="pb-3">Kelas / Jenjang</th>
                    <th class="pb-3">Asal Sekolah</th>
                    <th class="pb-3">Petugas Penindak</th>
                    <th class="pb-3">Waktu Kejadian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($recentViolations)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data penindakan pelanggaran.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentViolations as $viol): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-mono font-bold text-slate-800">
                                <?= esc($viol['ticket_number'] ?? '-') ?>
                            </td>
                            <td class="py-3 font-extrabold text-slate-800">
                                <?= esc($viol['student_name'] ?? 'Siswa') ?>
                            </td>
                            <td class="py-3 text-slate-600">
                                <span class="px-2 py-0.5 bg-slate-100 rounded text-[11px] font-semibold">
                                    <?= esc($viol['class_grade'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="py-3 font-semibold text-slate-700">
                                <?= esc($viol['school_name'] ?? '-') ?>
                            </td>
                            <td class="py-3 text-slate-600">
                                <?= esc(($viol['rank_title'] ?? '') . ' ' . ($viol['officer_name'] ?? 'Petugas')) ?>
                            </td>
                            <td class="py-3 text-slate-400 text-[11px]">
                                <?= date('d M Y, H:i', strtotime($viol['incident_at'] ?? $viol['created_at'] ?? 'now')) ?> WIB
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
