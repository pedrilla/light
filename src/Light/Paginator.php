<?php

declare(strict_types=1);

namespace Light;

/**
 * Class Paginator
 * @package Light
 */
class Paginator
{
    /**
     * @var Model |null
     */
    private $_model = null;

    /**
     * @var array|null
     */
    private $_cond = [];

    /**
     * @var array|null
     */
    private $_sort = [];

    /**
     * If this parameter will be specify (by calling setDataMapper) data will be mapped
     *
     * @var Map
     */
    private $_map = null;

    /**
     * @var int
     */
    private $_page = 0;

    /**
     * @var int
     */
    private $_itemsPerPage = 10;

    /**
     * @var string
     */
    private $_template = 'pagination';

    /**
     * Paginator constructor.
     * @param Model $model
     * @param array|string|null $cond
     * @param array|string|null $sort
     */
    public function __construct(Model $model, $cond = null, $sort = null)
    {
        $this->_model = $model;

        $this->_cond = $cond;
        $this->_sort = $sort;
    }

    /**
     * Return collection of selected items
     *
     * @return array|Model
     */
    public function getItems()
    {
        /** @var Model $modelClassName */
        $modelClassName = get_class($this->_model);

        $limit = $this->getItemsPerPage();
        $offset = ($this->getPage() - 1) * $limit;

        $data = $modelClassName::fetchAll($this->_cond, $this->_sort, $limit, $offset);

        if ($this->_map) {
            $this->_map->setData($data);
            return $this->_map->toArray();
        }

        return $data;
    }

    /**
     * @return int
     */
    public function count()
    {

        /** @var Model $modelClassName */
        $modelClassName = get_class($this->_model);

        return $modelClassName::count($this->_cond);
    }

    /**
     * @param Map $map
     */
    public function setMap(Map $map)
    {
        $this->_map = $map;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->_page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->_page = $page;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->_itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->_itemsPerPage = $itemsPerPage;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->_template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->_template = $template;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $totalCount = $this->_model::count($this->_cond);
        $currentPage = $this->_page;
        $itemsPerPage = $this->_itemsPerPage;
        $range = 5;

        if ($totalCount <= $itemsPerPage) {
            return false;
        }

        $prev = $currentPage != 1 ? $currentPage - 1 : false;
        $totalPages = intval(ceil($totalCount / $itemsPerPage));
        $next = $currentPage < $totalPages ? $currentPage + 1 : false;

        $pages = range(1, $totalPages);

        if ($totalPages > $range) {

            $start = $currentPage - ceil($range / 2);

            if ($start < 0) {
                $start = 0;
            }

            if ($start + $range > $totalPages) {
                $start = $totalPages - $range;
            }

            $pages = array_slice($pages, intval($start), $range);
        }

        $view = new View();
        $view->setPath(__DIR__ . '/Crud');

        return $view->render($this->_template, [
            'prev' => $prev,
            'next' => $next,
            'pages' => $pages,
            'paginator' => $this
        ]);
    }
}
