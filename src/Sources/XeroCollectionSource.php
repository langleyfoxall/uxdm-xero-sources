<?php
namespace LangleyFoxall\uxdm\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;
use Illuminate\Support\Arr;
use XeroPHP\Remote\Collection;
use XeroPHP\Remote\Model;

class XeroCollectionSource implements SourceInterface, \ArrayAccess
{
    /** @var Collection|Model[] */
    protected $collection;

    /** @var array|string[] $fields */
    protected $fields;

    /** @var int $perPage */
    protected $perPage = 1000;

    /**
     * @param Collection|Model $entity
     */
    public function __construct($entity)
    {
        if ($entity instanceof Collection) {
            $this->collection = $entity;
        } else if (is_subclass_of($entity, Model::class)) {
            $collection = new Collection;
            $collection[] = $entity;

            $this->collection = $collection;
        } else {
            throw new \InvalidArgumentException(
                'Entity must be instance of Remote\Collection or Remote\Model'
            );
        }

        $this->fields = $this->getFieldsFromFirstRow();
    }

    /**
     * @param int $count
     * @return $this|int
     */
    public function perPage(int $count = null)
    {
        if (empty($count)) {
            return $this->perPage;
        }

        $this->perPage = $count;

        return $this;
    }

    /**
     * @return Collection|Model[]
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getFieldsFromFirstRow()
    {
        if (!$this->countDataRows()) {
            return [];
        }

        return array_keys($this->collection->first()->getProperties());
    }

    /**
     * @param int   $page
     * @param array $fieldsToRetrieve
     * @return array
     */
    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        if (!$this->countDataRows()) {
            return [];
        }

        $offset = (($page - 1) * $this->perPage);
        $rows = array_slice((array)$this->collection, $offset, $this->perPage);

        return array_map(function (Model $model) use ($fieldsToRetrieve) {
            $model = $model->toStringArray();

            if (!empty($fieldsToRetrieve)) {
                $model = array_filter(
                    $model,
                    function ($key) use ($fieldsToRetrieve) {
                        return in_array($key, $fieldsToRetrieve);
                    },
                    ARRAY_FILTER_USE_KEY
                );
            }

            $dataRow = new DataRow;
            $dottedArray = Arr::dot($model);


            foreach ($dottedArray as $field => $value) {
                $dataRow->addDataItem(new DataItem($field, $value));
            }

            return $dataRow;
        }, $rows);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return int
     */
    public function countDataRows()
    {
        return count($this->collection);
    }

    /**
     * @return int
     */
    public function countPages()
    {
        return ceil($this->countDataRows() / $this->perPage);
    }

    /**
     * @param integer $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * @param integer $offset
     * @return Model
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * @param integer $offset
     * @param Model   $value
     */
    public function offsetSet($offset, $value)
    {
        if (!is_subclass_of($value, Model::class)) {
            throw new \InvalidArgumentException(
                'Entity must be instance of Remote\Model'
            );
        }

        $this->collection[$offset] = $value;
    }

    /**
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }
}
