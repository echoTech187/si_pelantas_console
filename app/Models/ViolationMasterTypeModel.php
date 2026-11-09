<?php

namespace App\Models;

use CodeIgniter\Model;

class ViolationMasterTypeModel extends Model
{
    protected $table            = 'violation_master_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'code',
        'title',
        'uu_llaj_article',
        'severity_weight',
        'max_fine_idr',
        'is_active',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil daftar jenis pasal tilang beserta total kasus yang pernah tercatat
     */
    public function getViolationTypesWithStats(?string $search = null)
    {
        $builder = $this->db->table('violation_master_types v')
            ->select('v.*,
                      (SELECT COUNT(*) FROM traffic_violation_items tvi WHERE tvi.violation_type_id = v.id) AS total_penindakan');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(v.title)', strtolower($search))
                ->orLike('v.code', $search)
                ->orLike('LOWER(v.uu_llaj_article)', strtolower($search))
                ->groupEnd();
        }

        return $builder->orderBy('v.severity_weight', 'DESC')->get()->getResultArray();
    }
}

