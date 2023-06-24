<?php

namespace App\Core\Database;

class DBQuery
{
    protected string $tableName;
    protected string $type = 'select';

    protected array $fields = ['*'];
    protected array $order = [];

    protected ?array $group;
    protected ?array $joins = [];

    protected array $conditions = [];

    protected int $limit;


    private function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function table(string $table): DBQuery
    {
        return new self($table);
    }

    public function first()
    {
        if (!$this->valideteSelect()) {
            return false;
        }

        $this->limit = 1;

        return ($result = ($this->getExecutor())()) ? $result[0] : false;
    }

    public function get(): mixed
    {
        if (!$this->valideteSelect()) {
            return false;
        }

        return ($this->getExecutor())();
    }

    public function insert(array $fields): static
    {
        $this->type = 'insert';
        $this->fields = $fields;

        return $this;
    }

    public function update(array $fields): static
    {
        $this->type = 'update';
        $this->fields = $fields;

        return $this;
    }

    public function select(array $fields = ['*']): static
    {
        $this->type = 'select';
        $this->fields = $fields;

        return $this;
    }

    public function order(string $columnName, string $ordening = 'asc'): static
    {
        $this->order[] = ["$columnName $ordening"];

        return $this;
    }

    public function group(array $fields): static
    {
        $this->group = $fields;

        return $this;
    }

    public function where(string $field, string $operation, string $value, string $boolean = 'and'): static
    {
        $this->conditions[] = ['field' => $field, 'operation' => $operation, 'value' => $value, 'boolean' => $boolean];

        return $this;
    }

    public function join(string $table, string $type, string $alias, string $on): static
    {
        $this->joins[] = ['table' => $table, 'type' => $type, 'alias' => $alias, 'on' => $on];

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function query(): string
    {
        return match ($this->type) {
            'select' => $this->getSelectQuery(),
        };
    }

    private function getExecutor(): \Closure
    {
        $db = DB::init();
        return match ($this->type) {
            'select' => function () use ($db) {
                $query = $this->getSelectQuery();
                var_dump($query);
                return $db->execute($query, $this->getConditionsValues(), true);
            }
        };
    }

    private function getConditionsValues(): array
    {
        return array_map(function ($condition) {
            return $condition['value'];
        }, $this->conditions);
    }

    private function getConditionsQuery(): string
    {
        return array_reduce($this->conditions, function ($query, $condition) {
            return sprintf("%s %s %s ?",
                empty($query) ? "" : $condition['boolean'],
                $condition['field'],
                $condition['operation']);
        }, "");
    }

    /**
     * @return string
     */
    private function getSelectQuery(): string
    {
        $base = sprintf(/** @lang text */ "SELECT %s FROM %s WHERE %s",
            implode(',', $this->fields), $this->tableName, $this->getConditionsQuery());

        foreach ($this->joins as $join) {
            $base .= sprintf("%s JOIN %s as %s ON %s", $join['type'], $join['table'], $join['alias'], $join['on']);
        }

        $orderBy = implode(',', $this->order);
        if (!empty($orderBy)) {
            $base .= "ORDER $orderBy";
        }

        if(isset($this->limit) && $this->limit > 0){
            $base .= " LIMIT $this->limit";
        }
        return $base;
    }

    /**
     * @return bool
     */
    private function valideteSelect(): bool
    {
        if ($this->type != 'select') {
            return false;
        }

        if (empty($this->conditions)) {
            $this->conditions[] = ['field' => '1', 'operation' => '=', 'value' => '1'];
        }

        return true;
    }
}