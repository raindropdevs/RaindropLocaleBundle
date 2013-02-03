<?php

namespace Raindrop\LocaleBundle\Tests\LocaleGuesser;

use Raindrop\LocaleBundle\LocaleGuesser\SessionLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{

    public function testGuesserExtendsInterface()
    {
        $request = $this->getRequestWithSessionLocale();
        $guesser = $this->getGuesser($request->getSession(), $this->getMetaValidatorMock());
        $this->assertTrue($guesser instanceof LocaleGuesserInterface);
    }

    public function testLocaleIsRetrievedFromSessionIfSet()
    {
        $request = $this->getRequestWithSessionLocale();
        $metaValidator = $this->getMetaValidatorMock();
        $inputs = array('ru');
        $outputs = array(true);
        $expectation = $metaValidator->expects($this->once())
                ->method('isAllowed');
        $this->setMultipleMatching($expectation, $inputs, $outputs);

        $guesser = $this->getGuesser($request->getSession(), $metaValidator);
        $guesser->guessLocale($request);
        $this->assertEquals('ru', $guesser->getIdentifiedLocale());
    }

    public function testLocaleIsNotRetrievedFromSessionIfInvalid()
    {
        $request = $this->getRequestWithSessionLocale();
        $metaValidator = $this->getMetaValidatorMock();
        $expectation = $metaValidator->expects($this->once())
                ->method('isAllowed');
        $this->setMultipleMatching($expectation, array('ru'), array(false));

        $guesser = $this->getGuesser($request->getSession(), $metaValidator);
        $guesser->guessLocale($request);
        $this->assertFalse($guesser->getIdentifiedLocale());
    }

    private function getGuesser($session = null, $metaValidator)
    {

        return new SessionLocaleGuesser($session, $metaValidator);
    }

    private function getRequestWithSessionLocale($locale = 'ru')
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set('Raindrop_locale', $locale);
        $request = Request::create('/');
        $request->setSession($session);
        $request->headers->set('Accept-language', 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4');

        return $request;
    }

    private function getSession()
    {
        return new Session(new MockArraySessionStorage());
    }

    public function getMetaValidatorMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\LocaleBundle\Validator\MetaValidator')->disableOriginalConstructor()->getMock();

        return $mock;
    }

    /**
     * A callback is built and linked to the mocked method.
     */
    public function setMultipleMatching($expectation,
                                        array $inputs,
                                        array $outputs)
    {
        $testCase = $this;
        $callback = function () use ($inputs, $outputs, $testCase) {
            $args = func_get_args();
            $testCase->assertContains($args[0], $inputs);
            $index = array_search($args[0], $inputs);

            return $outputs[$index];
        };
        $expectation->will($this->returnCallback($callback));
    }
}
