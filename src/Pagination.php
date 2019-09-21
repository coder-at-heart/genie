<?php

namespace Lnk7\Genie;

/**
 * Class Pagination
 *
 * @package Lnk7\Genie
 */
class Pagination {

    /**
     * An array of results (usually from get_posts)
     *
     * @var
     */
    var $data;

    /**
     * What page are we currently showing?
     *
     * @var mixed
     */
    var $page;

    /**
     * The total number of results
     * @var int
     */
    var $total;

    /**
     * The total number of pages
     *
     * @var float
     */
    var $totalPages;

    /**
     * The number of results per page
     *
     * @var int
     */
    var $limit = 10;

    /**
     * results offset
     *
     * @var int
     */
    var $offset;



    /**
     * Static constructor
     *
     * @param $array
     *
     * @return Pagination
     */
    public static function create( $array ) {

        return new static ( $array );
    }



    /**
     * Pagination constructor.
     *
     * @param $array
     */
    public function __construct( $array ) {

        $this->data       = $array;
        $this->total      = count( $this->data );
        $this->totalPages = ceil( $this->total / $this->limit );
        $this->page       = get_query_var( 'page-num' ) ?: 1;
        $this->page       = max( $this->page, 1 );
        $this->page       = min( $this->page, $this->totalPages );
        $this->offset     = ( $this->page - 1 ) * $this->limit;
        if ( $this->offset < 0 ) {
            $this->offset = 0;
        }

        return $this;
    }



    /**
     * @return array|string|void
     */
    public function getLinks() {

        return paginate_links( [
            'base'      => add_query_arg( [ 'page-num' => '%#%' ] ),
            'format'    => '?paged=%#%',
            'mid_size'  => 2,
            'prev_text' => __( '&laquo;' ),
            'next_text' => __( '&raquo;' ),
            'total'     => $this->totalPages,
            'current'   => $this->page,
        ] );
    }



    /**
     * get the Page
     *
     * @return mixed
     */
    public function getPage() {

        return $this->page;
    }



    /**
     * Resturn the results
     *
     * @return array
     */
    public function getResults() {

        return array_slice( $this->data, $this->offset, $this->limit );
    }

}