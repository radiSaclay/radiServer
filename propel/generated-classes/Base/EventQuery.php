<?php

namespace Base;

use \Event as ChildEvent;
use \EventQuery as ChildEventQuery;
use \Exception;
use \PDO;
use Map\EventTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'event' table.
 *
 *
 *
 * @method     ChildEventQuery orderByFarmId($order = Criteria::ASC) Order by the farm_id column
 * @method     ChildEventQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildEventQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     ChildEventQuery orderByPublishAt($order = Criteria::ASC) Order by the publish_at column
 * @method     ChildEventQuery orderByBeginAt($order = Criteria::ASC) Order by the begin_at column
 * @method     ChildEventQuery orderByEndAt($order = Criteria::ASC) Order by the end_at column
 * @method     ChildEventQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildEventQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildEventQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildEventQuery groupByFarmId() Group by the farm_id column
 * @method     ChildEventQuery groupByName() Group by the name column
 * @method     ChildEventQuery groupByDescription() Group by the description column
 * @method     ChildEventQuery groupByPublishAt() Group by the publish_at column
 * @method     ChildEventQuery groupByBeginAt() Group by the begin_at column
 * @method     ChildEventQuery groupByEndAt() Group by the end_at column
 * @method     ChildEventQuery groupById() Group by the id column
 * @method     ChildEventQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildEventQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildEventQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildEventQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildEventQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildEventQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildEventQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildEventQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildEventQuery leftJoinFarm($relationAlias = null) Adds a LEFT JOIN clause to the query using the Farm relation
 * @method     ChildEventQuery rightJoinFarm($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Farm relation
 * @method     ChildEventQuery innerJoinFarm($relationAlias = null) Adds a INNER JOIN clause to the query using the Farm relation
 *
 * @method     ChildEventQuery joinWithFarm($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Farm relation
 *
 * @method     ChildEventQuery leftJoinWithFarm() Adds a LEFT JOIN clause and with to the query using the Farm relation
 * @method     ChildEventQuery rightJoinWithFarm() Adds a RIGHT JOIN clause and with to the query using the Farm relation
 * @method     ChildEventQuery innerJoinWithFarm() Adds a INNER JOIN clause and with to the query using the Farm relation
 *
 * @method     ChildEventQuery leftJoinEventProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the EventProduct relation
 * @method     ChildEventQuery rightJoinEventProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EventProduct relation
 * @method     ChildEventQuery innerJoinEventProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the EventProduct relation
 *
 * @method     ChildEventQuery joinWithEventProduct($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the EventProduct relation
 *
 * @method     ChildEventQuery leftJoinWithEventProduct() Adds a LEFT JOIN clause and with to the query using the EventProduct relation
 * @method     ChildEventQuery rightJoinWithEventProduct() Adds a RIGHT JOIN clause and with to the query using the EventProduct relation
 * @method     ChildEventQuery innerJoinWithEventProduct() Adds a INNER JOIN clause and with to the query using the EventProduct relation
 *
 * @method     ChildEventQuery leftJoinPin($relationAlias = null) Adds a LEFT JOIN clause to the query using the Pin relation
 * @method     ChildEventQuery rightJoinPin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Pin relation
 * @method     ChildEventQuery innerJoinPin($relationAlias = null) Adds a INNER JOIN clause to the query using the Pin relation
 *
 * @method     ChildEventQuery joinWithPin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Pin relation
 *
 * @method     ChildEventQuery leftJoinWithPin() Adds a LEFT JOIN clause and with to the query using the Pin relation
 * @method     ChildEventQuery rightJoinWithPin() Adds a RIGHT JOIN clause and with to the query using the Pin relation
 * @method     ChildEventQuery innerJoinWithPin() Adds a INNER JOIN clause and with to the query using the Pin relation
 *
 * @method     \FarmQuery|\EventProductQuery|\PinQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildEvent findOne(ConnectionInterface $con = null) Return the first ChildEvent matching the query
 * @method     ChildEvent findOneOrCreate(ConnectionInterface $con = null) Return the first ChildEvent matching the query, or a new ChildEvent object populated from the query conditions when no match is found
 *
 * @method     ChildEvent findOneByFarmId(int $farm_id) Return the first ChildEvent filtered by the farm_id column
 * @method     ChildEvent findOneByName(string $name) Return the first ChildEvent filtered by the name column
 * @method     ChildEvent findOneByDescription(string $description) Return the first ChildEvent filtered by the description column
 * @method     ChildEvent findOneByPublishAt(string $publish_at) Return the first ChildEvent filtered by the publish_at column
 * @method     ChildEvent findOneByBeginAt(string $begin_at) Return the first ChildEvent filtered by the begin_at column
 * @method     ChildEvent findOneByEndAt(string $end_at) Return the first ChildEvent filtered by the end_at column
 * @method     ChildEvent findOneById(int $id) Return the first ChildEvent filtered by the id column
 * @method     ChildEvent findOneByCreatedAt(string $created_at) Return the first ChildEvent filtered by the created_at column
 * @method     ChildEvent findOneByUpdatedAt(string $updated_at) Return the first ChildEvent filtered by the updated_at column *

 * @method     ChildEvent requirePk($key, ConnectionInterface $con = null) Return the ChildEvent by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOne(ConnectionInterface $con = null) Return the first ChildEvent matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildEvent requireOneByFarmId(int $farm_id) Return the first ChildEvent filtered by the farm_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByName(string $name) Return the first ChildEvent filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByDescription(string $description) Return the first ChildEvent filtered by the description column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByPublishAt(string $publish_at) Return the first ChildEvent filtered by the publish_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByBeginAt(string $begin_at) Return the first ChildEvent filtered by the begin_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByEndAt(string $end_at) Return the first ChildEvent filtered by the end_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneById(int $id) Return the first ChildEvent filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByCreatedAt(string $created_at) Return the first ChildEvent filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildEvent requireOneByUpdatedAt(string $updated_at) Return the first ChildEvent filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildEvent[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildEvent objects based on current ModelCriteria
 * @method     ChildEvent[]|ObjectCollection findByFarmId(int $farm_id) Return ChildEvent objects filtered by the farm_id column
 * @method     ChildEvent[]|ObjectCollection findByName(string $name) Return ChildEvent objects filtered by the name column
 * @method     ChildEvent[]|ObjectCollection findByDescription(string $description) Return ChildEvent objects filtered by the description column
 * @method     ChildEvent[]|ObjectCollection findByPublishAt(string $publish_at) Return ChildEvent objects filtered by the publish_at column
 * @method     ChildEvent[]|ObjectCollection findByBeginAt(string $begin_at) Return ChildEvent objects filtered by the begin_at column
 * @method     ChildEvent[]|ObjectCollection findByEndAt(string $end_at) Return ChildEvent objects filtered by the end_at column
 * @method     ChildEvent[]|ObjectCollection findById(int $id) Return ChildEvent objects filtered by the id column
 * @method     ChildEvent[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildEvent objects filtered by the created_at column
 * @method     ChildEvent[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildEvent objects filtered by the updated_at column
 * @method     ChildEvent[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class EventQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\EventQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Event', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildEventQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildEventQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildEventQuery) {
            return $criteria;
        }
        $query = new ChildEventQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildEvent|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(EventTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = EventTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildEvent A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT farm_id, name, description, publish_at, begin_at, end_at, id, created_at, updated_at FROM event WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildEvent $obj */
            $obj = new ChildEvent();
            $obj->hydrate($row);
            EventTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildEvent|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(EventTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(EventTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the farm_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFarmId(1234); // WHERE farm_id = 1234
     * $query->filterByFarmId(array(12, 34)); // WHERE farm_id IN (12, 34)
     * $query->filterByFarmId(array('min' => 12)); // WHERE farm_id > 12
     * </code>
     *
     * @see       filterByFarm()
     *
     * @param     mixed $farmId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByFarmId($farmId = null, $comparison = null)
    {
        if (is_array($farmId)) {
            $useMinMax = false;
            if (isset($farmId['min'])) {
                $this->addUsingAlias(EventTableMap::COL_FARM_ID, $farmId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($farmId['max'])) {
                $this->addUsingAlias(EventTableMap::COL_FARM_ID, $farmId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_FARM_ID, $farmId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%', Criteria::LIKE); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%', Criteria::LIKE); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the publish_at column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishAt('2011-03-14'); // WHERE publish_at = '2011-03-14'
     * $query->filterByPublishAt('now'); // WHERE publish_at = '2011-03-14'
     * $query->filterByPublishAt(array('max' => 'yesterday')); // WHERE publish_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $publishAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByPublishAt($publishAt = null, $comparison = null)
    {
        if (is_array($publishAt)) {
            $useMinMax = false;
            if (isset($publishAt['min'])) {
                $this->addUsingAlias(EventTableMap::COL_PUBLISH_AT, $publishAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishAt['max'])) {
                $this->addUsingAlias(EventTableMap::COL_PUBLISH_AT, $publishAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_PUBLISH_AT, $publishAt, $comparison);
    }

    /**
     * Filter the query on the begin_at column
     *
     * Example usage:
     * <code>
     * $query->filterByBeginAt('2011-03-14'); // WHERE begin_at = '2011-03-14'
     * $query->filterByBeginAt('now'); // WHERE begin_at = '2011-03-14'
     * $query->filterByBeginAt(array('max' => 'yesterday')); // WHERE begin_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $beginAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByBeginAt($beginAt = null, $comparison = null)
    {
        if (is_array($beginAt)) {
            $useMinMax = false;
            if (isset($beginAt['min'])) {
                $this->addUsingAlias(EventTableMap::COL_BEGIN_AT, $beginAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($beginAt['max'])) {
                $this->addUsingAlias(EventTableMap::COL_BEGIN_AT, $beginAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_BEGIN_AT, $beginAt, $comparison);
    }

    /**
     * Filter the query on the end_at column
     *
     * Example usage:
     * <code>
     * $query->filterByEndAt('2011-03-14'); // WHERE end_at = '2011-03-14'
     * $query->filterByEndAt('now'); // WHERE end_at = '2011-03-14'
     * $query->filterByEndAt(array('max' => 'yesterday')); // WHERE end_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $endAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByEndAt($endAt = null, $comparison = null)
    {
        if (is_array($endAt)) {
            $useMinMax = false;
            if (isset($endAt['min'])) {
                $this->addUsingAlias(EventTableMap::COL_END_AT, $endAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($endAt['max'])) {
                $this->addUsingAlias(EventTableMap::COL_END_AT, $endAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_END_AT, $endAt, $comparison);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(EventTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(EventTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(EventTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(EventTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(EventTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(EventTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Farm object
     *
     * @param \Farm|ObjectCollection $farm The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildEventQuery The current query, for fluid interface
     */
    public function filterByFarm($farm, $comparison = null)
    {
        if ($farm instanceof \Farm) {
            return $this
                ->addUsingAlias(EventTableMap::COL_FARM_ID, $farm->getId(), $comparison);
        } elseif ($farm instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EventTableMap::COL_FARM_ID, $farm->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFarm() only accepts arguments of type \Farm or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Farm relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function joinFarm($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Farm');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Farm');
        }

        return $this;
    }

    /**
     * Use the Farm relation Farm object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FarmQuery A secondary query class using the current class as primary query
     */
    public function useFarmQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFarm($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Farm', '\FarmQuery');
    }

    /**
     * Filter the query by a related \EventProduct object
     *
     * @param \EventProduct|ObjectCollection $eventProduct the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildEventQuery The current query, for fluid interface
     */
    public function filterByEventProduct($eventProduct, $comparison = null)
    {
        if ($eventProduct instanceof \EventProduct) {
            return $this
                ->addUsingAlias(EventTableMap::COL_ID, $eventProduct->getEventId(), $comparison);
        } elseif ($eventProduct instanceof ObjectCollection) {
            return $this
                ->useEventProductQuery()
                ->filterByPrimaryKeys($eventProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByEventProduct() only accepts arguments of type \EventProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the EventProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function joinEventProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('EventProduct');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'EventProduct');
        }

        return $this;
    }

    /**
     * Use the EventProduct relation EventProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \EventProductQuery A secondary query class using the current class as primary query
     */
    public function useEventProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinEventProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'EventProduct', '\EventProductQuery');
    }

    /**
     * Filter the query by a related \Pin object
     *
     * @param \Pin|ObjectCollection $pin the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildEventQuery The current query, for fluid interface
     */
    public function filterByPin($pin, $comparison = null)
    {
        if ($pin instanceof \Pin) {
            return $this
                ->addUsingAlias(EventTableMap::COL_ID, $pin->getEventId(), $comparison);
        } elseif ($pin instanceof ObjectCollection) {
            return $this
                ->usePinQuery()
                ->filterByPrimaryKeys($pin->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPin() only accepts arguments of type \Pin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Pin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function joinPin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Pin');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Pin');
        }

        return $this;
    }

    /**
     * Use the Pin relation Pin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PinQuery A secondary query class using the current class as primary query
     */
    public function usePinQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Pin', '\PinQuery');
    }

    /**
     * Filter the query by a related Product object
     * using the event_product table as cross reference
     *
     * @param Product $product the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildEventQuery The current query, for fluid interface
     */
    public function filterByProduct($product, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useEventProductQuery()
            ->filterByProduct($product, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related User object
     * using the pin table as cross reference
     *
     * @param User $user the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildEventQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePinQuery()
            ->filterByUser($user, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildEvent $event Object to remove from the list of results
     *
     * @return $this|ChildEventQuery The current query, for fluid interface
     */
    public function prune($event = null)
    {
        if ($event) {
            $this->addUsingAlias(EventTableMap::COL_ID, $event->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the event table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(EventTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            EventTableMap::clearInstancePool();
            EventTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(EventTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(EventTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            EventTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            EventTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildEventQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(EventTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildEventQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(EventTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildEventQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(EventTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildEventQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(EventTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildEventQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(EventTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildEventQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(EventTableMap::COL_CREATED_AT);
    }

} // EventQuery
