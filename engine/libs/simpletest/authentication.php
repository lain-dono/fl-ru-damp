<?php

/**
 *  Base include file for SimpleTest.
 *
 *  @version    $Id: authentication.php 1720 2008-04-07 02:32:43Z lastcraft $
 */
/**
 *  include http class.
 */
require_once dirname(__FILE__).'/http.php';

/**
 *    Represents a single security realm's identity.
 */
class SimpleRealm
{
    public $_type;
    public $_root;
    public $_username;
    public $_password;

    /**
     *    Starts with the initial entry directory.
     *
     *    @param string $type      Authentication type for this
     *                             realm. Only Basic authentication
     *                             is currently supported.
     *    @param SimpleUrl $url    Somewhere in realm.
     */
    public function SimpleRealm($type, $url)
    {
        $this->_type = $type;
        $this->_root = $url->getBasePath();
        $this->_username = false;
        $this->_password = false;
    }

    /**
     *    Adds another location to the realm.
     *
     *    @param SimpleUrl $url    Somewhere in realm.
     */
    public function stretch($url)
    {
        $this->_root = $this->_getCommonPath($this->_root, $url->getPath());
    }

    /**
     *    Finds the common starting path.
     *
     *    @param string $first        Path to compare.
     *    @param string $second       Path to compare.
     *
     *    @return string              Common directories.
     */
    public function _getCommonPath($first, $second)
    {
        $first = explode('/', $first);
        $second = explode('/', $second);
        for ($i = 0; $i < min(count($first), count($second)); ++$i) {
            if ($first[$i] != $second[$i]) {
                return implode('/', array_slice($first, 0, $i)).'/';
            }
        }

        return implode('/', $first).'/';
    }

    /**
     *    Sets the identity to try within this realm.
     *
     *    @param string $username    Username in authentication dialog.
     *    @param string $username    Password in authentication dialog.
     */
    public function setIdentity($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     *    Accessor for current identity.
     *
     *    @return string        Last succesful username.
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     *    Accessor for current identity.
     *
     *    @return string        Last succesful password.
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     *    Test to see if the URL is within the directory
     *    tree of the realm.
     *
     *    @param SimpleUrl $url    URL to test.
     *
     *    @return bool          True if subpath.
     */
    public function isWithin($url)
    {
        if ($this->_isIn($this->_root, $url->getBasePath())) {
            return true;
        }
        if ($this->_isIn($this->_root, $url->getBasePath().$url->getPage().'/')) {
            return true;
        }

        return false;
    }

    /**
     *    Tests to see if one string is a substring of
     *    another.
     *
     *    @param string $part        Small bit.
     *    @param string $whole       Big bit.
     *
     *    @return bool            True if the small bit is
     *                               in the big bit.
     */
    public function _isIn($part, $whole)
    {
        return strpos($whole, $part) === 0;
    }
}

/**
 *    Manages security realms.
 */
class SimpleAuthenticator
{
    public $_realms;

    /**
     *    Clears the realms.
     */
    public function SimpleAuthenticator()
    {
        $this->restartSession();
    }

    /**
     *    Starts with no realms set up.
     */
    public function restartSession()
    {
        $this->_realms = array();
    }

    /**
     *    Adds a new realm centered the current URL.
     *    Browsers vary wildly on their behaviour in this
     *    regard. Mozilla ignores the realm and presents
     *    only when challenged, wasting bandwidth. IE
     *    just carries on presenting until a new challenge
     *    occours. SimpleTest tries to follow the spirit of
     *    the original standards committee and treats the
     *    base URL as the root of a file tree shaped realm.
     *
     *    @param SimpleUrl $url    Base of realm.
     *    @param string $type      Authentication type for this
     *                             realm. Only Basic authentication
     *                             is currently supported.
     *    @param string $realm     Name of realm.
     */
    public function addRealm($url, $type, $realm)
    {
        $this->_realms[$url->getHost()][$realm] = new SimpleRealm($type, $url);
    }

    /**
     *    Sets the current identity to be presented
     *    against that realm.
     *
     *    @param string $host        Server hosting realm.
     *    @param string $realm       Name of realm.
     *    @param string $username    Username for realm.
     *    @param string $password    Password for realm.
     */
    public function setIdentityForRealm($host, $realm, $username, $password)
    {
        if (isset($this->_realms[$host][$realm])) {
            $this->_realms[$host][$realm]->setIdentity($username, $password);
        }
    }

    /**
     *    Finds the name of the realm by comparing URLs.
     *
     *    @param SimpleUrl $url        URL to test.
     *
     *    @return SimpleRealm          Name of realm.
     */
    public function _findRealmFromUrl($url)
    {
        if (!isset($this->_realms[$url->getHost()])) {
            return false;
        }
        foreach ($this->_realms[$url->getHost()] as $name => $realm) {
            if ($realm->isWithin($url)) {
                return $realm;
            }
        }

        return false;
    }

    /**
     *    Presents the appropriate headers for this location.
     *
     *    @param SimpleHttpRequest $request  Request to modify.
     *    @param SimpleUrl $url              Base of realm.
     */
    public function addHeaders(&$request, $url)
    {
        if ($url->getUsername() && $url->getPassword()) {
            $username = $url->getUsername();
            $password = $url->getPassword();
        } elseif ($realm = $this->_findRealmFromUrl($url)) {
            $username = $realm->getUsername();
            $password = $realm->getPassword();
        } else {
            return;
        }
        $this->addBasicHeaders($request, $username, $password);
    }

    /**
     *    Presents the appropriate headers for this
     *    location for basic authentication.
     *
     *    @param SimpleHttpRequest $request  Request to modify.
     *    @param string $username            Username for realm.
     *    @param string $password            Password for realm.
     *    @static
     */
    public function addBasicHeaders(&$request, $username, $password)
    {
        if ($username && $password) {
            $request->addHeaderLine(
                    'Authorization: Basic '.base64_encode("$username:$password"));
        }
    }
}
