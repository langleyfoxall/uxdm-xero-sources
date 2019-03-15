<?php
namespace LangleyFoxall\uxdm\Sources;

use DivineOmega\uxdm\Interfaces\SourceInterface;
use XeroPHP\Remote\Query;

class XeroQuerySource implements SourceInterface
{
    /** @var Query $query */
    protected $query;

    /** @var XeroCollectionSource */
    protected $collectionSource;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param int   $page
     * @param array $fieldsToRetrieve
     * @throws \XeroPHP\Remote\Exception
     * @return array
     */
    public function getDataRows($page = 1, $fieldsToRetrieve = [])
    {
        $this->bootstrap($page);

        return $this->collectionSource->getDataRows(
            1, $fieldsToRetrieve
        );
    }

    public function countDataRows()
    {
        return $this->collectionSource->countDataRows();
    }

    public function countPages()
    {
        //
    }

    public function getFields()
    {
        return $this->collectionSource->getFields();
    }

    /**
     * @param int $page
     * @throws \XeroPHP\Remote\Exception
     * @return void
     */
    private function bootstrap($page = 1)
    {
        $collection = $this->query->page($page)->execute();

        $this->collectionSource =
            new XeroCollectionSource($collection);

        $this->collectionSource->perPage(
            count($collection)
        );
    }
}
