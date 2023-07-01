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

    protected int $offset;
    protected int $limit;


    protected function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function table(string $table): DBQuery
    {
        return new self($table);
    }

    public function first(array $fields = ['*'])
    {
        if (!$this->valideteSelect()) {
            $this->select($fields);
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

    public function sum(string $field, string $groupColumn): mixed
    {
        $this->type = 'select';
        $this->fields = ["sum($field) as {$field}_sum"];
        $this->group([$groupColumn]);

        return ($result = ($this->getExecutor())()) ? $result[0] : 0;
    }

    public function insert(array $fields): mixed
    {
        $this->type = 'insert';
        $this->fields = $fields;

        return ($this->getExecutor())();
    }

    public function updateQuery(array $fields): mixed
    {
        $this->type = 'update';
        $this->fields = $fields;

        return ($this->getExecutor())();
    }

    public function deleteQuery($id, $idName = 'id'): mixed
    {
        $this->type = 'delete';

        return ($this->where($idName, '=', $id)->getExecutor())();
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
        $this->group[] = implode(',', $fields);

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

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function query(bool $valueted = false): string
    {
        return match ($this->type) {
            'select' => $this->getSelectQuery($valueted),
            'insert' => $this->getInsertQuery($valueted),
            'updata' => $this->getUpdateQuery($valueted),
            'delete' => $this->getDeleteQuery($valueted),
        };
    }

    private function getExecutor(): \Closure
    {
        $db = DB::init();
        return match ($this->type) {
            'select' => function () use ($db) {
                return $db->execute($this->getSelectQuery(), $this->getConditionsValues(), true);
            },
            'insert' => function () use ($db) {
                return $db->execute($this->getInsertQuery(), array_values($this->fields), returnId: true);
            },
            'update' => function () use ($db) {
                return $db->execute($this->getUpdateQuery(), [...array_values($this->fields), ...$this->getConditionsValues()]);
            },
            'delete' => function () use ($db) {
                return $db->execute($this->getDeleteQuery(), $this->getConditionsValues());
            },
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
            return $query . sprintf(" %s %s %s ?",
                    empty($query) ? "" : $condition['boolean'],
                    $condition['field'],
                    $condition['operation']);
        }, "");
    }

    /**
     * @return string
     */
    private function getSelectQuery(bool $valueted = false): string
    {
        $base = sprintf(/** @lang text */ "SELECT %s FROM %s WHERE %s",
            implode(',', $this->fields), $this->tableName, $this->getConditionsQuery());

        foreach ($this->joins as $join) {
            $base .= sprintf("%s JOIN %s as %s ON %s", $join['type'], $join['table'], $join['alias'], $join['on']);
        }

        $orderBy = implode(',', $this->order);

        if (isset($this->limit) && $this->limit > 0) {
            $base .= " LIMIT $this->limit";
        }

        if (isset($this->offset) && $this->offset > 0) {
            $base .= " OFFSET $this->offset";
        }

        if(isset($this->group)){
            $base .= " GROUP BY " . implode(',',$this->group);
        }

        if (!empty($orderBy)) {
            $base .= "ORDER $orderBy";
        }

        if ($valueted) {
            $base = $this->mergeQueryValues($base, $this->getConditionsValues());
        }
        return $base;
    }

    /**
     * @return string
     */
    private function getInsertQuery(bool $valueted = false): string
    {
        return sprintf(/** @lang text */ "INSERT INTO %s (%s) VALUES (%s)",
            $this->tableName,
            implode(',', array_keys($this->fields)),
            substr(str_repeat(',?', sizeof($this->fields)), 1));
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

    private function getUpdateQuery(bool $valueted = false): string
    {
        return sprintf(/** @lang text */ "UPDATE %s SET %s WHERE (%s)",
            $this->tableName,
            implode(' = ?,', array_keys($this->fields)) . ' = ?',
            $this->getConditionsQuery());
    }

    private function getDeleteQuery(bool $valueted = false): string
    {
        return sprintf(/** @lang text */ "DELETE FROM %s WHERE (%s)",
            $this->tableName,
            $this->getConditionsQuery());
    }

    private function mergeQueryValues(string $query, array $values): string
    {
        while (($pos = strpos($query, '?')) && !empty($values)) {
            $query = substr_replace($query, array_shift($values), $pos);
        }
        return $query;
    }

}