<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- HEADER ACTION & FILTER -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-100 shadow-sm space-y-4 sm:space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Audit Data Penindakan Tilang Pelajar</h2>
            <p class="text-xs text-slate-400 mt-0.5">Seluruh rekaman tilang pelanggaran tata tertib berlalu lintas yang dilakukan oleh siswa di jalan raya.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200/60 w-fit">
                <i class="fa-solid fa-triangle-exclamation mr-1 text-rose-600"></i> Gakkum Lantas Mojokerto
            </span>
        </div>
    </div>

    <!-- FILTER BAR (DATA TABLES INTEGRATED) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-3 border-t border-slate-100">
        <div class="relative flex-1 w-full dt-search-wrapper">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="violationLogSearchInput" 
                   placeholder="Cari nomor tilang (misal: TLG-...), atau lokasi penindakan..." 
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
        <table id="violationsLogTable" class="w-full text-left text-xs min-w-[750px]">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                    <th class="py-3 px-6">No. Tilang & Waktu</th>
                    <th class="py-3 px-4">Nama Pelajar / Siswa</th>
                    <th class="py-3 px-4">Sekolah Asal</th>
                    <th class="py-3 px-4">Pasal Pelanggaran & Lokasi</th>
                    <th class="py-3 px-4">Petugas Penindak</th>
                    <th class="py-3 px-6 text-right">Denda / Sanksi</th>
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
    var table = initServerDataTable('#violationsLogTable', '<?= base_url('/audit-logs/violations/data') ?>', [
        { data: 'ticket_html', className: 'py-4 px-6', orderable: true },
        { data: 'student_html', className: 'py-4 px-4', orderable: false },
        { data: 'school_html', className: 'py-4 px-4 font-semibold text-slate-700', orderable: false },
        { data: 'violation_html', className: 'py-4 px-4 max-w-xs', orderable: false },
        { data: 'officer_html', className: 'py-4 px-4', orderable: false },
        { data: 'fine_html', className: 'py-4 px-6 text-right font-mono font-bold text-slate-800', orderable: false }
    ], {
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('hover:bg-slate-50/70 transition');
        }
    });

    // Custom instant search input with debounce
    $('#violationLogSearchInput').on('input', debounce(function() {
        table.search($(this).val()).draw();
    }, 300));

    // Reset button
    $('#btnResetFilter').on('click', function() {
        $('#violationLogSearchInput').val('');
        table.search('').draw();
    });
});
</script>
<?= $this->endSection() ?>
