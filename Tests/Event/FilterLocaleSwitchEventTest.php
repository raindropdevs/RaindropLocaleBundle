<?php

namespace Raindrop\LocaleBundle\Tests\Event;

use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Symfony\Component\HttpFoundation\Request;

class FilterLocaleSwitchEventTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterLocaleSwitchEvent()
    {
        $request = Request::create('/');
        $locale = 'de';
        $filter = new FilterLocaleSwitchEvent($request, $locale);
        $this->assertEquals('/', $filter->getRequest()->getPathInfo());
        $this->assertEquals('de', $filter->getLocale());
    }

    /**
     * @dataProvider invalidType
     */
    public function testThrowsInvalidTypeException($locale)
    {
        $this->setExpectedException('\InvalidArgumentException');
        new FilterLocaleSwitchEvent(Request::create('/'), $locale);
    }

    public function invalidType()
    {
        return array(
          array(123),
          array(''),
          array(null)
        );
    }
}
