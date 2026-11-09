<?php

namespace App\Controllers;

use App\Models\InspectionLogModel;
use App\Models\TrafficViolationModel;

class AuditLogs extends BaseController
{
    protected InspectionLogModel $inspModel;
    protected TrafficViolationModel $violModel;

    public function __construct()
    {
        $this->inspModel = new InspectionLogModel();
        $this->violModel = new TrafficViolationModel();
    }

    /**
     * Tampilkan Riwayat Log Kunjungan / Sidak Kamsel ke Sekolah
     */
    public function inspections()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('kamsel_inspection_logs il')
            ->select('il.*, s.name AS school_name, s.npsn, s.education_level, d.name AS district_name,
                      pa.full_name AS officer_name, pa.rank_title, pa.nrp,
                      fb.rating_stars, fb.feedback_notes, fb.created_at AS feedback_at,
                      bk.full_name AS bk_teacher_name')
            ->join('schools s', 's.id = il.school_id', 'left')
            ->join('districts d', 'd.id = s.district_id', 'left')
            ->join('police_accounts pa', 'pa.id = il.kamsel_account_id', 'left')
            ->join('kamsel_counseling_feedbacks fb', 'fb.inspection_log_id = il.id', 'left')
            ->join('school_bk_accounts bk', 'bk.id = fb.bk_account_id', 'left')
            ->orderBy('il.visit_date', 'DESC');

        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(s.name)', strtolower($search))
                ->orLike('LOWER(pa.full_name)', strtolower($search))
                ->orLike('LOWER(il.counseling_topic)', strtolower($search))
                ->groupEnd();
        }

        $inspections = $builder->get()->getResultArray();

        $data = [
            'title'       => 'Audit Log Sidak & Penyuluhan Kamsel',
            'pageTitle'   => 'Riwayat Log Sidak Kamsel',
            'activeMenu'  => 'inspections',
            'inspections' => $inspections,
            'search'      => $search,
            'totalCount'  => count($inspections),
        ];

        return view('audit_logs/inspections', $data);
    }

    /**
     * Endpoint DataTables Server-Side dengan Split-Query untuk Log Sidak Kamsel
     */
    public function datatableInspections()
    {
        return \App\Libraries\FluentDatatables::of('kamsel_inspection_logs')
            ->setPrimaryKey('id')
            ->select('kamsel_inspection_logs.id, kamsel_inspection_logs.visit_date, kamsel_inspection_logs.counseling_topic, kamsel_inspection_logs.kamsel_directive_summary, kamsel_inspection_logs.student_attendee_count, kamsel_inspection_logs.school_id, kamsel_inspection_logs.kamsel_account_id')
            ->set_column_search(['kamsel_inspection_logs.counseling_topic', 'kamsel_inspection_logs.kamsel_directive_summary'])
            ->set_orderable_columns(['id', 'kamsel_inspection_logs.visit_date', '', '', '', 'kamsel_inspection_logs.student_attendee_count', ''])
            ->set_default_order(['kamsel_inspection_logs.visit_date' => 'desc'])
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('kamsel_inspection_logs.*, s.name AS school_name, s.npsn, s.education_level, d.name AS district_name,
                                  pa.full_name AS officer_name, pa.rank_title, pa.nrp,
                                  fb.rating_stars, fb.feedback_notes, fb.created_at AS feedback_at,
                                  bk.full_name AS bk_teacher_name')
                        ->join('schools s', 's.id = kamsel_inspection_logs.school_id', 'left')
                        ->join('districts d', 'd.id = s.district_id', 'left')
                        ->join('police_accounts pa', 'pa.id = kamsel_inspection_logs.kamsel_account_id', 'left')
                        ->join('kamsel_counseling_feedbacks fb', 'fb.inspection_log_id = kamsel_inspection_logs.id', 'left')
                        ->join('school_bk_accounts bk', 'bk.id = fb.bk_account_id', 'left');
            })
            ->addColumn('date_html', function ($row) {
                $dateStr = date('d M Y', strtotime($row->visit_date));
                $timeStr = date('H:i', strtotime($row->visit_date));
                return '
                <div class="font-bold text-slate-800 font-mono">' . $dateStr . '</div>
                <div class="text-[11px] text-slate-400 font-mono">' . $timeStr . ' WIB</div>';
            })
            ->addColumn('school_html', function ($row) {
                $schName  = esc($row->school_name ?? '-');
                $distName = esc($row->district_name ?? '');
                return '
                <div class="font-bold text-slate-900">' . $schName . '</div>
                <div class="text-[11px] text-slate-400 mt-0.5">' . $distName . '</div>';
            })
            ->addColumn('officer_html', function ($row) {
                $offName = esc(($row->rank_title ?? '') . ' ' . ($row->officer_name ?? '-'));
                $nrp     = esc($row->nrp ?? '-');
                return '
                <div class="font-bold text-slate-800">' . $offName . '</div>
                <div class="text-[11px] font-mono text-slate-400">NRP: ' . $nrp . '</div>';
            })
            ->addColumn('topic_html', function ($row) {
                $topic = esc($row->counseling_topic ?? 'Penyuluhan Keselamatan LLAJ');
                $notes = esc($row->kamsel_directive_summary ?? '-');
                return '
                <div class="font-bold text-slate-800">' . $topic . '</div>
                <div class="text-[11px] text-slate-500 line-clamp-2 mt-0.5" title="' . $notes . '">' . $notes . '</div>';
            })
            ->addColumn('attendees_html', function ($row) {
                $cnt = (int)($row->student_attendee_count ?? 0);
                return '
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-50 text-blue-700">
                    <i class="fa-solid fa-users text-[10px]"></i>
                    <span>' . $cnt . ' Siswa</span>
                </span>';
            })
            ->addColumn('feedback_html', function ($row) {
                if (!empty($row->rating_stars)) {
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= '<i class="fa-' . ($i <= (int)$row->rating_stars ? 'solid' : 'regular') . ' fa-star"></i>';
                    }
                    $notes = esc($row->feedback_notes ?? 'Sangat memuaskan');
                    $by = esc($row->bk_teacher_name ?? 'Guru BK');
                    return '
                    <div class="flex items-center gap-1 text-amber-400 text-xs">
                        ' . $stars . '
                        <span class="font-bold text-slate-700 ml-1">' . $row->rating_stars . '.0</span>
                    </div>
                    <div class="text-[11px] text-slate-500 italic mt-1 line-clamp-1">"' . $notes . '"</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Oleh: ' . $by . '</div>';
                }
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-500">Menunggu Feedback BK</span>';
            })
            ->make();
    }

    /**
     * Tampilkan Riwayat Data Tilang Pelajar
     */
    public function violations()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('traffic_violations tv')
            ->select('tv.*, s.name AS school_name, s.education_level,
                      st.full_name AS student_name, st.class_grade, st.class_name,
                      pa.full_name AS officer_name, pa.rank_title, pa.nrp,
                      (SELECT STRING_AGG(vmt.title, \', \') 
                       FROM traffic_violation_items tvi 
                       JOIN violation_master_types vmt ON vmt.id = tvi.violation_type_id 
                       WHERE tvi.violation_id = tv.id) AS violation_items,
                      (SELECT COALESCE(SUM(vmt.max_fine_idr), 0) 
                       FROM traffic_violation_items tvi 
                       JOIN violation_master_types vmt ON vmt.id = tvi.violation_type_id 
                       WHERE tvi.violation_id = tv.id) AS total_fine_idr')
            ->join('schools s', 's.id = tv.school_id', 'left')
            ->join('students st', 'st.id = tv.student_id', 'left')
            ->join('police_accounts pa', 'pa.id = tv.polantas_account_id', 'left')
            ->orderBy('tv.incident_at', 'DESC');

        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(tv.ticket_number)', strtolower($search))
                ->orLike('LOWER(st.full_name)', strtolower($search))
                ->orLike('LOWER(s.name)', strtolower($search))
                ->orLike('LOWER(pa.full_name)', strtolower($search))
                ->groupEnd();
        }

        $violations = $builder->get()->getResultArray();

        $data = [
            'title'      => 'Audit Data Tilang Pelajar Lapangan',
            'pageTitle'  => 'Data Penindakan Tilang Siswa',
            'activeMenu' => 'violations_log',
            'violations' => $violations,
            'search'     => $search,
            'totalCount' => count($violations),
        ];

        return view('audit_logs/violations', $data);
    }

    /**
     * Endpoint DataTables Server-Side dengan Split-Query untuk Data Tilang Pelajar
     */
    public function datatableViolations()
    {
        return \App\Libraries\FluentDatatables::of('traffic_violations')
            ->setPrimaryKey('id')
            ->select('traffic_violations.id, traffic_violations.ticket_number, traffic_violations.incident_at, traffic_violations.incident_location, traffic_violations.school_id, traffic_violations.student_id, traffic_violations.polantas_account_id')
            ->set_column_search(['traffic_violations.ticket_number', 'traffic_violations.incident_location'])
            ->set_orderable_columns(['id', 'traffic_violations.incident_at', '', '', '', '', ''])
            ->set_default_order(['traffic_violations.incident_at' => 'desc'])
            ->withLateLookup(function ($builder, $ids) {
                $builder->select('traffic_violations.*, s.name AS school_name, s.education_level,
                                  st.full_name AS student_name, st.class_grade, st.class_name,
                                  pa.full_name AS officer_name, pa.rank_title, pa.nrp,
                                  (SELECT STRING_AGG(vmt.title, \', \') 
                                   FROM traffic_violation_items tvi 
                                   JOIN violation_master_types vmt ON vmt.id = tvi.violation_type_id 
                                   WHERE tvi.violation_id = traffic_violations.id) AS violation_items,
                                  (SELECT COALESCE(SUM(vmt.max_fine_idr), 0) 
                                   FROM traffic_violation_items tvi 
                                   JOIN violation_master_types vmt ON vmt.id = tvi.violation_type_id 
                                   WHERE tvi.violation_id = traffic_violations.id) AS total_fine_idr')
                        ->join('schools s', 's.id = traffic_violations.school_id', 'left')
                        ->join('students st', 'st.id = traffic_violations.student_id', 'left')
                        ->join('police_accounts pa', 'pa.id = traffic_violations.polantas_account_id', 'left');
            })
            ->addColumn('ticket_html', function ($row) {
                $tNum = esc($row->ticket_number);
                $dt = date('d M Y, H:i', strtotime($row->incident_at ?? 'now'));
                return '
                <div class="font-mono font-bold text-slate-900">' . $tNum . '</div>
                <div class="text-[11px] text-slate-400 font-mono mt-0.5">' . $dt . ' WIB</div>';
            })
            ->addColumn('student_html', function ($row) {
                $stName = esc($row->student_name ?? 'Pelajar (Umum)');
                $grade  = esc($row->class_grade ?? '-');
                $cls    = esc($row->class_name ?? '');
                return '
                <div class="font-bold text-slate-800">' . $stName . '</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Kelas: <span class="font-semibold text-slate-600">' . $grade . ' ' . $cls . '</span></div>';
            })
            ->addColumn('school_html', function ($row) {
                return '<span class="font-semibold text-slate-700">' . esc($row->school_name ?? '-') . '</span>';
            })
            ->addColumn('violation_html', function ($row) {
                $items = esc($row->violation_items ?: 'Pelanggaran Lalu Lintas');
                $loc   = esc($row->incident_location ?? 'Wilayah Mojokerto');
                return '
                <div class="text-xs text-rose-700 font-medium line-clamp-2">
                    <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i>
                    ' . $items . '
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5 truncate">Lokasi: ' . $loc . '</div>';
            })
            ->addColumn('officer_html', function ($row) {
                $offName = esc(($row->rank_title ?? '') . ' ' . ($row->officer_name ?? '-'));
                $nrp     = esc($row->nrp ?? '-');
                return '
                <div class="font-bold text-slate-800">' . $offName . '</div>
                <div class="text-[11px] font-mono text-slate-400">NRP: ' . $nrp . '</div>';
            })
            ->addColumn('fine_html', function ($row) {
                return 'Rp ' . number_format($row->total_fine_idr ?? 0, 0, ',', '.');
            })
            ->make();
    }
}
