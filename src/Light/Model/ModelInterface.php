<?php

declare( strict_types = 1 );

namespace Light\Model;

/**
 * Interface ModelInterface
 * @package Light\Model
 */
interface ModelInterface
{
    /**
     * @param array|string|null $cond
     * @param array|string|null $sort
     *
     * @param int|null $count
     * @param int|null $offset
     *
     * @return array|\Light\Model
     */
    public static function fetchAll($cond = null, $sort = null, int $count = null, int $offset = null);

    /**
     * @param array|string|null $cond
     * @param array|string|null $sort
     *
     * @return null|\Light\Model
     */
    public static function fetchOne($cond = null, $sort = null);

    /**
     * @param array|string|null $cond
     * @param array|string|null $sort
     *
     * @return \Light\Model
     */
    public static function fetchObject($cond = null, $sort = null);

    /**
     * @param array|string|null $cond
     * @return int
     */
    public static function count($cond = null);

    /**
     * @param array $data
     * @return int
     */
    public static function batchInsert(array $data = []);

    /**
     * @param array|string|null $cond
     * @param array $data
     *
     * @return int
     */
    public static function update($cond = null, array $data = []);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     * @return void
     */
    public function populate(array $data);

    /**
     * @return int
     */
    public function save();

    /**
     * @return array
     */
    public function toArray() : array;
}