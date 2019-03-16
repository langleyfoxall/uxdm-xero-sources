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
	 * @param int   $page
	 * @param array $fieldsToRetrieve
	 *
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
		$collection = $this->query->page($page)->execute();

		$this->collectionSource =
			new XeroCollectionSource($collection);

		$this->collectionSource->perPage(
			count($collection)
		);
	}
}
