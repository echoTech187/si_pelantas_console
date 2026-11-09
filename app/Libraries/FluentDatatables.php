<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * FluentDatatables Library for CodeIgniter 4
 * 
 * High-Performance Server-Side DataTables engine with native support for:
 * 1. Split-Query Architecture (Deferred Join / Late Row Lookup) for lightning-fast pagination on 10M+ rows
 * 2. Optimized single-pass count (bypasses redundant filtered count when search is empty)
 * 3. PostgreSQL & MySQL cross-database compatibility (case-insensitive ILIKE / LIKE, UUID & BigInt PK support)
 * 4. Fluent API compatible with Yajra Laravel DataTables & CI3 FluentDatatables
 */
class FluentDatatables
{
    protected BaseConnection $db;
    protected ?string $table = null;
    protected string $primary_key = 'id';

    // Core query parameters
    protected string|array $select = '*';
    protected array $where = [];
    protected array $where_in = [];
    protected array $joins = [];
    protected array $group_by = [];

    // DataTables configuration
    protected array $column_order = [];
    protected array $column_search = [];
    protected array $order = [];

    // Output modifiers
    protected array $add_columns = [];
    protected array $edit_columns = [];
    protected mixed $result_processor = null;

    // Performance and bypass flags
    protected ?int $manual_recordsTotal = null;
    protected ?int $manual_recordsFiltered = null;
    protected ?array $manual_data = null;
    protected bool $use_fulltext = false;
    protected string $fulltext_mode = 'BOOLEAN MODE';

    // Callbacks
    protected mixed $late_lookup_callback = null;
    protected mixed $filter_callback = null;

    // Request parameters cache
    protected array $params = [];

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * Static factory method for fluent instantiation
     */
    public static function of(string $table, ?BaseConnection $db = null): self
    {
        $instance = new self($db);
        $instance->table = $table;
        return $instance;
    }

    /**
     * Set the primary table name
     */
    public function setTable(string $table): self
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    /**
     * Set primary key for Split-Query / Late Row Lookup (e.g., 'id', 'uuid')
     */
    public function setPrimaryKey(string $key): self
    {
        $this->primary_key = $key;
        return $this;
    }

    /**
     * Define the SELECT columns
     */
    public function select(string|array $select): self
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Add a WHERE condition
     */
    public function where(mixed $key, mixed $value = null, bool $escape = true): self
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->where[] = ['key' => $k, 'value' => $v, 'escape' => $escape];
            }
        } else {
            $this->where[] = ['key' => $key, 'value' => $value, 'escape' => $escape];
        }
        return $this;
    }

    /**
     * Add a WHERE IN condition
     */
    public function where_in(string $key, array $values): self
    {
        return $this->whereIn($key, $values);
    }

    public function whereIn(string $key, array $values): self
    {
        $this->where_in[] = ['key' => $key, 'values' => $values];
        return $this;
    }

    /**
     * Add a JOIN clause
     */
    public function join(string $table, string $cond, string $type = ''): self
    {
        $this->joins[] = ['table' => $table, 'cond' => $cond, 'type' => $type];
        return $this;
    }

    /**
     * Group results by a column
     */
    public function groupBy(string|array $column): self
    {
        $this->group_by[] = $column;
        return $this;
    }

    /**
     * Map frontend columns (numeric indices) to database columns for ordering
     */
    public function set_column_order(array $columns): self
    {
        $this->column_order = $columns;
        return $this;
    }

    public function set_orderable_columns(array $columns): self
    {
        return $this->set_column_order($columns);
    }

    /**
     * Define columns for global search
     */
    public function set_column_search(array $columns): self
    {
        $this->column_search = $columns;
        return $this;
    }

    public function set_searchable_columns(array $columns): self
    {
        return $this->set_column_search($columns);
    }

    /**
     * Default ORDER BY fallback
     */
    public function set_default_order(array $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Add a dynamic computed column to the JSON output
     */
    public function addColumn(string $name, callable $callback): self
    {
        $this->add_columns[$name] = $callback;
        return $this;
    }

    /**
     * Modify an existing column's output in the JSON response
     */
    public function editColumn(string $name, callable $callback): self
    {
        $this->edit_columns[$name] = $callback;
        return $this;
    }

    /**
     * Bypass total count (for extreme performance with cached count)
     */
    public function setRecordsTotal(int $total): self
    {
        $this->manual_recordsTotal = $total;
        return $this;
    }

    /**
     * Bypass filtered count
     */
    public function setRecordsFiltered(int $count): self
    {
        $this->manual_recordsFiltered = $count;
        return $this;
    }

    /**
     * Override or inject POST/GET parameters
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Custom query filter callback
     */
    public function filter(callable $callback): self
    {
        $this->filter_callback = $callback;
        return $this;
    }

    /**
     * Post-processing callback on final result set
     */
    public function modifyResult(callable $callback): self
    {
        $this->result_processor = $callback;
        return $this;
    }

    /**
     * Provide static data directly, bypassing DB
     */
    public function setData(?array $data): self
    {
        $this->manual_data = $data;
        return $this;
    }

    /**
     * ENABLE HIGH-PERFORMANCE SPLIT-QUERY (LATE ROW LOOKUP) MODE
     * 
     * Heavy joins and large payloads will ONLY be executed on the paginated primary IDs,
     * reducing query time from tens of seconds down to milliseconds on millions of records.
     */
    public function withLateLookup(callable $callback): self
    {
        $this->late_lookup_callback = $callback;
        return $this;
    }

    /**
     * Backward compatibility wrapper for generate()
     */
    public function make(bool $json = true)
    {
        return $this->generate(null, $json);
    }

    /**
     * Main DataTables JSON generation
     */
    public function generate(?callable $late_lookup_callback = null, bool $json = true)
    {
        if ($late_lookup_callback !== null) {
            $this->late_lookup_callback = $late_lookup_callback;
        }

        // Bypass everything if manual data is supplied
        if ($this->manual_data !== null) {
            return $this->_handle_manual_data($json);
        }

        $request = service('request');
        $rawPost = $request->getPost();
        if (empty($rawPost)) {
            $rawPost = $request->getGet();
        }
        $params = array_merge($rawPost ?? [], $this->params);

        // ====================================================================
        // PHASE 1: COUNT TOTAL RECORDS (UNFILTERED)
        // ====================================================================
        if ($this->manual_recordsTotal !== null) {
            $recordsTotal = $this->manual_recordsTotal;
        } else {
            $totalBuilder = $this->db->table($this->table);
            $this->apply_base_query($totalBuilder);
            $recordsTotal = (int) $totalBuilder->countAllResults(false);
        }

        // ====================================================================
        // PHASE 2: COUNT FILTERED RECORDS (SEARCH & CUSTOM FILTERS)
        // ====================================================================
        $searchVal = isset($params['search']['value']) ? trim((string) $params['search']['value']) : '';
        $mainBuilder = $this->db->table($this->table);
        $this->apply_base_query($mainBuilder);

        if ($this->filter_callback !== null) {
            call_user_func($this->filter_callback, $mainBuilder, $params);
        }

        $this->apply_search($mainBuilder, $searchVal);

        if ($this->manual_recordsFiltered !== null) {
            $recordsFiltered = $this->manual_recordsFiltered;
        } elseif ($searchVal === '' && $this->filter_callback === null) {
            // OPTIMIZATION: If no search query and no extra filter callback, filtered count == total count!
            $recordsFiltered = $recordsTotal;
        } else {
            $filterCountBuilder = clone $mainBuilder;
            $recordsFiltered = (int) $filterCountBuilder->countAllResults(false);
        }

        // ====================================================================
        // PHASE 3: APPLY ORDERING & PAGINATION
        // ====================================================================
        $this->apply_order($mainBuilder, $params);

        $start  = isset($params['start']) ? (int) $params['start'] : 0;
        $length = isset($params['length']) ? (int) $params['length'] : 10;

        if ($length > 0) {
            $mainBuilder->limit($length, $start);
        }

        // ====================================================================
        // PHASE 4: EXECUTE QUERY (SPLIT-QUERY OR STANDARD)
        // ====================================================================
        if ($this->late_lookup_callback !== null) {
            $result = $this->_execute_split_query($mainBuilder);
        } else {
            $result = $mainBuilder->get()->getResult();
        }

        // Apply final custom result processors
        if ($this->result_processor !== null) {
            $processor = $this->result_processor;
            $result = $processor($result);
        }

        return $this->_format_output($result, $recordsTotal, $recordsFiltered, $params, $json);
    }

    /**
     * Executes the High-Performance Split-Query (Late Row Lookup) Architecture
     */
    protected function _execute_split_query(BaseBuilder $mainBuilder): array
    {
        $pkColumn = $this->table ? "{$this->table}.{$this->primary_key}" : $this->primary_key;

        // 1. Fetch strictly ONLY the Primary IDs for the paginated window
        $idBuilder = clone $mainBuilder;
        $idBuilder->select($pkColumn);
        $idResults = $idBuilder->get()->getResultArray();

        if (empty($idResults)) {
            return [];
        }

        // Extract raw array of IDs
        $ids = [];
        foreach ($idResults as $row) {
            $ids[] = reset($row);
        }

        // 2. Fetch full rows strictly for those paginated IDs
        $lookupBuilder = $this->db->table($this->table);
        $lookupBuilder->whereIn($pkColumn, $ids);

        // Execute Developer's Heavy JOINs & Selects
        call_user_func($this->late_lookup_callback, $lookupBuilder, $ids);

        // Re-apply deterministic ordering based on the paginated ID array
        // Works seamlessly across PostgreSQL and MySQL for UUID and BigInt
        $orderCase = "CASE " . $this->db->protectIdentifiers($pkColumn);
        foreach ($ids as $index => $id) {
            $escapedId = $this->db->escape($id);
            $orderCase .= " WHEN {$escapedId} THEN {$index}";
        }
        $orderCase .= " ELSE 999999 END";
        $lookupBuilder->orderBy($orderCase, 'ASC', false);

        return $lookupBuilder->get()->getResult();
    }

    /**
     * Apply base WHERE, JOIN, and GROUP BY conditions
     */
    protected function apply_base_query(BaseBuilder $builder): void
    {
        $builder->select($this->select);

        foreach ($this->joins as $j) {
            $builder->join($j['table'], $j['cond'], $j['type']);
        }

        foreach ($this->where as $w) {
            $builder->where($w['key'], $w['value'], $w['escape']);
        }

        foreach ($this->where_in as $win) {
            $builder->whereIn($win['key'], $win['values']);
        }

        foreach ($this->group_by as $g) {
            $builder->groupBy($g);
        }
    }

    /**
     * Applies search filters across designated columns (PostgreSQL ILIKE & MySQL LIKE)
     */
    protected function apply_search(BaseBuilder $builder, string $searchValue): void
    {
        if ($searchValue !== '' && !empty($this->column_search)) {
            $builder->groupStart();

            $isPgsql = ($this->db->DBDriver === 'Postgre');
            $i = 0;

            foreach ($this->column_search as $column) {
                if ($isPgsql) {
                    // PostgreSQL: Case-insensitive search using ILIKE or CAST
                    $condition = "CAST({$column} AS TEXT) ILIKE " . $this->db->escape('%' . $searchValue . '%');
                    if ($i === 0) {
                        $builder->where($condition, null, false);
                    } else {
                        $builder->orWhere($condition, null, false);
                    }
                } else {
                    // MySQL / SQLite
                    if ($i === 0) {
                        $builder->like($column, $searchValue);
                    } else {
                        $builder->orLike($column, $searchValue);
                    }
                }
                $i++;
            }

            $builder->groupEnd();
        }
    }

    /**
     * Applies correct column sorting based on DataTables request
     */
    protected function apply_order(BaseBuilder $builder, array $params): void
    {
        if (isset($params['order'][0]['column']) && !empty($this->column_order)) {
            $colIndex = (int) $params['order'][0]['column'];
            $dir = strtolower($params['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

            if (isset($this->column_order[$colIndex])) {
                $orderColumn = $this->column_order[$colIndex];
                if (!empty($orderColumn)) {
                    $builder->orderBy($orderColumn, $dir);
                }
            }
        } elseif (!empty($this->order)) {
            foreach ($this->order as $col => $dir) {
                $builder->orderBy($col, strtoupper($dir));
            }
        }
    }

    /**
     * Formats final JSON payload matching DataTables protocol
     */
    protected function _format_output(array $result, int $recordsTotal, int $recordsFiltered, array $params, bool $json = true)
    {
        $data = [];
        $start = isset($params['start']) ? (int) $params['start'] : 0;
        $no = $start;

        foreach ($result as $row) {
            $no++;
            $item = (array) $row;
            $item['DT_RowIndex'] = $no;

            // Apply editColumn callbacks
            foreach ($this->edit_columns as $col => $callback) {
                if (array_key_exists($col, $item)) {
                    $item[$col] = $callback($row);
                }
            }

            // Apply addColumn callbacks
            foreach ($this->add_columns as $col => $callback) {
                $item[$col] = $callback($row);
            }

            $data[] = $item;
        }

        $output = [
            'draw'            => isset($params['draw']) ? (int) $params['draw'] : 0,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];

        // Append fresh CSRF token if active in CI4
        if (config('Security')->csrfProtection !== 'cookie' || true) {
            $output['csrf_token'] = csrf_token();
            $output['csrf_hash']  = csrf_hash();
        }

        if ($json) {
            return service('response')
                ->setContentType('application/json')
                ->setJSON($output);
        }

        return $output;
    }

    /**
     * Handles manual static data bypassing database
     */
    protected function _handle_manual_data(bool $json)
    {
        $result = $this->manual_data ?? [];
        $recordsTotal = $this->manual_recordsTotal ?? count($result);

        $request = service('request');
        $params = array_merge($request->getPost() ?? [], $this->params);

        $recordsFiltered = $this->manual_recordsFiltered ?? count($result);

        if (isset($params['length']) && (int) $params['length'] > 0) {
            $start  = isset($params['start']) ? (int) $params['start'] : 0;
            $length = (int) $params['length'];
            $result = array_slice($result, $start, $length);
        }

        if ($this->result_processor !== null) {
            $processor = $this->result_processor;
            $result = $processor($result);
        }

        return $this->_format_output($result, $recordsTotal, $recordsFiltered, $params, $json);
    }

    /**
     * Resets all internal query builder state
     */
    public function reset(): self
    {
        $this->table = null;
        $this->primary_key = 'id';
        $this->select = '*';
        $this->where = [];
        $this->where_in = [];
        $this->joins = [];
        $this->group_by = [];
        $this->column_order = [];
        $this->column_search = [];
        $this->order = [];
        $this->add_columns = [];
        $this->edit_columns = [];
        $this->manual_recordsTotal = null;
        $this->manual_recordsFiltered = null;
        $this->manual_data = null;
        $this->result_processor = null;
        $this->late_lookup_callback = null;
        $this->filter_callback = null;
        $this->params = [];
        return $this;
    }
}

