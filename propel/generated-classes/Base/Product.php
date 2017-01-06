<?php

namespace Base;

use \Event as ChildEvent;
use \EventQuery as ChildEventQuery;
use \Farm as ChildFarm;
use \FarmProduct as ChildFarmProduct;
use \FarmProductQuery as ChildFarmProductQuery;
use \FarmQuery as ChildFarmQuery;
use \Order as ChildOrder;
use \OrderQuery as ChildOrderQuery;
use \Product as ChildProduct;
use \ProductQuery as ChildProductQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\EventTableMap;
use Map\FarmProductTableMap;
use Map\OrderTableMap;
use Map\ProductTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use Propel\Runtime\Validator\Constraints\Unique;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base class that represents a row from the 'product' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Product implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\ProductTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the parent_id field.
     *
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the name field.
     *
     * @var        string
     */
    protected $name;

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the created_at field.
     *
     * @var        DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     *
     * @var        DateTime
     */
    protected $updated_at;

    /**
     * @var        ChildProduct
     */
    protected $aProductRelatedByParentId;

    /**
     * @var        ObjectCollection|ChildProduct[] Collection to store aggregation of ChildProduct objects.
     */
    protected $collProductsRelatedById;
    protected $collProductsRelatedByIdPartial;

    /**
     * @var        ObjectCollection|ChildEvent[] Collection to store aggregation of ChildEvent objects.
     */
    protected $collEvents;
    protected $collEventsPartial;

    /**
     * @var        ObjectCollection|ChildOrder[] Collection to store aggregation of ChildOrder objects.
     */
    protected $collOrders;
    protected $collOrdersPartial;

    /**
     * @var        ObjectCollection|ChildFarmProduct[] Collection to store aggregation of ChildFarmProduct objects.
     */
    protected $collFarmProducts;
    protected $collFarmProductsPartial;

    /**
     * @var        ObjectCollection|ChildFarm[] Cross Collection to store aggregation of ChildFarm objects.
     */
    protected $collFarms;

    /**
     * @var bool
     */
    protected $collFarmsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // validate behavior

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * ConstraintViolationList object
     *
     * @see     http://api.symfony.com/2.0/Symfony/Component/Validator/ConstraintViolationList.html
     * @var     ConstraintViolationList
     */
    protected $validationFailures;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildFarm[]
     */
    protected $farmsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildProduct[]
     */
    protected $productsRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildEvent[]
     */
    protected $eventsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildOrder[]
     */
    protected $ordersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildFarmProduct[]
     */
    protected $farmProductsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Product object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Product</code> instance.  If
     * <code>obj</code> is an instance of <code>Product</code>, delegates to
     * <code>equals(Product)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Product The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTimeInterface ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTimeInterface ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [parent_id] column.
     *
     * @param int $v new value
     * @return $this|\Product The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_PARENT_ID] = true;
        }

        if ($this->aProductRelatedByParentId !== null && $this->aProductRelatedByParentId->getId() !== $v) {
            $this->aProductRelatedByParentId = null;
        }

        return $this;
    } // setParentId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return $this|\Product The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[ProductTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Product The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ProductTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\Product The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->created_at->format("Y-m-d H:i:s.u")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ProductTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\Product The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->updated_at->format("Y-m-d H:i:s.u")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ProductTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ProductTableMap::translateFieldName('ParentId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ProductTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ProductTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ProductTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ProductTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = ProductTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Product'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aProductRelatedByParentId !== null && $this->parent_id !== $this->aProductRelatedByParentId->getId()) {
            $this->aProductRelatedByParentId = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildProductQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aProductRelatedByParentId = null;
            $this->collProductsRelatedById = null;

            $this->collEvents = null;

            $this->collOrders = null;

            $this->collFarmProducts = null;

            $this->collFarms = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Product::setDeleted()
     * @see Product::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildProductQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(ProductTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(\Propel\Runtime\Util\PropelDateTime::createHighPrecision());
                }
                if (!$this->isColumnModified(ProductTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(\Propel\Runtime\Util\PropelDateTime::createHighPrecision());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ProductTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(\Propel\Runtime\Util\PropelDateTime::createHighPrecision());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ProductTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aProductRelatedByParentId !== null) {
                if ($this->aProductRelatedByParentId->isModified() || $this->aProductRelatedByParentId->isNew()) {
                    $affectedRows += $this->aProductRelatedByParentId->save($con);
                }
                $this->setProductRelatedByParentId($this->aProductRelatedByParentId);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->farmsScheduledForDeletion !== null) {
                if (!$this->farmsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->farmsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[1] = $this->getId();
                        $entryPk[0] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \FarmProductQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->farmsScheduledForDeletion = null;
                }

            }

            if ($this->collFarms) {
                foreach ($this->collFarms as $farm) {
                    if (!$farm->isDeleted() && ($farm->isNew() || $farm->isModified())) {
                        $farm->save($con);
                    }
                }
            }


            if ($this->productsRelatedByIdScheduledForDeletion !== null) {
                if (!$this->productsRelatedByIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->productsRelatedByIdScheduledForDeletion as $productRelatedById) {
                        // need to save related object because we set the relation to null
                        $productRelatedById->save($con);
                    }
                    $this->productsRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collProductsRelatedById !== null) {
                foreach ($this->collProductsRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->eventsScheduledForDeletion !== null) {
                if (!$this->eventsScheduledForDeletion->isEmpty()) {
                    foreach ($this->eventsScheduledForDeletion as $event) {
                        // need to save related object because we set the relation to null
                        $event->save($con);
                    }
                    $this->eventsScheduledForDeletion = null;
                }
            }

            if ($this->collEvents !== null) {
                foreach ($this->collEvents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ordersScheduledForDeletion !== null) {
                if (!$this->ordersScheduledForDeletion->isEmpty()) {
                    \OrderQuery::create()
                        ->filterByPrimaryKeys($this->ordersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ordersScheduledForDeletion = null;
                }
            }

            if ($this->collOrders !== null) {
                foreach ($this->collOrders as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->farmProductsScheduledForDeletion !== null) {
                if (!$this->farmProductsScheduledForDeletion->isEmpty()) {
                    \FarmProductQuery::create()
                        ->filterByPrimaryKeys($this->farmProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->farmProductsScheduledForDeletion = null;
                }
            }

            if ($this->collFarmProducts !== null) {
                foreach ($this->collFarmProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[ProductTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProductTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProductTableMap::COL_PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'parent_id';
        }
        if ($this->isColumnModified(ProductTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(ProductTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ProductTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(ProductTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO product (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'parent_id':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'created_at':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'updated_at':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProductTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getParentId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getId();
                break;
            case 3:
                return $this->getCreatedAt();
                break;
            case 4:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Product'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Product'][$this->hashCode()] = true;
        $keys = ProductTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getParentId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getId(),
            $keys[3] => $this->getCreatedAt(),
            $keys[4] => $this->getUpdatedAt(),
        );
        if ($result[$keys[3]] instanceof \DateTime) {
            $result[$keys[3]] = $result[$keys[3]]->format('c');
        }

        if ($result[$keys[4]] instanceof \DateTime) {
            $result[$keys[4]] = $result[$keys[4]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aProductRelatedByParentId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'product';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'product';
                        break;
                    default:
                        $key = 'Product';
                }

                $result[$key] = $this->aProductRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collProductsRelatedById) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'products';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'products';
                        break;
                    default:
                        $key = 'Products';
                }

                $result[$key] = $this->collProductsRelatedById->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collEvents) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'events';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'events';
                        break;
                    default:
                        $key = 'Events';
                }

                $result[$key] = $this->collEvents->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrders) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'orders';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'orders';
                        break;
                    default:
                        $key = 'Orders';
                }

                $result[$key] = $this->collOrders->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFarmProducts) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'farmProducts';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'farm_products';
                        break;
                    default:
                        $key = 'FarmProducts';
                }

                $result[$key] = $this->collFarmProducts->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Product
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProductTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Product
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setParentId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setId($value);
                break;
            case 3:
                $this->setCreatedAt($value);
                break;
            case 4:
                $this->setUpdatedAt($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = ProductTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setParentId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setCreatedAt($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setUpdatedAt($arr[$keys[4]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Product The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProductTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ProductTableMap::COL_PARENT_ID)) {
            $criteria->add(ProductTableMap::COL_PARENT_ID, $this->parent_id);
        }
        if ($this->isColumnModified(ProductTableMap::COL_NAME)) {
            $criteria->add(ProductTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(ProductTableMap::COL_ID)) {
            $criteria->add(ProductTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ProductTableMap::COL_CREATED_AT)) {
            $criteria->add(ProductTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(ProductTableMap::COL_UPDATED_AT)) {
            $criteria->add(ProductTableMap::COL_UPDATED_AT, $this->updated_at);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildProductQuery::create();
        $criteria->add(ProductTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Product (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentId($this->getParentId());
        $copyObj->setName($this->getName());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getProductsRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getEvents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addEvent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrders() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrder($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFarmProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFarmProduct($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Product Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildProduct object.
     *
     * @param  ChildProduct $v
     * @return $this|\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProductRelatedByParentId(ChildProduct $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aProductRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildProduct object, it will not be re-added.
        if ($v !== null) {
            $v->addProductRelatedById($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildProduct object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildProduct The associated ChildProduct object.
     * @throws PropelException
     */
    public function getProductRelatedByParentId(ConnectionInterface $con = null)
    {
        if ($this->aProductRelatedByParentId === null && ($this->parent_id !== null)) {
            $this->aProductRelatedByParentId = ChildProductQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProductRelatedByParentId->addProductsRelatedById($this);
             */
        }

        return $this->aProductRelatedByParentId;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('ProductRelatedById' == $relationName) {
            return $this->initProductsRelatedById();
        }
        if ('Event' == $relationName) {
            return $this->initEvents();
        }
        if ('Order' == $relationName) {
            return $this->initOrders();
        }
        if ('FarmProduct' == $relationName) {
            return $this->initFarmProducts();
        }
    }

    /**
     * Clears out the collProductsRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductsRelatedById()
     */
    public function clearProductsRelatedById()
    {
        $this->collProductsRelatedById = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductsRelatedById collection loaded partially.
     */
    public function resetPartialProductsRelatedById($v = true)
    {
        $this->collProductsRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collProductsRelatedById collection.
     *
     * By default this just sets the collProductsRelatedById collection to an empty array (like clearcollProductsRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsRelatedById($overrideExisting = true)
    {
        if (null !== $this->collProductsRelatedById && !$overrideExisting) {
            return;
        }

        $collectionClassName = ProductTableMap::getTableMap()->getCollectionClassName();

        $this->collProductsRelatedById = new $collectionClassName;
        $this->collProductsRelatedById->setModel('\Product');
    }

    /**
     * Gets an array of ChildProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildProduct[] List of ChildProduct objects
     * @throws PropelException
     */
    public function getProductsRelatedById(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductsRelatedByIdPartial && !$this->isNew();
        if (null === $this->collProductsRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsRelatedById) {
                // return empty collection
                $this->initProductsRelatedById();
            } else {
                $collProductsRelatedById = ChildProductQuery::create(null, $criteria)
                    ->filterByProductRelatedByParentId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductsRelatedByIdPartial && count($collProductsRelatedById)) {
                        $this->initProductsRelatedById(false);

                        foreach ($collProductsRelatedById as $obj) {
                            if (false == $this->collProductsRelatedById->contains($obj)) {
                                $this->collProductsRelatedById->append($obj);
                            }
                        }

                        $this->collProductsRelatedByIdPartial = true;
                    }

                    return $collProductsRelatedById;
                }

                if ($partial && $this->collProductsRelatedById) {
                    foreach ($this->collProductsRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collProductsRelatedById[] = $obj;
                        }
                    }
                }

                $this->collProductsRelatedById = $collProductsRelatedById;
                $this->collProductsRelatedByIdPartial = false;
            }
        }

        return $this->collProductsRelatedById;
    }

    /**
     * Sets a collection of ChildProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productsRelatedById A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function setProductsRelatedById(Collection $productsRelatedById, ConnectionInterface $con = null)
    {
        /** @var ChildProduct[] $productsRelatedByIdToDelete */
        $productsRelatedByIdToDelete = $this->getProductsRelatedById(new Criteria(), $con)->diff($productsRelatedById);


        $this->productsRelatedByIdScheduledForDeletion = $productsRelatedByIdToDelete;

        foreach ($productsRelatedByIdToDelete as $productRelatedByIdRemoved) {
            $productRelatedByIdRemoved->setProductRelatedByParentId(null);
        }

        $this->collProductsRelatedById = null;
        foreach ($productsRelatedById as $productRelatedById) {
            $this->addProductRelatedById($productRelatedById);
        }

        $this->collProductsRelatedById = $productsRelatedById;
        $this->collProductsRelatedByIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Product objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Product objects.
     * @throws PropelException
     */
    public function countProductsRelatedById(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductsRelatedByIdPartial && !$this->isNew();
        if (null === $this->collProductsRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsRelatedById());
            }

            $query = ChildProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByParentId($this)
                ->count($con);
        }

        return count($this->collProductsRelatedById);
    }

    /**
     * Method called to associate a ChildProduct object to this object
     * through the ChildProduct foreign key attribute.
     *
     * @param  ChildProduct $l ChildProduct
     * @return $this|\Product The current object (for fluent API support)
     */
    public function addProductRelatedById(ChildProduct $l)
    {
        if ($this->collProductsRelatedById === null) {
            $this->initProductsRelatedById();
            $this->collProductsRelatedByIdPartial = true;
        }

        if (!$this->collProductsRelatedById->contains($l)) {
            $this->doAddProductRelatedById($l);

            if ($this->productsRelatedByIdScheduledForDeletion and $this->productsRelatedByIdScheduledForDeletion->contains($l)) {
                $this->productsRelatedByIdScheduledForDeletion->remove($this->productsRelatedByIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildProduct $productRelatedById The ChildProduct object to add.
     */
    protected function doAddProductRelatedById(ChildProduct $productRelatedById)
    {
        $this->collProductsRelatedById[]= $productRelatedById;
        $productRelatedById->setProductRelatedByParentId($this);
    }

    /**
     * @param  ChildProduct $productRelatedById The ChildProduct object to remove.
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function removeProductRelatedById(ChildProduct $productRelatedById)
    {
        if ($this->getProductsRelatedById()->contains($productRelatedById)) {
            $pos = $this->collProductsRelatedById->search($productRelatedById);
            $this->collProductsRelatedById->remove($pos);
            if (null === $this->productsRelatedByIdScheduledForDeletion) {
                $this->productsRelatedByIdScheduledForDeletion = clone $this->collProductsRelatedById;
                $this->productsRelatedByIdScheduledForDeletion->clear();
            }
            $this->productsRelatedByIdScheduledForDeletion[]= $productRelatedById;
            $productRelatedById->setProductRelatedByParentId(null);
        }

        return $this;
    }

    /**
     * Clears out the collEvents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addEvents()
     */
    public function clearEvents()
    {
        $this->collEvents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collEvents collection loaded partially.
     */
    public function resetPartialEvents($v = true)
    {
        $this->collEventsPartial = $v;
    }

    /**
     * Initializes the collEvents collection.
     *
     * By default this just sets the collEvents collection to an empty array (like clearcollEvents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initEvents($overrideExisting = true)
    {
        if (null !== $this->collEvents && !$overrideExisting) {
            return;
        }

        $collectionClassName = EventTableMap::getTableMap()->getCollectionClassName();

        $this->collEvents = new $collectionClassName;
        $this->collEvents->setModel('\Event');
    }

    /**
     * Gets an array of ChildEvent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildEvent[] List of ChildEvent objects
     * @throws PropelException
     */
    public function getEvents(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collEventsPartial && !$this->isNew();
        if (null === $this->collEvents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collEvents) {
                // return empty collection
                $this->initEvents();
            } else {
                $collEvents = ChildEventQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collEventsPartial && count($collEvents)) {
                        $this->initEvents(false);

                        foreach ($collEvents as $obj) {
                            if (false == $this->collEvents->contains($obj)) {
                                $this->collEvents->append($obj);
                            }
                        }

                        $this->collEventsPartial = true;
                    }

                    return $collEvents;
                }

                if ($partial && $this->collEvents) {
                    foreach ($this->collEvents as $obj) {
                        if ($obj->isNew()) {
                            $collEvents[] = $obj;
                        }
                    }
                }

                $this->collEvents = $collEvents;
                $this->collEventsPartial = false;
            }
        }

        return $this->collEvents;
    }

    /**
     * Sets a collection of ChildEvent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $events A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function setEvents(Collection $events, ConnectionInterface $con = null)
    {
        /** @var ChildEvent[] $eventsToDelete */
        $eventsToDelete = $this->getEvents(new Criteria(), $con)->diff($events);


        $this->eventsScheduledForDeletion = $eventsToDelete;

        foreach ($eventsToDelete as $eventRemoved) {
            $eventRemoved->setProduct(null);
        }

        $this->collEvents = null;
        foreach ($events as $event) {
            $this->addEvent($event);
        }

        $this->collEvents = $events;
        $this->collEventsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Event objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Event objects.
     * @throws PropelException
     */
    public function countEvents(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collEventsPartial && !$this->isNew();
        if (null === $this->collEvents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collEvents) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getEvents());
            }

            $query = ChildEventQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collEvents);
    }

    /**
     * Method called to associate a ChildEvent object to this object
     * through the ChildEvent foreign key attribute.
     *
     * @param  ChildEvent $l ChildEvent
     * @return $this|\Product The current object (for fluent API support)
     */
    public function addEvent(ChildEvent $l)
    {
        if ($this->collEvents === null) {
            $this->initEvents();
            $this->collEventsPartial = true;
        }

        if (!$this->collEvents->contains($l)) {
            $this->doAddEvent($l);

            if ($this->eventsScheduledForDeletion and $this->eventsScheduledForDeletion->contains($l)) {
                $this->eventsScheduledForDeletion->remove($this->eventsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildEvent $event The ChildEvent object to add.
     */
    protected function doAddEvent(ChildEvent $event)
    {
        $this->collEvents[]= $event;
        $event->setProduct($this);
    }

    /**
     * @param  ChildEvent $event The ChildEvent object to remove.
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function removeEvent(ChildEvent $event)
    {
        if ($this->getEvents()->contains($event)) {
            $pos = $this->collEvents->search($event);
            $this->collEvents->remove($pos);
            if (null === $this->eventsScheduledForDeletion) {
                $this->eventsScheduledForDeletion = clone $this->collEvents;
                $this->eventsScheduledForDeletion->clear();
            }
            $this->eventsScheduledForDeletion[]= $event;
            $event->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related Events from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildEvent[] List of ChildEvent objects
     */
    public function getEventsJoinFarm(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildEventQuery::create(null, $criteria);
        $query->joinWith('Farm', $joinBehavior);

        return $this->getEvents($query, $con);
    }

    /**
     * Clears out the collOrders collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrders()
     */
    public function clearOrders()
    {
        $this->collOrders = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrders collection loaded partially.
     */
    public function resetPartialOrders($v = true)
    {
        $this->collOrdersPartial = $v;
    }

    /**
     * Initializes the collOrders collection.
     *
     * By default this just sets the collOrders collection to an empty array (like clearcollOrders());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrders($overrideExisting = true)
    {
        if (null !== $this->collOrders && !$overrideExisting) {
            return;
        }

        $collectionClassName = OrderTableMap::getTableMap()->getCollectionClassName();

        $this->collOrders = new $collectionClassName;
        $this->collOrders->setModel('\Order');
    }

    /**
     * Gets an array of ChildOrder objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildOrder[] List of ChildOrder objects
     * @throws PropelException
     */
    public function getOrders(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrdersPartial && !$this->isNew();
        if (null === $this->collOrders || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrders) {
                // return empty collection
                $this->initOrders();
            } else {
                $collOrders = ChildOrderQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrdersPartial && count($collOrders)) {
                        $this->initOrders(false);

                        foreach ($collOrders as $obj) {
                            if (false == $this->collOrders->contains($obj)) {
                                $this->collOrders->append($obj);
                            }
                        }

                        $this->collOrdersPartial = true;
                    }

                    return $collOrders;
                }

                if ($partial && $this->collOrders) {
                    foreach ($this->collOrders as $obj) {
                        if ($obj->isNew()) {
                            $collOrders[] = $obj;
                        }
                    }
                }

                $this->collOrders = $collOrders;
                $this->collOrdersPartial = false;
            }
        }

        return $this->collOrders;
    }

    /**
     * Sets a collection of ChildOrder objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orders A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function setOrders(Collection $orders, ConnectionInterface $con = null)
    {
        /** @var ChildOrder[] $ordersToDelete */
        $ordersToDelete = $this->getOrders(new Criteria(), $con)->diff($orders);


        $this->ordersScheduledForDeletion = $ordersToDelete;

        foreach ($ordersToDelete as $orderRemoved) {
            $orderRemoved->setProduct(null);
        }

        $this->collOrders = null;
        foreach ($orders as $order) {
            $this->addOrder($order);
        }

        $this->collOrders = $orders;
        $this->collOrdersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Order objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Order objects.
     * @throws PropelException
     */
    public function countOrders(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrdersPartial && !$this->isNew();
        if (null === $this->collOrders || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrders) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrders());
            }

            $query = ChildOrderQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collOrders);
    }

    /**
     * Method called to associate a ChildOrder object to this object
     * through the ChildOrder foreign key attribute.
     *
     * @param  ChildOrder $l ChildOrder
     * @return $this|\Product The current object (for fluent API support)
     */
    public function addOrder(ChildOrder $l)
    {
        if ($this->collOrders === null) {
            $this->initOrders();
            $this->collOrdersPartial = true;
        }

        if (!$this->collOrders->contains($l)) {
            $this->doAddOrder($l);

            if ($this->ordersScheduledForDeletion and $this->ordersScheduledForDeletion->contains($l)) {
                $this->ordersScheduledForDeletion->remove($this->ordersScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildOrder $order The ChildOrder object to add.
     */
    protected function doAddOrder(ChildOrder $order)
    {
        $this->collOrders[]= $order;
        $order->setProduct($this);
    }

    /**
     * @param  ChildOrder $order The ChildOrder object to remove.
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function removeOrder(ChildOrder $order)
    {
        if ($this->getOrders()->contains($order)) {
            $pos = $this->collOrders->search($order);
            $this->collOrders->remove($pos);
            if (null === $this->ordersScheduledForDeletion) {
                $this->ordersScheduledForDeletion = clone $this->collOrders;
                $this->ordersScheduledForDeletion->clear();
            }
            $this->ordersScheduledForDeletion[]= clone $order;
            $order->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related Orders from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildOrder[] List of ChildOrder objects
     */
    public function getOrdersJoinUser(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildOrderQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getOrders($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related Orders from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildOrder[] List of ChildOrder objects
     */
    public function getOrdersJoinFarm(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildOrderQuery::create(null, $criteria);
        $query->joinWith('Farm', $joinBehavior);

        return $this->getOrders($query, $con);
    }

    /**
     * Clears out the collFarmProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addFarmProducts()
     */
    public function clearFarmProducts()
    {
        $this->collFarmProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collFarmProducts collection loaded partially.
     */
    public function resetPartialFarmProducts($v = true)
    {
        $this->collFarmProductsPartial = $v;
    }

    /**
     * Initializes the collFarmProducts collection.
     *
     * By default this just sets the collFarmProducts collection to an empty array (like clearcollFarmProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFarmProducts($overrideExisting = true)
    {
        if (null !== $this->collFarmProducts && !$overrideExisting) {
            return;
        }

        $collectionClassName = FarmProductTableMap::getTableMap()->getCollectionClassName();

        $this->collFarmProducts = new $collectionClassName;
        $this->collFarmProducts->setModel('\FarmProduct');
    }

    /**
     * Gets an array of ChildFarmProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildFarmProduct[] List of ChildFarmProduct objects
     * @throws PropelException
     */
    public function getFarmProducts(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collFarmProductsPartial && !$this->isNew();
        if (null === $this->collFarmProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFarmProducts) {
                // return empty collection
                $this->initFarmProducts();
            } else {
                $collFarmProducts = ChildFarmProductQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collFarmProductsPartial && count($collFarmProducts)) {
                        $this->initFarmProducts(false);

                        foreach ($collFarmProducts as $obj) {
                            if (false == $this->collFarmProducts->contains($obj)) {
                                $this->collFarmProducts->append($obj);
                            }
                        }

                        $this->collFarmProductsPartial = true;
                    }

                    return $collFarmProducts;
                }

                if ($partial && $this->collFarmProducts) {
                    foreach ($this->collFarmProducts as $obj) {
                        if ($obj->isNew()) {
                            $collFarmProducts[] = $obj;
                        }
                    }
                }

                $this->collFarmProducts = $collFarmProducts;
                $this->collFarmProductsPartial = false;
            }
        }

        return $this->collFarmProducts;
    }

    /**
     * Sets a collection of ChildFarmProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $farmProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function setFarmProducts(Collection $farmProducts, ConnectionInterface $con = null)
    {
        /** @var ChildFarmProduct[] $farmProductsToDelete */
        $farmProductsToDelete = $this->getFarmProducts(new Criteria(), $con)->diff($farmProducts);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->farmProductsScheduledForDeletion = clone $farmProductsToDelete;

        foreach ($farmProductsToDelete as $farmProductRemoved) {
            $farmProductRemoved->setProduct(null);
        }

        $this->collFarmProducts = null;
        foreach ($farmProducts as $farmProduct) {
            $this->addFarmProduct($farmProduct);
        }

        $this->collFarmProducts = $farmProducts;
        $this->collFarmProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FarmProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related FarmProduct objects.
     * @throws PropelException
     */
    public function countFarmProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collFarmProductsPartial && !$this->isNew();
        if (null === $this->collFarmProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFarmProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFarmProducts());
            }

            $query = ChildFarmProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collFarmProducts);
    }

    /**
     * Method called to associate a ChildFarmProduct object to this object
     * through the ChildFarmProduct foreign key attribute.
     *
     * @param  ChildFarmProduct $l ChildFarmProduct
     * @return $this|\Product The current object (for fluent API support)
     */
    public function addFarmProduct(ChildFarmProduct $l)
    {
        if ($this->collFarmProducts === null) {
            $this->initFarmProducts();
            $this->collFarmProductsPartial = true;
        }

        if (!$this->collFarmProducts->contains($l)) {
            $this->doAddFarmProduct($l);

            if ($this->farmProductsScheduledForDeletion and $this->farmProductsScheduledForDeletion->contains($l)) {
                $this->farmProductsScheduledForDeletion->remove($this->farmProductsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildFarmProduct $farmProduct The ChildFarmProduct object to add.
     */
    protected function doAddFarmProduct(ChildFarmProduct $farmProduct)
    {
        $this->collFarmProducts[]= $farmProduct;
        $farmProduct->setProduct($this);
    }

    /**
     * @param  ChildFarmProduct $farmProduct The ChildFarmProduct object to remove.
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function removeFarmProduct(ChildFarmProduct $farmProduct)
    {
        if ($this->getFarmProducts()->contains($farmProduct)) {
            $pos = $this->collFarmProducts->search($farmProduct);
            $this->collFarmProducts->remove($pos);
            if (null === $this->farmProductsScheduledForDeletion) {
                $this->farmProductsScheduledForDeletion = clone $this->collFarmProducts;
                $this->farmProductsScheduledForDeletion->clear();
            }
            $this->farmProductsScheduledForDeletion[]= clone $farmProduct;
            $farmProduct->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related FarmProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildFarmProduct[] List of ChildFarmProduct objects
     */
    public function getFarmProductsJoinFarm(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildFarmProductQuery::create(null, $criteria);
        $query->joinWith('Farm', $joinBehavior);

        return $this->getFarmProducts($query, $con);
    }

    /**
     * Clears out the collFarms collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addFarms()
     */
    public function clearFarms()
    {
        $this->collFarms = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collFarms crossRef collection.
     *
     * By default this just sets the collFarms collection to an empty collection (like clearFarms());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initFarms()
    {
        $collectionClassName = FarmProductTableMap::getTableMap()->getCollectionClassName();

        $this->collFarms = new $collectionClassName;
        $this->collFarmsPartial = true;
        $this->collFarms->setModel('\Farm');
    }

    /**
     * Checks if the collFarms collection is loaded.
     *
     * @return bool
     */
    public function isFarmsLoaded()
    {
        return null !== $this->collFarms;
    }

    /**
     * Gets a collection of ChildFarm objects related by a many-to-many relationship
     * to the current object by way of the farm_product cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildFarm[] List of ChildFarm objects
     */
    public function getFarms(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collFarmsPartial && !$this->isNew();
        if (null === $this->collFarms || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collFarms) {
                    $this->initFarms();
                }
            } else {

                $query = ChildFarmQuery::create(null, $criteria)
                    ->filterByProduct($this);
                $collFarms = $query->find($con);
                if (null !== $criteria) {
                    return $collFarms;
                }

                if ($partial && $this->collFarms) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collFarms as $obj) {
                        if (!$collFarms->contains($obj)) {
                            $collFarms[] = $obj;
                        }
                    }
                }

                $this->collFarms = $collFarms;
                $this->collFarmsPartial = false;
            }
        }

        return $this->collFarms;
    }

    /**
     * Sets a collection of Farm objects related by a many-to-many relationship
     * to the current object by way of the farm_product cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $farms A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildProduct The current object (for fluent API support)
     */
    public function setFarms(Collection $farms, ConnectionInterface $con = null)
    {
        $this->clearFarms();
        $currentFarms = $this->getFarms();

        $farmsScheduledForDeletion = $currentFarms->diff($farms);

        foreach ($farmsScheduledForDeletion as $toDelete) {
            $this->removeFarm($toDelete);
        }

        foreach ($farms as $farm) {
            if (!$currentFarms->contains($farm)) {
                $this->doAddFarm($farm);
            }
        }

        $this->collFarmsPartial = false;
        $this->collFarms = $farms;

        return $this;
    }

    /**
     * Gets the number of Farm objects related by a many-to-many relationship
     * to the current object by way of the farm_product cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Farm objects
     */
    public function countFarms(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collFarmsPartial && !$this->isNew();
        if (null === $this->collFarms || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFarms) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getFarms());
                }

                $query = ChildFarmQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByProduct($this)
                    ->count($con);
            }
        } else {
            return count($this->collFarms);
        }
    }

    /**
     * Associate a ChildFarm to this object
     * through the farm_product cross reference table.
     *
     * @param ChildFarm $farm
     * @return ChildProduct The current object (for fluent API support)
     */
    public function addFarm(ChildFarm $farm)
    {
        if ($this->collFarms === null) {
            $this->initFarms();
        }

        if (!$this->getFarms()->contains($farm)) {
            // only add it if the **same** object is not already associated
            $this->collFarms->push($farm);
            $this->doAddFarm($farm);
        }

        return $this;
    }

    /**
     *
     * @param ChildFarm $farm
     */
    protected function doAddFarm(ChildFarm $farm)
    {
        $farmProduct = new ChildFarmProduct();

        $farmProduct->setFarm($farm);

        $farmProduct->setProduct($this);

        $this->addFarmProduct($farmProduct);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$farm->isProductsLoaded()) {
            $farm->initProducts();
            $farm->getProducts()->push($this);
        } elseif (!$farm->getProducts()->contains($this)) {
            $farm->getProducts()->push($this);
        }

    }

    /**
     * Remove farm of this object
     * through the farm_product cross reference table.
     *
     * @param ChildFarm $farm
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeFarm(ChildFarm $farm)
    {
        if ($this->getFarms()->contains($farm)) {
            $farmProduct = new ChildFarmProduct();
            $farmProduct->setFarm($farm);
            if ($farm->isProductsLoaded()) {
                //remove the back reference if available
                $farm->getProducts()->removeObject($this);
            }

            $farmProduct->setProduct($this);
            $this->removeFarmProduct(clone $farmProduct);
            $farmProduct->clear();

            $this->collFarms->remove($this->collFarms->search($farm));

            if (null === $this->farmsScheduledForDeletion) {
                $this->farmsScheduledForDeletion = clone $this->collFarms;
                $this->farmsScheduledForDeletion->clear();
            }

            $this->farmsScheduledForDeletion->push($farm);
        }


        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aProductRelatedByParentId) {
            $this->aProductRelatedByParentId->removeProductRelatedById($this);
        }
        $this->parent_id = null;
        $this->name = null;
        $this->id = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collProductsRelatedById) {
                foreach ($this->collProductsRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collEvents) {
                foreach ($this->collEvents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrders) {
                foreach ($this->collOrders as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFarmProducts) {
                foreach ($this->collFarmProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFarms) {
                foreach ($this->collFarms as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collProductsRelatedById = null;
        $this->collEvents = null;
        $this->collOrders = null;
        $this->collFarmProducts = null;
        $this->collFarms = null;
        $this->aProductRelatedByParentId = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string The value of the 'name' column
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildProduct The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[ProductTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    // validate behavior

    /**
     * Configure validators constraints. The Validator object uses this method
     * to perform object validation.
     *
     * @param ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotNull());
        $metadata->addPropertyConstraint('name', new Unique());
    }

    /**
     * Validates the object and all objects related to this table.
     *
     * @see        getValidationFailures()
     * @param      ValidatorInterface|null $validator A Validator class instance
     * @return     boolean Whether all objects pass validation.
     */
    public function validate(ValidatorInterface $validator = null)
    {
        if (null === $validator) {
            $validator = new RecursiveValidator(
                new ExecutionContextFactory(new IdentityTranslator()),
                new LazyLoadingMetadataFactory(new StaticMethodLoader()),
                new ConstraintValidatorFactory()
            );
        }

        $failureMap = new ConstraintViolationList();

        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aProductRelatedByParentId, 'validate')) {
                if (!$this->aProductRelatedByParentId->validate($validator)) {
                    $failureMap->addAll($this->aProductRelatedByParentId->getValidationFailures());
                }
            }

            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }

            if (null !== $this->collProductsRelatedById) {
                foreach ($this->collProductsRelatedById as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collEvents) {
                foreach ($this->collEvents as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collOrders) {
                foreach ($this->collOrders as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collFarmProducts) {
                foreach ($this->collFarmProducts as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }

            $this->alreadyInValidation = false;
        }

        $this->validationFailures = $failureMap;

        return (Boolean) (!(count($this->validationFailures) > 0));

    }

    /**
     * Gets any ConstraintViolation objects that resulted from last call to validate().
     *
     *
     * @return     object ConstraintViolationList
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
