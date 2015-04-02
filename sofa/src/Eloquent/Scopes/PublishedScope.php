<?php namespace Sofa\Eloquent\Scopes;

use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class PublishedScope implements ScopeInterface {

	/**
	 * Apply scope on the query.
	 * 
	 * @param \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function apply(Builder $builder)
	{
		$column = $builder->getModel()->getQualifiedPublishedColumn();

		$builder->where($column, '=', 1);

		$this->addWithDrafts($builder);
	}

	/**
	 * Remove scope from the query.
	 * 
	 * @param  Builder $builder
	 * @return void
	 */
	public function remove(Builder $builder)
	{
		$query = $builder->getQuery();

		$column = $builder->getModel()->getQualifiedPublishedColumn();

		$bindingKey = 0;

		foreach ((array) $query->wheres as $key => $where)
		{
			if ($this->isPublishedConstraint($where, $column))
			{
				$this->removeWhere($query, $key);

				// Here SoftDeletingScope simply removes the where
				// but since we use Basic where (not Null type)
				// we need to get rid of the binding as well
				$this->removeBinding($query, $bindingKey);
			}
			
			// Check if where is either NULL or NOT NULL type,
			// if that's the case, don't increment the key
			// since there is no binding for these types
			if ( ! in_array($where['type'], ['Null', 'NotNull'])) $bindingKey++;
		}
	}

	/**
	 * Remove scope constraint from the query.
	 * 
	 * @param  \Illuminate\Database\Query\Builder  $builder
	 * @param  int  $key
	 * @return void
	 */
	protected function removeWhere(BaseBuilder $query, $key)
	{
		unset($query->wheres[$key]);

		$query->wheres = array_values($query->wheres);
	}

	/**
	 * Remove scope constraint from the query.
	 * 
	 * @param  \Illuminate\Database\Query\Builder  $builder
	 * @param  int  $key
	 * @return void
	 */
	protected function removeBinding(BaseBuilder $query, $key)
	{
		$bindings = $query->getRawBindings()['where'];

		unset($bindings[$key]);

		$query->setBindings($bindings);
	}

	/**
	 * Check if given where is the scope constraint.
	 * 
	 * @param  array   $where
	 * @param  string  $column
	 * @return boolean
	 */
	protected function isPublishedConstraint(array $where, $column)
	{
		return ($where['type'] == 'Basic' && $where['column'] == $column && $where['value'] == 1);
	}

	/**
	 * Extend Builder with custom method.
	 * 
	 * @param \Illuminate\Database\Eloquent\Builder  $builder
	 */
	protected function addWithDrafts(Builder $builder)
	{
		$builder->macro('withDrafts', function(Builder $builder)
		{
			$this->remove($builder);

			return $builder;
		});
	}
}
