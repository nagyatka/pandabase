<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 03. 31.
 * Time: 16:34
 */

namespace PandaBase\QueryBuilder;


use Exception;
use PandaBase\Connection\ConnectionManager;

/**
 * Class QueryBuilder
 *
 * QueryBuilder helps to write complex MYSQL queries. It does not contain any black magic. It simply assembles different
 * parts of a query.
 *
 * Now it supports the following parts:
 *  - WHERE parameters
 *  - ORDER BY parameters
 *  - GROUP BY parameters
 *
 * Further improvements
 *  - LIMIT
 *  - Unique order parameter bag
 *  - ...
 *
 * Recommendation: Use a ResponseGenerator instead of LIMIT.
 *
 * @package PandaBase\QueryBuilder
 */
class QueryBuilder {

    /**
     * @var string
     */
    private $sql;

    /**
     * @var array
     */
    private $searchParameters;

    /**
     * @var array
     */
    private $whereParams;

    /**
     * @var array
     */
    private $orderParams;

    /**
     * @var string
     */
    private $groupBy;

    /**
     * @param string $sql
     * @param array $parameterList
     */
    function __construct($sql,$parameterList)
    {
        $this->sql  = $sql;
        $this->searchParameters = $parameterList;
        $this->whereParams = [];
        $this->orderParams = [];
        $this->groupBy = "";
    }

    /**
     * Add order by parameter with direction to parameter set. Ordering of the parameter follow the order of the addition.
     *
     * @param $parameterName
     * @param $direction
     * @return $this
     * @throws Exception
     */
    public function addOrderParam($parameterName,$direction) {
        //Le kell csekkolni, hogy ide normális paraméterek mennek, mert ezek itt veszélyesek!
        if(strtolower($direction) != "asc" && strtolower($direction) != "desc") {
            throw new Exception("Unknown ordering direction");
        }
        if(!in_array($parameterName,array_keys($this->searchParameters))) {
            throw new Exception($parameterName." is unknown ordering property");
        }
        $this->orderParams[] = $parameterName." ".$direction;

        return $this;
    }

    /**
     * Add where parameter with boolean operator to parameter set. Ordering of the parameter follow the order of the addition.
     * @param $booleanOperator
     * @param $expression
     * @return $this
     */
    public function addWhereParam($booleanOperator,$expression) {
        $this->whereParams[] = [
            $booleanOperator,
            $expression
        ];
        return $this;
    }

    /** Set the group by expression.
     * @param $groupByExpression
     * @return $this
     */
    public function groupBy($groupByExpression) {
        $this->groupBy = $groupByExpression;
        return $this;
    }

    /**
     * Returns with a mixed result set.
     *
     * @return \PandaBase\Record\MixedRecordContainer
     */
    public function getMixedResult() {
        $this->buildSqlString();
        return ConnectionManager::getInstance()->getMixedRecords($this->sql,$this->searchParameters);
    }

    /**
     * @param $className
     * @return \PandaBase\Record\InstanceRecordContainer
     */
    public function getInstances($className) {
        $this->buildSqlString();
        return ConnectionManager::getInstance()->getInstanceRecords($className,$this->sql,$this->searchParameters);
    }

    /**
     * Concatenates sql parts.
     */
    private function buildSqlString() {
        //Gather where params
        if (count($this->whereParams) > 0) {
            $this->sql .= " WHERE ";
            $count = count($this->whereParams);
            for ($i=0; $i < $count;++$i) {
                $this->sql .= $this->whereParams[$i][1];
                if ($i != ($count-1)) {
                    $this->sql .= " ".$this->whereParams[$i+1][0]." ";
                }
            }
        }

        //Append group by
        if($this->groupBy != "") $this->sql .= " GROUP BY ".$this->groupBy." ";

        //Gather order by params
        if (count($this->orderParams) > 0) {
            $this->sql .= " ORDER BY ";
            $count = count($this->orderParams);
            for ($i=0; $i < $count;++$i) {
                $this->sql .= $this->orderParams[$i];
                if ($i != ($count-1)) {
                    $this->sql .= " , ";
                }
            }
        }
    }


} 