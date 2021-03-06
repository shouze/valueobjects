<?php

namespace ValueObjects\Tests\Web;

use ValueObjects\Null\Null;
use ValueObjects\String\String;
use ValueObjects\Tests\TestCase;
use ValueObjects\Web\FragmentIdentifier;
use ValueObjects\Web\NullPortNumber;
use ValueObjects\Web\Path;
use ValueObjects\Web\PortNumber;
use ValueObjects\Web\QueryString;
use ValueObjects\Web\SchemeName;
use ValueObjects\Web\Url;
use ValueObjects\Web\Hostname;

class UrlTest extends TestCase
{
    /** @var Url */
    protected $url;

    public function setup()
    {
        $this->url = new Url(
            new SchemeName('http'),
            new String('user'),
            new String('pass'),
            new Hostname('foo.com'),
            new PortNumber(80),
            new Path('/bar'),
            new QueryString('?querystring'),
            new FragmentIdentifier('#fragmentidentifier')
        );
    }

    public function testFromNative()
    {
        $nativeUrlString = 'http://user:pass@foo.com:80/bar?querystring#fragmentidentifier';
        $fromNativeUrl   = Url::fromNative($nativeUrlString);

        $this->assertTrue($this->url->equals($fromNativeUrl));
    }

    public function testEquals()
    {
        $url2 = new Url(
            new SchemeName('http'),
            new String('user'),
            new String('pass'),
            new Hostname('foo.com'),
            new PortNumber(80),
            new Path('/bar'),
            new QueryString('?querystring'),
            new FragmentIdentifier('#fragmentidentifier')
        );

        $url3 = new Url(
            new SchemeName('git+ssh'),
            new String(''),
            new String(''),
            new Hostname('github.com'),
            new NullPortNumber(),
            new Path('/nicolopignatelli/valueobjects'),
            new QueryString('?querystring'),
            new FragmentIdentifier('#fragmentidentifier')
        );

        $this->assertTrue($this->url->equals($url2));
        $this->assertTrue($url2->equals($this->url));
        $this->assertFalse($this->url->equals($url3));

        $mock = $this->getMock('ValueObjects\ValueObjectInterface');
        $this->assertFalse($this->url->equals($mock));
    }

    public function testGetDomain()
    {
        $domain = new Hostname('foo.com');
        $this->assertTrue($this->url->getDomain()->equals($domain));
    }

    public function testGetFragmentIdentifier()
    {
        $fragment = new FragmentIdentifier('#fragmentidentifier');
        $this->assertTrue($this->url->getFragmentIdentifier()->equals($fragment));
    }

    public function testGetPassword()
    {
        $password = new String('pass');
        $this->assertTrue($this->url->getPassword()->equals($password));
    }

    public function testGetPath()
    {
        $path = new Path('/bar');
        $this->assertTrue($this->url->getPath()->equals($path));
    }

    public function testGetPort()
    {
        $port = new PortNumber(80);
        $this->assertTrue($this->url->getPort()->equals($port));
    }

    public function testGetQueryString()
    {
        $queryString = new QueryString('?querystring');
        $this->assertTrue($this->url->getQueryString()->equals($queryString));
    }

    public function testGetScheme()
    {
        $scheme = new SchemeName('http');
        $this->assertTrue($this->url->getScheme()->equals($scheme));
    }

    public function testGetUser()
    {
        $user = new String('user');
        $this->assertTrue($this->url->getUser()->equals($user));
    }

    public function testToString()
    {
        $this->assertSame('http://user:pass@foo.com:80/bar?querystring#fragmentidentifier', $this->url->__toString());
    }

    public function testAuthlessUrlToString()
    {
        $authlessUrl = new Url(
            new SchemeName('http'),
            new String(''),
            new String(''),
            new Hostname('foo.com'),
            new PortNumber(80),
            new Path('/bar'),
            new QueryString('?querystring'),
            new FragmentIdentifier('#fragmentidentifier')
        );
        $this->assertSame('http://foo.com:80/bar?querystring#fragmentidentifier', $authlessUrl->__toString());
    }

    public function testNullPortUrlToString()
    {
        $nullPortUrl = new Url(
            new SchemeName('http'),
            new String('user'),
            new String(''),
            new Hostname('foo.com'),
            new NullPortNumber(),
            new Path('/bar'),
            new QueryString('?querystring'),
            new FragmentIdentifier('#fragmentidentifier')
        );
        $this->assertSame('http://user@foo.com/bar?querystring#fragmentidentifier', $nullPortUrl->__toString());
    }
}