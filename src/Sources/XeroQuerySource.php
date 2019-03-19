<?php
namespace LangleyFoxall\uxdm\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use LangleyFoxall\uxdm\Traits\HasFilters;
use LangleyFoxall\uxdm\Traits\HasMaps;
use XeroPHP\Remote\Model;
use XeroPHP\Remote\Query;

class XeroQuerySource implements SourceInterface
{
    use HasFilters, HasMaps;

    /** @var Query $query */
    protected $query;

    /** @var XeroCollectionSource $collectionSource */
    protected $collectionSource;

    /** @var bool $pageable */
    protected $pageable;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;

        $this->pageable(
            $query->getFrom()
        );
    }

    /**
     * @throws \XeroPHP\Remote\Exception
     * @return XeroCollectionSource
     */
    public function collection()
    {
        $this->bootstrapIfNotSet();

        return $this->collectionSource;
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function query(\Closure $closure)
    {
        $out = $closure($this->query);

        if ($out instanceof Query) {
            $this->query = $out;
        }

        return $this;
    }

    /**
     * @param bool|string $pageable
     * @return $this|bool
     */
    public function pageable($pageable = null)
    {
        if (is_bool($pageable)) {
            $this->pageable = $pageable;

            return $this;
        }

        if (is_string($pageable) || is_object($pageable)) {
            $validClass = class_exists($pageable) && is_subclass_of($pageable, Model::class);
            $hasMethod = method_exists($pageable, 'isPageable');

            if ($validClass && $hasMethod) {
                $this->pageable = (new $pageable)->isPageable();
            }

            return $this;
        }

        return $this->pageable;
    }

    /**
     * @param int $page
     * @param array $fieldsToRetrieve
     *
     * @throws \XeroPHP\Remote\Exception
     * @return array
     */
    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        try {
            $this->bootstrap($page);

            return $this->collectionSource->getDataRows(
                1, $fieldsToRetrieve
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @throws \XeroPHP\Remote\Exception
     * @return int
     */
    public function countDataRows()
    {
        $this->bootstrapIfNotSet();

        return $this->collectionSource->countDataRows();
    }

    /**
     * @return int
     */
    public function countPages()
    {
        return 1;
    }

    /**
     * @throws \XeroPHP\Remote\Exception
     * @return array|string[]
     */
    public function getFields()
    {
        $this->bootstrapIfNotSet();

        return $this->collectionSource->getFields();
    }

    /**
     * @throws \XeroPHP\Remote\Exception
     * @return void
     */
    private function bootstrapIfNotSet()
    {
        if (!$this->collectionSource) {
            $this->bootstrap();
        }
    }

    /**
     * @param int $page
     *
     * @throws \XeroPHP\Remote\Exception
     * @return void
     */
    private function bootstrap($page = 1)
    {
        $collection =
            $this->pageable
                ? $this->query->page($page)->execute()
                : $this->query->execute();

        $this->collectionSource =
            new XeroCollectionSource($collection);

        $this->collectionSource->perPage(
            count($collection)
        );

        if (!empty($this->dataRowFilters)) {
            $this->collection()->mergeDataRowFilters(
                $this->dataRowFilters
            );
        }

        if (!empty($this->dataRowMaps)) {
            $this->collection()->mergeDataRowMaps(
                $this->dataRowMaps
            );
        }
    }
}
