<?php namespace JobApis\Jobs\Client\Test;

use JobApis\Jobs\Client\Queries\PhpjobsQuery;
use Mockery as m;

class PhpjobsQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = new PhpjobsQuery();
    }

    public function testItCanGetBaseUrl()
    {
        $this->assertEquals(
            'http://www.phpjobs.com/search',
            $this->query->getBaseUrl()
        );
    }

    public function testItCanGetKeyword()
    {
        $keyword = uniqid();
        $this->query->set('search_string', $keyword);
        $this->assertEquals($keyword, $this->query->getKeyword());
    }

    public function testItCanAddAttributesToUrl()
    {
        $this->query->set('search_string', uniqid());
        $this->query->set('country_code', uniqid());

        $url = $this->query->getUrl();

        $this->assertContains('search_string=', $url);
        $this->assertContains('country_code=', $url);
    }

    public function testItAddsDefaultAttributes()
    {
        $url = $this->query->getUrl();

        $this->assertContains('format=rss20', $url);
        $this->assertContains('search=1', $url);
    }

    public function testItFailsWithoutRequiredAttributes()
    {
        $this->query->set('format', null);

        $this->assertFalse($this->query->isValid());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenSettingInvalidAttribute()
    {
        $this->query->set(uniqid(), uniqid());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenGettingInvalidAttribute()
    {
        $this->query->get(uniqid());
    }

    public function testItSetsAndGetsValidAttributes()
    {
        $attributes = [
            'search_string' => uniqid(),
            'country_code' => uniqid(),
        ];

        foreach ($attributes as $key => $value) {
            $this->query->set($key, $value);
        }

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $this->query->get($key));
        }
    }
}
