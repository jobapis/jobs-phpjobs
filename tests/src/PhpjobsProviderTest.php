<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\PhpjobsProvider;
use JobApis\Jobs\Client\Queries\PhpjobsQuery;
use Mockery as m;

class PhpjobsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = m::mock('JobApis\Jobs\Client\Queries\PhpjobsQuery');

        $this->client = new PhpjobsProvider($this->query);
    }

    public function testItCanGetDefaultResponseFields()
    {
        $fields = [
            'description',
            'link',
            'pubDate',
            'title',
        ];
        $this->assertEquals($fields, $this->client->getDefaultResponseFields());
    }

    public function testItCanGetListingsPath()
    {
        $this->assertEquals('channel.item', $this->client->getListingsPath());
    }

    public function testItCanGetFormat()
    {
        $this->assertEquals('xml', $this->client->getFormat());
    }

    public function testItCanCreateJobObject()
    {
        $payload = $this->createJobArray();

        $results = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $results);
        $this->assertEquals($payload['title'], $results->getTitle());
        $this->assertEquals($payload['title'], $results->getName());
        $this->assertEquals($payload['description'], $results->getDescription());
        $this->assertEquals($payload['link'], $results->getUrl());
    }

    /**
     * Integration test for the client's getJobs() method.
     */
    public function testItCanGetJobs()
    {
        $options = [
            'search_string' => uniqid(),
            'country_code' => uniqid(),
        ];

        $guzzle = m::mock('GuzzleHttp\Client');

        $query = new PhpjobsQuery($options);

        $client = new PhpjobsProvider($query);

        $client->setClient($guzzle);

        $response = m::mock('GuzzleHttp\Message\Response');

        $jobs = $this->createXmlResponse();

        $guzzle->shouldReceive('get')
            ->with($query->getUrl(), [])
            ->once()
            ->andReturn($response);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($jobs);

        $results = $client->getJobs();

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);
    }

    /**
     * Integration test with actual API call to the provider.
     */
    public function testItCanGetJobsFromApi()
    {
        if (!getenv('REAL_CALL')) {
            $this->markTestSkipped('REAL_CALL not set. Real API call will not be made.');
        }

        $keyword = 'engineering';

        $query = new PhpjobsQuery([
            'search_string' => $keyword,
        ]);

        $client = new PhpjobsProvider($query);

        $results = $client->getJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);

        foreach($results as $job) {
            $this->assertEquals($keyword, $job->query);
        }
    }

    private function createJobArray()
    {
        return [
            'title' => uniqid(),
            'link' => uniqid(),
            'description' => uniqid(),
            'pubDate' => 'Fri, '.rand(1,30).' Nov '.rand(2015, 2016).' 18:36:18 Z',
        ];
    }

    private function createXmlResponse()
    {
        return "<?xml version='1.0' encoding='UTF-8' ?><rss version='2.0'><channel><title>Hotel Jobs in Chicago, IL // JobInventory.com</title><item><title>Hotel Specialist Agents</title><link>http://www.jobinventory.com/d/Hotel-Specialist-Agents-Jobs-Chicago-IL-1391153840.html</link><description>Full-time/Regular 9:00 AM to 6:00 PM. 40 hours per week. (Overtime as required) The <b>hotel</b> ... Specialist Agents would pre pay all <b>hotel</b> rooms and help support the reservations team. JOB OVERVIEW work</description><pubDate>2015-04-11 00:48:39</pubDate></item><item><title>Maintenance_Hourly1</title><link>http://www.jobinventory.com/d/Maintenance_hourly1-Jobs-Chicago-IL-1583775509.html</link><description>Data not provided</description><pubDate>2016-05-17 18:13:26</pubDate></item></channel></rss>";
    }
}
