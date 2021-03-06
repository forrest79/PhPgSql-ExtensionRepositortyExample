<?php declare(strict_types=1);

namespace App\Database;

use Forrest79\Database;

/**
 * @method Row|NULL fetch()
 * @method array<Row> fetchAll()
 */
final class Query extends Database\Fluent\Query
{
	private ?Joins $innerJoins = NULL;

	private ?Joins $leftJoins = NULL;



	public function joins(): Joins
	{
		if ($this->innerJoins === NULL) {
			$this->innerJoins = new Joins($this, Joins::INNER_JOIN);
		}
		return $this->innerJoins;
	}


	public function leftJoins(): Joins
	{
		if ($this->leftJoins === NULL) {
			$this->leftJoins = new Joins($this, Joins::LEFT_JOIN);
		}
		return $this->leftJoins;
	}


	/**
	 * @param class-string $repositoryClass
	 * @return static
	 */
	public function joinRepository(
		string $repositoryClass,
		string $repositoryAlias,
		string $joinColumn,
		?string $repositoryJoinColumn = NULL
	): self
	{
		return $this->createRepositoryJoin(
			Joins::INNER_JOIN,
			$repositoryClass,
			$repositoryAlias,
			$joinColumn,
			$repositoryJoinColumn,
		);
	}


	/**
	 * @param class-string $repositoryClass
	 * @return static
	 */
	public function leftJoinRepository(
		string $repositoryClass,
		string $repositoryAlias,
		string $joinColumn,
		?string $repositoryJoinColumn = NULL
	): self
	{
		return $this->createRepositoryJoin(
			Joins::LEFT_JOIN,
			$repositoryClass,
			$repositoryAlias,
			$joinColumn,
			$repositoryJoinColumn,
		);
	}


	/**
	 * @param class-string $repositoryClass
	 * @return static
	 */
	private function createRepositoryJoin(
		int $joinType,
		string $repositoryClass,
		string $repositoryAlias,
		string $joinColumn,
		?string $repositoryJoinColumn = NULL
	): self
	{
		[$tableName, $repositoryJoinColumn] = Database\Repository::meta($repositoryClass, $repositoryJoinColumn);
		$params = [
			$tableName,
			$repositoryAlias,
			sprintf('%s.%s = %s', $repositoryAlias, $repositoryJoinColumn, $joinColumn),
		];

		if ($joinType === Joins::INNER_JOIN) {
			return $this->innerJoin(...$params);
		}
		return $this->leftJoin(...$params);
	}

}
