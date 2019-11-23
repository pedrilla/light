<?php

declare(strict_types = 1);

namespace Light\Map;

interface MapInterface
{
    /**
     * @return array|object|\Light\Model
     */
    public function getData();

    /**
     * @param array|object|\Light\Model $data
     */
    public function setData($data);

    /**
     * @return array|object|\Light\Model
     */
    public function getRow();

    /**
     * @return array
     */
    public function getUserData() : array;

    /**
     * @param array $data
     */
    public function setUserData(array $data = []);

    /**
     * @return string
     */
    public function getContext() : string;

    /**
     * @param string $context
     */
    public function setContext(string $context);

    /**
     * @return array
     */
    public function common () : array;

    /**
     * @return array
     */
    public function toArray() : array;
}