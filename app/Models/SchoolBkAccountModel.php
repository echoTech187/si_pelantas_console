<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolBkAccountModel extends Model
{
    protected $table            = 'school_bk_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'school_id',
        'nip_or_nuptk',
        'username',
        'password_hash',
        'full_name',
        'phone_wa',
        'fcm_token',
        'is_primary_pic',
        'is_active',
        'last_login_at',
        'created_at',
        'updated_at',
        'photo_url',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil daftar akun guru BK lengkap beserta informasi sekolah
     */
    public function getBkAccountsWithSchool(?string $search = null, ?string $schoolId = null)
    {
        $builder = $this->db->table('school_bk_accounts bk')
            ->select('bk.*, s.name AS school_name, s.npsn, s.education_level, d.name AS district_name')
            ->join('schools s', 's.id = bk.school_id', 'left')
            ->join('districts d', 'd.id = s.district_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(bk.full_name)', strtolower($search))
                ->orLike('bk.nip_or_nuptk', $search)
                ->orLike('LOWER(bk.username)', strtolower($search))
                ->orLike('LOWER(s.name)', strtolower($search))
                ->groupEnd();
        }

        if (!empty($schoolId) && $schoolId !== 'ALL') {
            $builder->where('bk.school_id', $schoolId);
        }

        return $builder->orderBy('s.name', 'ASC')->orderBy('bk.is_primary_pic', 'DESC')->get()->getResultArray();
    }
}

