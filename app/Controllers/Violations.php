<?php

namespace App\Controllers;

use App\Models\ViolationMasterTypeModel;

class Violations extends BaseController
{
    protected ViolationMasterTypeModel $vModel;

    public function __construct()
    {
        $this->vModel = new ViolationMasterTypeModel();
    }

    /**
     * Tampilkan daftar Master Jenis Pelanggaran & Pasal UU LLAJ
     */
    public function index()
    {
        $search = $this->request->getGet('search');

        $violations = $this->vModel->getViolationTypesWithStats($search);

        $data = [
            'title'      => 'Master Pasal Pelanggaran (UU LLAJ No. 22/2009)',
            'pageTitle'  => 'Manajemen Pasal & Jenis Tilang',
            'activeMenu' => 'violations',
            'violations' => $violations,
            'search'     => $search,
            'totalCount' => count($violations),
        ];

        return view('violations/index', $data);
    }

    /**
     * Endpoint DataTables Server-Side dengan Split-Query untuk Master Jenis Tilang
     */
    public function datatable()
    {
        return \App\Libraries\FluentDatatables::of('violation_master_types')
            ->setPrimaryKey('id')
            ->select('id, code, title, uu_llaj_article, severity_weight, max_fine_idr, is_active')
            ->set_column_search(['code', 'title', 'uu_llaj_article'])
            ->set_orderable_columns(['id', 'title', 'uu_llaj_article', 'severity_weight', 'max_fine_idr', '', 'is_active', ''])
            ->set_default_order(['severity_weight' => 'desc', 'code' => 'asc'])
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('violation_master_types.*,
                                  (SELECT COUNT(*) FROM traffic_violation_items tvi WHERE tvi.violation_type_id = violation_master_types.id) AS total_penindakan');
            })
            ->addColumn('violation_html', function ($row) {
                $titleEsc = esc($row->title);
                $codeEsc  = esc($row->code);

                return '
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900">' . $titleEsc . '</div>
                        <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                            Kode: <span class="font-bold text-slate-600">' . $codeEsc . '</span>
                        </div>
                    </div>
                </div>';
            })
            ->addColumn('article_html', function ($row) {
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] bg-slate-100 text-slate-700 font-mono">' . esc($row->uu_llaj_article) . '</span>';
            })
            ->addColumn('severity_html', function ($row) {
                $w = (int)$row->severity_weight;
                if ($w >= 8) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-rose-100 text-rose-700">Level ' . $w . ' (Tinggi)</span>';
                } elseif ($w >= 5) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-amber-100 text-amber-700">Level ' . $w . ' (Sedang)</span>';
                } else {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">Level ' . $w . ' (Ringan)</span>';
                }
            })
            ->addColumn('fine_html', function ($row) {
                return 'Rp ' . number_format($row->max_fine_idr, 0, ',', '.');
            })
            ->addColumn('count_html', function ($row) {
                $cnt = (int)($row->total_penindakan ?? 0);
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">' . $cnt . ' Kali</span>';
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
                $toggleTitle = $isActive ? 'Nonaktifkan Pasal' : 'Aktifkan Pasal';
                $toggleIcon  = $isActive ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400';
                $toggleUrl   = base_url('/violations/toggle/' . $row->id);
                $editUrl     = base_url('/violations/edit/' . $row->id);
                $delUrl      = base_url('/violations/delete/' . $row->id);
                $delMsg      = 'Yakin ingin menghapus jenis pelanggaran ' . esc($row->title) . '?';

                return '
                <div class="inline-flex items-center gap-1.5">
                    <a href="' . $toggleUrl . '" title="' . $toggleTitle . '" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
                        <i class="fa-solid ' . $toggleIcon . ' text-xs"></i>
                    </a>
                    <a href="' . $editUrl . '" title="Edit Pasal" class="p-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 transition">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </a>
                    <a href="' . $delUrl . '" onclick="return confirm(\'' . addslashes($delMsg) . '\')" title="Hapus Pasal" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </a>
                </div>';
            })
            ->make();
    }

    /**
     * Form Tambah Jenis Pelanggaran Baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Pasal Pelanggaran Baru',
            'pageTitle'  => 'Tambah Master Jenis Tilang',
            'activeMenu' => 'violations',
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
            'old'        => session()->getFlashdata('oldInput') ?? [],
        ];

        return view('violations/create', $data);
    }

    /**
     * Simpan Jenis Pelanggaran ke Database
     */
    public function store()
    {
        $rules = [
            'code'            => 'required|max_length[50]|is_unique[violation_master_types.code]',
            'title'           => 'required|max_length[150]',
            'uu_llaj_article' => 'required|max_length[120]',
            'severity_weight' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[10]',
            'max_fine_idr'    => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $insertData = [
            'id'              => uuid_v4(),
            'code'            => strtoupper(trim($this->request->getPost('code'))),
            'title'           => trim($this->request->getPost('title')),
            'uu_llaj_article' => trim($this->request->getPost('uu_llaj_article')),
            'severity_weight' => (int)$this->request->getPost('severity_weight'),
            'max_fine_idr'    => (float)$this->request->getPost('max_fine_idr'),
            'is_active'       => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        try {
            $this->vModel->insert($insertData);
            return redirect()->to('/violations')->with('success', 'Pasal pelanggaran ' . esc($insertData['title']) . ' berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan jenis pelanggaran: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Jenis Pelanggaran
     */
    public function edit(string $id)
    {
        $violation = $this->vModel->find($id);

        if (!$violation) {
            return redirect()->to('/violations')->with('error', 'Data jenis pelanggaran tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Pasal Pelanggaran - ' . esc($violation['code']),
            'pageTitle'  => 'Edit Master Jenis Tilang',
            'activeMenu' => 'violations',
            'violation'  => $violation,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
        ];

        return view('violations/edit', $data);
    }

    /**
     * Update Jenis Pelanggaran
     */
    public function update(string $id)
    {
        $violation = $this->vModel->find($id);
        if (!$violation) {
            return redirect()->to('/violations')->with('error', 'Data tidak ditemukan.');
        }

        $code = strtoupper(trim($this->request->getPost('code')));
        $codeRule = ($code === $violation['code'])
            ? 'required|max_length[50]'
            : 'required|max_length[50]|is_unique[violation_master_types.code]';

        $rules = [
            'code'            => $codeRule,
            'title'           => 'required|max_length[150]',
            'uu_llaj_article' => 'required|max_length[120]',
            'severity_weight' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[10]',
            'max_fine_idr'    => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $updateData = [
            'code'            => $code,
            'title'           => trim($this->request->getPost('title')),
            'uu_llaj_article' => trim($this->request->getPost('uu_llaj_article')),
            'severity_weight' => (int)$this->request->getPost('severity_weight'),
            'max_fine_idr'    => (float)$this->request->getPost('max_fine_idr'),
            'is_active'       => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        try {
            $this->vModel->update($id, $updateData);
            return redirect()->to('/violations')->with('success', 'Pasal pelanggaran ' . esc($updateData['title']) . ' berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pasal: ' . $e->getMessage());
        }
    }

    /**
     * Hapus / Nonaktifkan Jenis Pelanggaran
     */
    public function delete(string $id)
    {
        $violation = $this->vModel->find($id);
        if (!$violation) {
            return redirect()->to('/violations')->with('error', 'Data tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $usedCount = $db->table('traffic_violation_items')->where('violation_type_id', $id)->countAllResults();

        if ($usedCount > 0) {
            // Lindungi riwayat tilang yang pernah diterbitkan
            $this->vModel->update($id, ['is_active' => false]);
            return redirect()->to('/violations')->with('success', 'Pasal ' . esc($violation['title']) . ' sudah pernah digunakan pada riwayat penindakan tilang. Status dinonaktifkan.');
        }

        try {
            $this->vModel->delete($id);
            return redirect()->to('/violations')->with('success', 'Pasal ' . esc($violation['title']) . ' berhasil dihapus permanen.');
        } catch (\Throwable $e) {
            return redirect()->to('/violations')->with('error', 'Gagal menghapus pasal: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Status Aktif / Non-Aktif
     */
    public function toggleStatus(string $id)
    {
        $violation = $this->vModel->find($id);
        if (!$violation) {
            return redirect()->to('/violations')->with('error', 'Data tidak ditemukan.');
        }

        $currentStatus = to_bool($violation['is_active']);
        $newStatus     = !$currentStatus;
        $this->vModel->update($id, ['is_active' => $newStatus ? 't' : 'f']);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/violations')->with('success', 'Pasal ' . esc($violation['code']) . " berhasil $statusText.");
    }
}

