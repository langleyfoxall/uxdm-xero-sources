<?php
namespace LangleyFoxall\uxdm\Traits;

trait HasMaps
{
	/** @var array|\Closure[] $dataRowMaps */
	protected $dataRowMaps;

	/**
	 * @param \Closure $closure
	 *
	 * @return $this
	 */
	public function setDataRowMap(\Closure $closure)
	{
		$this->dataRowMaps = [$closure];

		return $this;
	}

	/**
	 * @param array $maps
	 *
	 * @return $this
	 */
	public function mergeDataRowMaps(array $maps)
	{
		$this->dataRowMaps = array_merge(
			$this->dataRowMaps,
			$maps
		);

		return $this;
	}

	/**
	 * @param \Closure $closure
	 *
	 * @return $this
	 */
	public function prependDataRowMap(\Closure $closure)
	{
		$this->dataRowMaps = array_merge(
			[$closure],
			$this->dataRowMaps
		);

		return $this;
	}

	/**
	 * @param \Closure $closure
	 *
	 * @return $this
	 */
	public function addDataRowMap(\Closure $closure)
	{
		$this->dataRowMaps = array_merge(
			$this->dataRowMaps,
			[$closure]
		);

		return $this;
	}
}
