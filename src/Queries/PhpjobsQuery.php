<?php namespace JobApis\Jobs\Client\Queries;

class PhpjobsQuery extends AbstractQuery
{
    /**
     * search_string
     *
     * The search query.
     *
     * @var string
     */
    protected $search_string;

    /**
     * country_code
     *
     * The country string to search.
     *
     * @var string
     */
    protected $country_code;

    /**
     * format
     *
     * Must be 'rss20'
     *
     * @var string
     */
    protected $format;

    /**
     * search
     *
     * Must be '1'
     *
     * @var string
     */
    protected $search;

    /**
     * Get baseUrl
     *
     * @return  string Value of the base url to this api
     */
    public function getBaseUrl()
    {
        return 'http://www.phpjobs.com/search';
    }

    /**
     * Get keyword
     *
     * @return  string Attribute being used as the search keyword
     */
    public function getKeyword()
    {
        return $this->search_string;
    }

    public function defaultAttributes()
    {
        return [
            'format' => 'rss20',
            'search' => '1',
        ];
    }

    public function requiredAttributes()
    {
        return [
            'format',
            'search',
        ];
    }
}
