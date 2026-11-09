<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- HEADER ACTION & FILTER -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-100 shadow-sm space-y-4 sm:space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Daftar Guru BK Penghubung Sekolah</h2>
            <p class="text-xs text-slate-400 mt-0.5">Kelola kontak verifikator data pelanggaran pelajar dari pihak sekolah dan pengampu bimbingan konseling.</p>
        </div>
        <a href="<?= base_url('/bk-accounts/create') ?>" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#55E5A4] hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition shadow-sm shadow-emerald-500/20 w-full sm:w-auto flex-shrink-0">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Tambah Guru BK</span>
        </a>
    </div>

    <!-- FILTER BAR (DATA TABLES INTEGRATED) -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 pt-3 border-t border-slate-100">
        <div class="relative flex-1 w-full dt-search-wrapper">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="bkSearchInput" 
                   placeholder="Cari nama guru, NIP, username, atau email..." 
                   class="dt-search-input w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-800 placeholder-slate-400">
        </div>

        <div class="w-full md:w-64">
            <select id="schoolFilter" 
                    class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 font-medium">
                <option value="ALL">Semua Sekolah Binaan</option>
                <?php foreach ($schools as $sch): ?>
                    <option value="<?= $sch['id'] ?>"><?= esc($sch['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="button" id="btnResetFilter" class="w-full md:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">
            Reset
        </button>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto table-responsive">
        <table id="bkAccountsTable" class="w-full text-left text-xs min-w-[750px]">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                    <th class="py-3 px-6">Guru BK & NIP</th>
                    <th class="py-3 px-4">Sekolah Induk</th>
                    <th class="py-3 px-4">WhatsApp & Email</th>
                    <th class="py-3 px-4 text-center">PIC Utama</th>
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
    var table = initServerDataTable('#bkAccountsTable', '<?= base_url('/bk-accounts/data') ?>', [
        { data: 'teacher_html', className: 'py-4 px-6', orderable: true },
        { data: 'school_html', className: 'py-4 px-4', orderable: false },
        { data: 'contact_html', className: 'py-4 px-4', orderable: false },
        { data: 'primary_html', className: 'py-4 px-4 text-center', orderable: false },
        { data: 'status_html', className: 'py-4 px-4 text-center', orderable: false },
        { data: 'actions_html', className: 'py-4 px-6 text-right', orderable: false }
    ], {
        ajax: {
            data: function(d) {
                d.school_id = $('#schoolFilter').val();
            }
        },
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('hover:bg-slate-50/70 transition');
        }
    });

    // Custom instant search input with debounce
    $('#bkSearchInput').on('input', debounce(function() {
        table.search($(this).val()).draw();
    }, 300));

    // Instant school filter
    $('#schoolFilter').on('change', function() {
        table.draw();
    });

    // Reset button
    $('#btnResetFilter').on('click', function() {
        $('#bkSearchInput').val('');
        $('#schoolFilter').val('ALL').trigger('change');
        table.search('').draw();
    });
});
</script>
<?= $this->endSection() ?>
