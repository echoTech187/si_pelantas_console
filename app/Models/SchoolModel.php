<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolModel extends Model
{
    protected $table            = 'schools';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'npsn',
        'name',
        'education_level',
        'district_id',
        'address',
        'official_phone',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil daftar sekolah lengkap beserta nama kecamatan & statistik pelanggar
     */
    public function getSchoolsWithDistrict(?string $search = null, ?string $level = null)
    {
        $builder = $this->db->table('schools s')
            ->select('s.*, d.name AS district_name, d.region_type,
                      (SELECT COUNT(*) FROM traffic_violations tv WHERE tv.school_id = s.id) AS violation_count,
                      (SELECT COUNT(*) FROM school_bk_accounts bk WHERE bk.school_id = s.id) AS bk_account_count,
                      (SELECT COUNT(*) FROM kamsel_inspection_logs il WHERE il.school_id = s.id) AS inspection_count')
            ->join('districts d', 'd.id = s.district_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(s.name)', strtolower($search))
                ->orLike('s.npsn', $search)
                ->orLike('LOWER(d.name)', strtolower($search))
                ->groupEnd();
        }

        if (!empty($level) && $level !== 'ALL') {
            $builder->where('s.education_level', $level);
        }

        return $builder->orderBy('s.name', 'ASC')->get()->getResultArray();
    }
}

