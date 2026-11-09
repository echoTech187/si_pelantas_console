<?php

namespace App\Controllers;

use App\Models\PoliceAccountModel;

class PoliceAccounts extends BaseController
{
    protected PoliceAccountModel $policeModel;

    public function __construct()
    {
        $this->policeModel = new PoliceAccountModel();
    }

    /**
     * Tampilkan daftar Akun Polri
     */
    public function index()
    {
        $search   = $this->request->getGet('search');
        $division = $this->request->getGet('division');

        $accounts = $this->policeModel->getFilteredList($search, $division);

        $data = [
            'title'      => 'Master Akun Polri (Kamsel & Polantas)',
            'pageTitle'  => 'Manajemen Akun Polri',
            'activeMenu' => 'police',
            'accounts'   => $accounts,
            'search'     => $search,
            'division'   => $division ?? 'ALL',
            'totalCount' => count($accounts),
        ];

        return view('police/index', $data);
    }

    /**
     * Endpoint DataTables Server-Side dengan Split-Query (Late Row Lookup)
     */
    public function datatable()
    {
        return \App\Libraries\FluentDatatables::of('police_accounts')
            ->setPrimaryKey('id')
            ->select('id, nrp, username, full_name, rank_title, role, division, phone_wa, sector_area, is_active, created_at')
            ->set_column_search(['nrp', 'username', 'full_name', 'rank_title', 'phone_wa', 'sector_area'])
            ->set_orderable_columns(['id', 'full_name', 'division', 'sector_area', 'phone_wa', 'is_active', ''])
            ->set_default_order(['created_at' => 'desc'])
            ->filter(function ($builder, $params) {
                $division = $this->request->getPost('division') ?? $this->request->getGet('division');
                if (!empty($division) && $division !== 'ALL') {
                    $builder->where('division', $division);
                }
            })
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('id, nrp, username, full_name, rank_title, role, division, phone_wa, sector_area, is_active, created_at');
            })
            ->addColumn('personel_html', function ($row) {
                $initials = strtoupper(substr($row->rank_title ?? 'P', 0, 1) . substr($row->full_name ?? 'P', 0, 1));
                $nameEsc = esc(($row->rank_title ?? '') . ' ' . $row->full_name);
                $nrpEsc  = esc($row->nrp);
                $userEsc = esc($row->username);
                return '
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                        ' . $initials . '
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900">' . $nameEsc . '</div>
                        <div class="text-[11px] font-mono text-slate-400 flex items-center gap-2 mt-0.5">
                            <span>NRP: ' . $nrpEsc . '</span>
                            <span>&bull;</span>
                            <span>@' . $userEsc . '</span>
                        </div>
                    </div>
                </div>';
            })
            ->addColumn('role_html', function ($row) {
                if ($row->division === 'UNIT_KAMSEL') {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60"><i class="fa-solid fa-chalkboard-user mr-1 text-[9px]"></i> Unit Kamsel</span>';
                } elseif ($row->division === 'GAKKUM_LANTAS') {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60"><i class="fa-solid fa-gavel mr-1 text-[9px]"></i> Gakkum Lantas</span>';
                } else {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60"><i class="fa-solid fa-car-side mr-1 text-[9px]"></i> Turjawali Lantas</span>';
                }
            })
            ->addColumn('sector_html', function ($row) {
                return '<span class="font-semibold text-slate-700">' . esc($row->sector_area ?? 'Polres Mojokerto') . '</span>';
            })
            ->addColumn('wa_html', function ($row) {
                $wa = esc($row->phone_wa);
                return '<a href="https://wa.me/' . $wa . '" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold hover:underline"><i class="fa-brands fa-whatsapp text-sm"></i><span>' . $wa . '</span></a>';
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
                $toggleTitle = $isActive ? 'Nonaktifkan Akun' : 'Aktifkan Akun';
                $toggleIcon  = $isActive ? 'fa-user-xmark' : 'fa-user-check';
                $toggleUrl   = base_url('/police-accounts/toggle/' . $row->id);
                $editUrl     = base_url('/police-accounts/edit/' . $row->id);
                $delUrl      = base_url('/police-accounts/delete/' . $row->id);
                $delMsg      = 'Yakin ingin menghapus personel ' . esc($row->full_name) . '? Data riwayat sidak dan tilang yang terkait mungkin terpengaruh.';

                return '
                <div class="inline-flex items-center gap-1.5">
                    <a href="' . $toggleUrl . '" title="' . $toggleTitle . '" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                        <i class="fa-solid ' . $toggleIcon . ' text-xs"></i>
                    </a>
                    <a href="' . $editUrl . '" title="Edit Personel" class="p-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 transition">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </a>
                    <a href="' . $delUrl . '" onclick="return confirm(\'' . addslashes($delMsg) . '\')" title="Hapus Personel" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </a>
                </div>';
            })
            ->make();
    }

    /**
     * Form Tambah Akun Polisi Baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Akun Petugas Polri',
            'pageTitle'  => 'Tambah Personel Polri',
            'activeMenu' => 'police',
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
            'old'        => session()->getFlashdata('oldInput') ?? [],
        ];

        return view('police/create', $data);
    }

    /**
     * Simpan Akun Baru ke Database
     */
    public function store()
    {
        $rules = [
            'nrp'         => 'required|min_length[6]|max_length[20]|is_unique[police_accounts.nrp]',
            'username'    => 'required|min_length[4]|max_length[50]|is_unique[police_accounts.username]',
            'password'    => 'required|min_length[6]',
            'full_name'   => 'required|min_length[3]|max_length[100]',
            'rank_title'  => 'required',
            'role'        => 'required|in_list[POLANTAS,UNIT_KAMSEL]',
            'division'    => 'required|in_list[TURJAWALI_LANTAS,GAKKUM_LANTAS,UNIT_KAMSEL]',
            'phone_wa'    => 'required|min_length[9]|max_length[20]',
            'sector_area' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $passwordHash = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        // Buat format nomor WA Indonesia jika belum ada kode negara
        $phone = preg_replace('/[^0-9]/', '', $this->request->getPost('phone_wa'));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $insertData = [
            'id'            => uuid_v4(),
            'nrp'           => trim($this->request->getPost('nrp')),
            'username'      => strtolower(trim($this->request->getPost('username'))),
            'password_hash' => $passwordHash,
            'full_name'     => trim($this->request->getPost('full_name')),
            'rank_title'    => trim($this->request->getPost('rank_title')),
            'role'          => $this->request->getPost('role'),
            'division'      => $this->request->getPost('division'),
            'sector_area'   => trim($this->request->getPost('sector_area')) ?: 'Polres Mojokerto',
            'phone_wa'      => $phone,
            'is_active'     => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        try {
            $this->policeModel->insert($insertData);
            return redirect()->to('/police-accounts')->with('success', 'Akun personel Polri ' . esc($insertData['full_name']) . ' berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan akun: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Akun Polisi
     */
    public function edit(string $id)
    {
        $account = $this->policeModel->find($id);

        if (!$account) {
            return redirect()->to('/police-accounts')->with('error', 'Akun personel Polri tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Akun Personel - ' . esc($account['full_name']),
            'pageTitle'  => 'Edit Personel Polri',
            'activeMenu' => 'police',
            'account'    => $account,
            'errors'     => session()->getFlashdata('validationErrors') ?? [],
        ];

        return view('police/edit', $data);
    }

    /**
     * Simpan Perubahan Data Akun
     */
    public function update(string $id)
    {
        $account = $this->policeModel->find($id);
        if (!$account) {
            return redirect()->to('/police-accounts')->with('error', 'Akun tidak ditemukan.');
        }

        $rules = [
            'full_name'   => 'required|min_length[3]|max_length[100]',
            'rank_title'  => 'required',
            'role'        => 'required|in_list[POLANTAS,UNIT_KAMSEL]',
            'division'    => 'required|in_list[TURJAWALI_LANTAS,GAKKUM_LANTAS,UNIT_KAMSEL]',
            'phone_wa'    => 'required|min_length[9]|max_length[20]',
            'sector_area' => 'permit_empty|max_length[100]',
        ];

        // Validasi password baru hanya jika diisi
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validationErrors', $this->validator->getErrors());
        }

        $phone = preg_replace('/[^0-9]/', '', $this->request->getPost('phone_wa'));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $updateData = [
            'full_name'   => trim($this->request->getPost('full_name')),
            'rank_title'  => trim($this->request->getPost('rank_title')),
            'role'        => $this->request->getPost('role'),
            'division'    => $this->request->getPost('division'),
            'sector_area' => trim($this->request->getPost('sector_area')) ?: 'Polres Mojokerto',
            'phone_wa'    => $phone,
            'is_active'   => $this->request->getPost('is_active') ? 't' : 'f',
        ];

        if (!empty($newPassword)) {
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        try {
            $this->policeModel->update($id, $updateData);
            return redirect()->to('/police-accounts')->with('success', 'Data personel ' . esc($updateData['full_name']) . ' berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage());
        }
    }

    /**
     * Hapus atau Nonaktifkan Akun
     */
    public function delete(string $id)
    {
        $account = $this->policeModel->find($id);
        if (!$account) {
            return redirect()->to('/police-accounts')->with('error', 'Akun tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $hasViolations = $db->table('traffic_violations')->where('polantas_account_id', $id)->countAllResults();
        $hasInspections = $db->table('kamsel_inspection_logs')->where('kamsel_account_id', $id)->countAllResults();

        if ($hasViolations > 0 || $hasInspections > 0) {
            // Lindungi integritas audit log: Nonaktifkan daripada hard delete
            $this->policeModel->update($id, ['is_active' => false]);
            return redirect()->to('/police-accounts')->with('success', 'Akun personel ' . esc($account['full_name']) . ' memiliki riwayat operasional, status otomatis dinonaktifkan.');
        }

        try {
            $this->policeModel->delete($id);
            return redirect()->to('/police-accounts')->with('success', 'Akun personel ' . esc($account['full_name']) . ' berhasil dihapus permanen.');
        } catch (\Throwable $e) {
            return redirect()->to('/police-accounts')->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Status Aktif / Non-Aktif
     */
    public function toggleStatus(string $id)
    {
        $account = $this->policeModel->find($id);
        if (!$account) {
            return redirect()->to('/police-accounts')->with('error', 'Akun tidak ditemukan.');
        }

        $currentStatus = to_bool($account['is_active']);
        $newStatus     = !$currentStatus;
        $this->policeModel->update($id, ['is_active' => $newStatus ? 't' : 'f']);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/police-accounts')->with('success', 'Akun ' . esc($account['full_name']) . " berhasil $statusText.");
    }
}
