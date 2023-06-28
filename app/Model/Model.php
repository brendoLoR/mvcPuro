<?php

namespace App\Model;

use App\Core\Database\DBQuery;

class Model
{
    protected string $table;
    protected string $idColumn = 'id';
    protected DBQuery $query;

    protected static array $hidden = [];

    public function __construct(
        public ?array $attributes = null,
    )
    {
        $this->filterHiddens();
    }

    protected function getTableName(): string
    {
        return $this->table;
    }

    protected static function beforeSave(Model $model): Model
    {
        return $model;
    }

    protected function getIdColumn(): string
    {
        return $this->idColumn;
    }

    public function query(): DBQuery
    {
        if (!isset($this->query)) {
            $this->query = DBQuery::table($this->getTableName());
        }

        return $this->query;
    }

    public static function find($id, $attributes = ['*'], bool $filter = true): Model|false
    {
        $model = new static();
        if (!$finded = $model->query()
            ->select($attributes)
            ->where('id', '=', "$id")
            ->first()) {
            return false;
        }
        $model->attributes = $finded;
        if ($filter){
            $model->filterHiddens();
        }

        return $model;
    }

    public static function all($attributes = ['*'])
    {
        $finded = (new static())->query()->select($attributes)->get();

        return array_map(function ($model){
            $model = new static($model);
            $model->filterHiddens();
            return $model;
        }, $finded);
    }

    public function delete($id = null): DBQuery|bool
    {
        if ($id) {
            return $this->query()->delete($this->getIdColumn(), $id);
        }
        if ($id = $this->getAttribute($this->getIdColumn())) {
            return $this->query()->delete($this->getIdColumn(), $id);
        }
        return false;
    }

    public function save(): DBQuery|Model|bool
    {
        if (empty($this->attributes)) {
            return false;
        }

        $model = static::beforeSave($this);

        $query = $this->query();

        if ($id = $model->getAttribute($model->getIdColumn())) {
            return $model->update($this->attributes);
        }

        if (!$created = $query->insert($model->attributes)) {
            return false;
        }

        $model->setAttribute($model->getIdColumn(), $created);

        return $model;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return Model
     */
    public function setAttribute(string $attribute, mixed $value): static
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttribute(string $attribute): mixed
    {
        return $this->attributes[$attribute] ?? false;
    }

    protected function filterHiddens(): void
    {
        foreach (static::$hidden as $hidden) {
            unset($this->attributes[$hidden]);
        }
    }

    /**
     * @param DBQuery $query
     * @param Model $model
     * @param mixed $id
     * @return Model|false
     */
    protected function update(array $attributes): Model|false
    {
        if ($this->query()->where($this->getIdColumn(), '=', $this->getAttribute($this->getIdColumn()))
            ->update($attributes)) {
            return $this;
        }
        return false;
    }


}