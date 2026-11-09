<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- HEADER ACTION & FILTER -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-100 shadow-sm space-y-4 sm:space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Master Jenis Pelanggaran & Pasal UU LLAJ</h2>
            <p class="text-xs text-slate-400 mt-0.5">Klasifikasi bobot keparahan pelanggaran tata tertib lalu lintas jalan pelajar dan besaran denda tilang.</p>
        </div>
        <a href="<?= base_url('/violations/create') ?>" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#55E5A4] hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition shadow-sm shadow-emerald-500/20 w-full sm:w-auto flex-shrink-0">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Jenis Pelanggaran</span>
        </a>
    </div>

    <!-- FILTER BAR (DATA TABLES INTEGRATED) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-3 border-t border-slate-100">
        <div class="relative flex-1 w-full dt-search-wrapper">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="violationSearchInput" 
                   placeholder="Cari kode (misal: VIO-HELM), judul pelanggaran, atau rujukan pasal UU LLAJ..." 
                   class="dt-search-input w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
        </div>

        <button type="button" id="btnResetFilter" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">
            Reset
        </button>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto table-responsive">
        <table id="violationsTable" class="w-full text-left text-xs min-w-[750px]">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                    <th class="py-3 px-6">Kode & Pelanggaran</th>
                    <th class="py-3 px-4">Pasal Rujukan UU LLAJ</th>
                    <th class="py-3 px-4 text-center">Bobot Keparahan</th>
                    <th class="py-3 px-4">Denda Maksimal</th>
                    <th class="py-3 px-4 text-center">Frekuensi Ditilang</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <!-- Data populated dynamically via FluentDatatables Server-Side -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    var table = initServerDataTable('#violationsTable', '<?= base_url('/violations/data') ?>', [
        { data: 'violation_html', className: 'py-4 px-6', orderable: true },
        { data: 'article_html', className: 'py-4 px-4 font-semibold text-slate-700', orderable: true },
        { data: 'severity_html', className: 'py-4 px-4 text-center', orderable: true },
        { data: 'fine_html', className: 'py-4 px-4 font-mono font-bold text-slate-800', orderable: true },
        { data: 'count_html', className: 'py-4 px-4 text-center', orderable: false },
        { data: 'status_html', className: 'py-4 px-4 text-center', orderable: false },
        { data: 'actions_html', className: 'py-4 px-6 text-right', orderable: false }
    ], {
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('hover:bg-slate-50/70 transition');
        }
    });

    // Custom instant search input with debounce
    $('#violationSearchInput').on('input', debounce(function() {
        table.search($(this).val()).draw();
    }, 300));

    // Reset button
    $('#btnResetFilter').on('click', function() {
        $('#violationSearchInput').val('');
        table.search('').draw();
    });
});
</script>
<?= $this->endSection() ?>
