<?php namespace JobApis\Jobs\Client\Providers;

use JobApis\Jobs\Client\Job;

class PhpjobsProvider extends AbstractProvider
{
    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobApis\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $job = new Job([
            'description' => $payload['description'],
            'name' => $payload['title'],
            'title' => $payload['title'],
            'url' => $payload['link'],
        ]);

        // Set date posted
        $job->setDatePostedAsString($payload['pubDate']);

        return $job;
    }

    /**
     * Job response object default keys that should be set
     *
     * @return  array
     */
    public function getDefaultResponseFields()
    {
        return [
            'description',
            'link',
            'pubDate',
            'title',
        ];
    }

    /**
     * Get format
     *
     * @return  string Currently only 'json' and 'xml' supported
     */
    public function getFormat()
    {
        return 'xml';
    }

    /**
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'channel.item';
    }
}
