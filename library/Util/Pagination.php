<?php

namespace WT\Util;

/**
 * A pagination helper class to calculate the necessary variables
 * for database queries for example.
 */
class Pagination
{
    protected $currentPage = 1;
    protected $itemsPerPage;
    protected $totalItems;
    protected $totalPages;

    /**
     * The constructor
     *
     * Creates a pagination helper class and sets its number of
     * items per page.
     *
     * @param integer $itemsPerPage
     */
    public function __construct($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * A get interface to the variables
     *
     * @param string $var
     * @return integer Whatever is requested
     */
    public function __get($var)
    {
        switch($var) {
        case "current":
            return $this->currentPage;
            break;
        case "total":
            return $this->totalPages;
            break;
        case "totalItems":
            return $this->totalItems;
            break;
        case "offset":
            return $this->itemsPerPage * ($this->currentPage - 1);
            break;
        case "number":
            return $this->itemsPerPage;
            break;
        }

        return null;
    }

    /**
     * setTotal
     *
     * Sets the total number of items in the collection paginated.
     *
     * @param integer $total
     * @return boolean Whether the current page has been changed
     */
    public function setTotal($total)
    {
        $this->totalItems = $total;

        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);

        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
            return true;
        }

        return false;
    }

    /**
     * setPage
     *
     * Sets the currently set page so the offset can be calculated
     *
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->currentPage = WT\Util\Value::clamp($page, 1, $this->totalPages);
    }

    /**
     * getInfo
     *
     * Returns an array of information useful for creating the page links
     * in the HTML.
     *
     * @return array Array of information
     */
    public function getInfo()
    {
        return array(
            'current' => $this->currentPage,
            'total' => $this->totalPages,
            'totalItems' => $this->totalItems,
            'offset' => $this->offset,
            'number' => $this->itemsPerPage,
            'prev' => $this->currentPage > 1,
            'next' => $this->currentPage < $this->totalPages
        );
    }
}
