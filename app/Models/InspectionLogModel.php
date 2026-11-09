<?php

namespace App\Models;

use CodeIgniter\Model;

class InspectionLogModel extends Model
{
    protected $table            = 'kamsel_inspection_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    public function getRecentInspections(int $limit = 10)
    {
        return $this->db->table('kamsel_inspection_logs il')
            ->select('il.*, s.name AS school_name, pa.full_name AS officer_name, pa.rank_title, pa.nrp,
                      fb.rating_stars, fb.feedback_notes, bk.full_name AS bk_teacher_name')
            ->join('schools s', 's.id = il.school_id', 'left')
            ->join('police_accounts pa', 'pa.id = il.kamsel_account_id', 'left')
            ->join('kamsel_counseling_feedbacks fb', 'fb.inspection_log_id = il.id', 'left')
            ->join('school_bk_accounts bk', 'bk.id = fb.bk_account_id', 'left')
            ->orderBy('il.visit_date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
