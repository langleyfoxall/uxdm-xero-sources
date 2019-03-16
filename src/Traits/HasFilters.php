<?php
namespace LangleyFoxall\uxdm\Traits;

trait HasFilters
{
	/** @var array|\Closure[] $dataRowFilters */
	protected $dataRowFilters;

	/**
	 * @param \Closure $closure
	 *
	 * @return $this
	 */
	public function setDataRowFilter(\Closure $closure)
	{
		$this->dataRowFilters = [$closure];

		return $this;
	}

	/**
	 * @param array $filters
	 *
	 * @return $this
	 */
	public function mergeDataRowFilters(array $filters)
	{
		$this->dataRowFilters = array_merge(
			$this->dataRowFilters,
			$filters
		);

		return $this;
	}

	/**
	 * @param \Closure $closure
	 *
	 * @return $this
	 */
	public function prependDataRowFilter(\Closure $closure)
	{
		$this->dataRowFilters = array_merge(
			[$closure],
			$this->dataRowFilters
		);

		return $this;
	}

	/**
	 * @param \Closure $closure
	 *
	 * @return $this
	 */
	public function addDataRowFilter(\Closure $closure)
	{
		$this->dataRowFilters = array_merge(
			$this->dataRowFilters,
			[$closure]
		);

		return $this;
	}
}
