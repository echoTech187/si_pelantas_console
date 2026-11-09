<?php

namespace App\Controllers;

use App\Models\SchoolModel;
use App\Models\DistrictModel;

class Schools extends BaseController
{
    protected SchoolModel $schoolModel;
    protected DistrictModel $districtModel;

    public function __construct()
    {
        $this->schoolModel   = new SchoolModel();
        $this->districtModel = new DistrictModel();
    }

    /**
     * Tampilkan daftar Sekolah & Statistik Pelanggaran
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $level  = $this->request->getGet('level');

        $schools   = $this->schoolModel->getSchoolsWithDistrict($search, $level);
        $districts = $this->districtModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Master Data Sekolah (SMA/SMK/MA)',
            'pageTitle'  => 'Manajemen Sekolah & Radar Kamsel',
            'activeMenu' => 'schools',
            'schools'    => $schools,
            'districts'  => $districts,
            'search'     => $search,
            'level'      => $level ?? 'ALL',
            'totalCount' => count($schools),
        ];

        return view('schools/index', $data);
    }

    /**
     * Endpoint DataTables Server-Side dengan Split-Query (Late Lookup) untuk Master Sekolah
     */
    public function datatable()
    {
        return \App\Libraries\FluentDatatables::of('schools')
            ->setPrimaryKey('id')
            ->select('schools.id, schools.npsn, schools.name, schools.education_level, schools.district_id, schools.address, schools.official_phone, schools.is_active, schools.created_at')
            ->set_column_search(['schools.name', 'schools.npsn', 'schools.address'])
            ->set_orderable_columns(['id', 'schools.name', 'schools.education_level', 'schools.district_id', '', '', 'schools.is_active', ''])
            ->set_default_order(['schools.name' => 'asc'])
            ->filter(function ($builder, $params) {
                $level = $this->request->getPost('level') ?? $this->request->getGet('level');
                if (!empty($level) && $level !== 'ALL') {
                    $builder->where('schools.education_level', $level);
                }
            })
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('schools.*, d.name AS district_name,
                                  (SELECT COUNT(*) FROM traffic_violations tv WHERE tv.school_id = schools.id) AS violation_count,
                                  (SELECT COUNT(*) FROM school_bk_accounts bk WHERE bk.school_id = schools.id) AS bk_account_count,
                                  (SELECT COUNT(*) FROM kamsel_inspection_logs il WHERE il.school_id = schools.id) AS inspection_count')
                        ->join('districts d', 'd.id = schools.district_id', 'left');
            })
            ->addColumn('school_html', function ($row) {
                $nameEsc = esc($row->name);
                $npsnEsc = esc($row->npsn);
                $phoneHtml = !empty($row->official_phone) ? '<span class="text-slate-300 mx-1">&bull;</span><span class="text-slate-500 font-sans"><i class="fa-solid fa-phone text-[9px] mr-0.5"></i> ' . esc($row->official_phone) . '</span>' : '';

                return '
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                        <i class="fa-solid fa-school text-xs"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900">' . $nameEsc . '</div>
                        <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                            NPSN: ' . $npsnEsc . ' ' . $phoneHtml . '
                        </div>
                    </div>
                </div>';
            })
            ->addColumn('level_html', function ($row) {
                if ($row->education_level === 'SMA') {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">SMA</span>';
                } elseif ($row->education_level === 'SMK') {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">SMK</span>';
                } else {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">MA</span>';
                }
            })
            ->addColumn('district_html', function ($row) {
                $distName = esc($row->district_name ?? 'Wilayah Mojokerto');
                $addr = esc($row->address);
                return '
                <div class="font-bold text-slate-800">' . $distName . '</div>
                <div class="text-[11px] text-slate-400 truncate max-w-xs" title="' . $addr . '">' . $addr . '</div>';
            })
            ->addColumn('bk_count_html', function ($row) {
                $count = (int)($row->bk_account_count ?? 0);
                return '
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200/60">
                    <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                    <span>' . $count . ' Guru</span>
                </span>';
            })
            ->addColumn('violation_html', function ($row) {
                $vCount = (int)($row->violation_count ?? 0);
                if ($vCount >= 7) {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200">' . $vCount . ' Kasus (Sidak)</span>';
                } elseif ($vCount >= 5) {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 border border-amber-200">' . $vCount . ' Kasus (Siaga)</span>';
                } else {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">' . $vCount . ' Kasus</span>';
                }
            })
            ->addColumn('status_html', function ($row) {
                if (to_bool($row->is_active)) {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif</span>';
                } else {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Non-Aktif</span>';
                }
            })
            ->addColumn('actions_html', function ($row) {
                $isActive    = to_bool($row->is_active);
                $toggleTitle = $isActive ? 'Nonaktifkan Sekolah' : 'Aktifkan Sekolah';
                $toggleIcon  = $isActive ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400';
                $toggleUrl   = base_url('/schools/toggle/' . $row->id);
                $editUrl     = base_url('/schools/edit/' . $row->id);
                $delUrl      = base_url('/schools/delete/' . $row->id);
                $delMsg      = 'Yakin ingin menghapus sekolah ' . esc($row->name) . '? Seluruh riwayat akun BK terkait akan terdampak.';

                return '
                <div class="inline-flex items-center gap-1.5">
                    <a href="' . $toggleUrl . '" title="' . $toggleTitle . '" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
                        <i class="fa-solid ' . $toggleIcon . ' text-xs"></i>
                    </a>
                    <a href="' . $editUrl . '" title="Edit Sekolah" class="p-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 transition">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </a>
                    <a href="' . $delUrl . '" onclick="return confirm(\'' . addslashes($delMsg) . '\')" title="Hapus Sekolah" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </a>
                </div>';
            })
            ->make();
    }

    /**
     * Form Tambah Sekolah Baru
     */
    public function create()
    {
        $districts = $this->districtModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Tambah Data Sekolah Baru',
            'pageTitle'  => 'Tambah Sekolah Binaan',
            'activeMenu' => 'schools',
            'districts'  => $districts,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
            'old'        => session()->getFlashdata('oldInput') ?? [],
        ];

        return view('schools/create', $data);
    }

    /**
     * Simpan Sekolah Baru ke Database
     */
    public function store()
    {
        $rules = [
            'npsn'            => 'required|exact_length[8]|numeric|is_unique[schools.npsn]',
            'name'            => 'required|min_length[3]|max_length[150]',
            'education_level' => 'required|in_list[SMA,SMK,MA]',
            'district_id'     => 'required',
            'address'         => 'required|min_length[5]',
            'official_phone'  => 'permit_empty|max_length[25]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $schoolId = uuid_v4();

        $insertData = [
            'id'              => $schoolId,
            'npsn'            => trim($this->request->getPost('npsn')),
            'name'            => trim($this->request->getPost('name')),
            'education_level' => $this->request->getPost('education_level'),
            'district_id'     => $this->request->getPost('district_id'),
            'address'         => trim($this->request->getPost('address')),
            'official_phone'  => trim($this->request->getPost('official_phone')),
            'is_active'       => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 1. Simpan Sekolah
            $this->schoolModel->insert($insertData);

            // 2. Inisialisasi Siklus Kamsel agar langsung terpantau di Radar Kamsel
            $db->table('kamsel_evaluation_cycles')->insert([
                'id'                         => uuid_v4(),
                'school_id'                  => $schoolId,
                'cycle_number'               => 1,
                'violation_count'            => 0,
                'warning_threshold_snapshot' => 5,
                'visit_threshold_snapshot'   => 7,
                'status'                     => 'AMAN',
                'opened_at'                  => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();

            return redirect()->to('/schools')->with('success', 'Sekolah ' . esc($insertData['name']) . ' berhasil didaftarkan ke sistem & radar pembinaan!');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan sekolah: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Sekolah
     */
    public function edit(string $id)
    {
        $school = $this->schoolModel->find($id);

        if (!$school) {
            return redirect()->to('/schools')->with('error', 'Data sekolah tidak ditemukan.');
        }

        $districts = $this->districtModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Edit Data Sekolah - ' . esc($school['name']),
            'pageTitle'  => 'Edit Data Sekolah',
            'activeMenu' => 'schools',
            'school'     => $school,
            'districts'  => $districts,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
        ];

        return view('schools/edit', $data);
    }

    /**
     * Update Data Sekolah
     */
    public function update(string $id)
    {
        $school = $this->schoolModel->find($id);
        if (!$school) {
            return redirect()->to('/schools')->with('error', 'Data sekolah tidak ditemukan.');
        }

        $npsn = trim($this->request->getPost('npsn'));
        $npsnRule = ($npsn === $school['npsn']) 
            ? 'required|exact_length[8]|numeric' 
            : 'required|exact_length[8]|numeric|is_unique[schools.npsn]';

        $rules = [
            'npsn'            => $npsnRule,
            'name'            => 'required|min_length[3]|max_length[150]',
            'education_level' => 'required|in_list[SMA,SMK,MA]',
            'district_id'     => 'required',
            'address'         => 'required|min_length[5]',
            'official_phone'  => 'permit_empty|max_length[25]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $updateData = [
            'npsn'            => $npsn,
            'name'            => trim($this->request->getPost('name')),
            'education_level' => $this->request->getPost('education_level'),
            'district_id'     => $this->request->getPost('district_id'),
            'address'         => trim($this->request->getPost('address')),
            'official_phone'  => trim($this->request->getPost('official_phone')),
            'is_active'       => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        try {
            $this->schoolModel->update($id, $updateData);
            return redirect()->to('/schools')->with('success', 'Data sekolah ' . esc($updateData['name']) . ' berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui sekolah: ' . $e->getMessage());
        }
    }

    /**
     * Hapus / Nonaktifkan Sekolah
     */
    public function delete(string $id)
    {
        $school = $this->schoolModel->find($id);
        if (!$school) {
            return redirect()->to('/schools')->with('error', 'Data sekolah tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $violationCount  = $db->table('traffic_violations')->where('school_id', $id)->countAllResults();
        $inspectionCount = $db->table('kamsel_inspection_logs')->where('school_id', $id)->countAllResults();
        $bkCount         = $db->table('school_bk_accounts')->where('school_id', $id)->countAllResults();

        if ($violationCount > 0 || $inspectionCount > 0 || $bkCount > 0) {
            // Lindungi integritas audit: Nonaktifkan daripada hard delete
            $this->schoolModel->update($id, ['is_active' => false]);
            return redirect()->to('/schools')->with('success', 'Sekolah ' . esc($school['name']) . ' memiliki riwayat pelanggaran/kunjungan/akun BK. Status otomatis dinonaktifkan.');
        }

        try {
            // Hapus siklus kamsel terlebih dahulu jika ada
            $db->table('kamsel_evaluation_cycles')->where('school_id', $id)->delete();
            $this->schoolModel->delete($id);
            return redirect()->to('/schools')->with('success', 'Sekolah ' . esc($school['name']) . ' berhasil dihapus permanen.');
        } catch (\Throwable $e) {
            return redirect()->to('/schools')->with('error', 'Gagal menghapus sekolah: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Status Aktif / Non-Aktif
     */
    public function toggleStatus(string $id)
    {
        $school = $this->schoolModel->find($id);
        if (!$school) {
            return redirect()->to('/schools')->with('error', 'Data sekolah tidak ditemukan.');
        }

        $currentStatus = to_bool($school['is_active']);
        $newStatus     = !$currentStatus;
        $this->schoolModel->update($id, ['is_active' => $newStatus ? 't' : 'f']);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/schools')->with('success', 'Sekolah ' . esc($school['name']) . " berhasil $statusText.");
    }
}

