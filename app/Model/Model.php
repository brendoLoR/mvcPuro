<?php

namespace App\Model;

use App\Core\Database\DBQuery;
use App\Core\Http\Request;

class Model extends DBQuery
{
    protected string $table;
    protected string $idColumn = 'id';

    protected static array $hidden = [];

    public function __construct(
        public ?array $attributes = null,
    )
    {
        parent::__construct($this->getTableName());
        $this->filterHiddens();
    }

    protected function getTableName(): string
    {
        return $this->table;
    }

    /**
     * @param string $column
     */
    public static function addHidden(string $column): void
    {
        self::$hidden[] = $column;
    }

    protected static function beforeSave(Model $model): Model
    {
        return $model;
    }

    protected static function afterFind(Model $model): Model
    {
        return $model;
    }

    protected static function beforeUpdate(array $attributes): array
    {
        return $attributes;
    }

    protected function getIdColumn(): string
    {
        return $this->idColumn;
    }

    public static function find($id, $attributes = ['*'], bool $filter = true): Model|false
    {
        $model = new static();

        if ($countingQuery = $model->getWithCountQuery()) {
            $attributes[] = $countingQuery;
        }

        if (!$finded = $model
            ->select($attributes)
            ->where('id', '=', "$id")
            ->first()) {
            return false;
        }

        $model->attributes = $finded;

        if ($filter) {
            $model->filterHiddens();
        }

        return static::afterFind($model);
    }

    public static function all($attributes = ['*'])
    {
        $model = new static();

        if ($countingQuery = $model->getWithCountQuery()) {
            $attributes[] = $countingQuery;
        }

        $finded = $model->select($attributes)->get();

        return array_map(function ($model) {
            $model = new static($model);
            $model->filterHiddens();
            return static::afterFind($model);
        }, $finded);
    }

    public function delete($id = null, $idName = null): DBQuery|bool
    {
        if ($id) {
            return $this->deleteQuery($id, $this->getIdColumn());
        }
        if ($id = $this->getAttribute($this->getIdColumn())) {
            return $this->deleteQuery($id, $this->getIdColumn());
        }
        return false;
    }

    public function save(): DBQuery|Model|bool
    {
        if (empty($this->attributes)) {
            return false;
        }

        $model = static::beforeSave($this);

        $query = $this;

        if ($id = $model->getAttribute($model->getIdColumn())) {
            return $model->update($this->attributes);
        }

        if (!$created = $query->insert($model->attributes)) {
            return false;
        }

        $model->setAttribute($model->getIdColumn(), $created);

        return $model;
    }

    public static function create(array $attributes): DBQuery|Model|bool
    {
        $model = new static();
        $model->attributes = $attributes;

        return $model->save();
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
    public function update(array $attributes): Model|false
    {
        $attributes = self::beforeUpdate($attributes);
        if (parent::where($this->getIdColumn(), '=', $this->getAttribute($this->getIdColumn()))
            ->updateQuery($attributes)) {
            return $this;
        }
        return false;
    }

    protected function getWithCountQuery(): null|string
    {
        return null;
    }


    public static function paginate(int $perPage = 10, array $attributes = ['*'], int $currentPage = null): array|false
    {
        $model = new static();
        $request = Request::getRequest();

        $numRows = $model->select(['COUNT(*) as selected_rows'])->first()['selected_rows'];

        $numPages = round($numRows / $perPage);

        if ($countingQuery = $model->getWithCountQuery()) {
            $attributes[] = $countingQuery;
        }

        $page = $currentPage ?:
            $request->getData('page') ?: 1;

        if ($page < 1) $page = 1;
        $nextPage = $page + 1;
        $previusPage = $page <= 1 ? null : $page - 1;

        $offset = $perPage * ($page - 1);
        $limit = $perPage * $page;

        if ($limit >= $numRows) {
            $limit = $numRows;
            $nextPage = null;
        }

        if ($limit < $offset) return false;

        $finded = $model->select($attributes)
            ->offset($offset)
            ->limit($limit)->get();

        $itens = array_map(function ($model) {
            $model = new static($model);
            $model->filterHiddens();
            return static::afterFind($model);
        }, $finded);

        return [
            'itens' => $itens,
            'currentPage' => $page,
            'numPages' => $numPages,
            'nextPage' => $nextPage,
            'previusPage' => $previusPage,
            'nextPageUrl' => $nextPage ? $request->getUri(['page' => $nextPage]) : null,
            'previusPageUrl' => $previusPage ? $request->getUri(['page' => $previusPage]) : null,
        ];
    }

}