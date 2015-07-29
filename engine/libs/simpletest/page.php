<?php

/**
 *  Base include file for SimpleTest.
 *
 *  @version    $Id: page.php 1672 2008-03-02 04:47:34Z edwardzyang $
 */

/**#@+
    *   include other SimpleTest class files
    */
require_once dirname(__FILE__).'/http.php';
require_once dirname(__FILE__).'/parser.php';
require_once dirname(__FILE__).'/tag.php';
require_once dirname(__FILE__).'/form.php';
require_once dirname(__FILE__).'/selector.php';
/**#@-*/

/**
 *    Creates tags and widgets given HTML tag
 *    attributes.
 */
class SimpleTagBuilder
{
    /**
     *    Factory for the tag objects. Creates the
     *    appropriate tag object for the incoming tag name
     *    and attributes.
     *
     *    @param string $name        HTML tag name.
     *    @param hash $attributes    Element attributes.
     *
     *    @return SimpleTag          Tag object.
     */
    public function createTag($name, $attributes)
    {
        static $map = array(
                'a' => 'SimpleAnchorTag',
                'title' => 'SimpleTitleTag',
                'base' => 'SimpleBaseTag',
                'button' => 'SimpleButtonTag',
                'textarea' => 'SimpleTextAreaTag',
                'option' => 'SimpleOptionTag',
                'label' => 'SimpleLabelTag',
                'form' => 'SimpleFormTag',
                'frame' => 'SimpleFrameTag', );
        $attributes = $this->_keysToLowerCase($attributes);
        if (array_key_exists($name, $map)) {
            $tag_class = $map[$name];

            return new $tag_class($attributes);
        } elseif ($name == 'select') {
            return $this->_createSelectionTag($attributes);
        } elseif ($name == 'input') {
            return $this->_createInputTag($attributes);
        }

        return new SimpleTag($name, $attributes);
    }

    /**
     *    Factory for selection fields.
     *
     *    @param hash $attributes    Element attributes.
     *
     *    @return SimpleTag          Tag object.
     */
    public function _createSelectionTag($attributes)
    {
        if (isset($attributes['multiple'])) {
            return new MultipleSelectionTag($attributes);
        }

        return new SimpleSelectionTag($attributes);
    }

    /**
     *    Factory for input tags.
     *
     *    @param hash $attributes    Element attributes.
     *
     *    @return SimpleTag          Tag object.
     */
    public function _createInputTag($attributes)
    {
        if (!isset($attributes['type'])) {
            return new SimpleTextTag($attributes);
        }
        $type = strtolower(trim($attributes['type']));
        $map = array(
                'submit' => 'SimpleSubmitTag',
                'image' => 'SimpleImageSubmitTag',
                'checkbox' => 'SimpleCheckboxTag',
                'radio' => 'SimpleRadioButtonTag',
                'text' => 'SimpleTextTag',
                'hidden' => 'SimpleTextTag',
                'password' => 'SimpleTextTag',
                'file' => 'SimpleUploadTag', );
        if (array_key_exists($type, $map)) {
            $tag_class = $map[$type];

            return new $tag_class($attributes);
        }

        return false;
    }

    /**
     *    Make the keys lower case for case insensitive look-ups.
     *
     *    @param hash $map   Hash to convert.
     *
     *    @return hash       Unchanged values, but keys lower case.
     */
    public function _keysToLowerCase($map)
    {
        $lower = array();
        foreach ($map as $key => $value) {
            $lower[strtolower($key)] = $value;
        }

        return $lower;
    }
}

/**
 *    SAX event handler. Maintains a list of
 *    open tags and dispatches them as they close.
 */
class SimplePageBuilder extends SimpleSaxListener
{
    public $_tags;
    public $_page;
    public $_private_content_tag;

    /**
     *    Sets the builder up empty.
     */
    public function SimplePageBuilder()
    {
        $this->SimpleSaxListener();
    }

    /**
     *    Frees up any references so as to allow the PHP garbage
     *    collection from unset() to work.
     */
    public function free()
    {
        unset($this->_tags);
        unset($this->_page);
        unset($this->_private_content_tags);
    }

    /**
     *    Reads the raw content and send events
     *    into the page to be built.
     *
     *    @param $response SimpleHttpResponse  Fetched response.
     *
     *    @return SimplePage                   Newly parsed page.
     */
    public function &parse($response)
    {
        $this->_tags = array();
        $this->_page = &$this->_createPage($response);
        $parser = &$this->_createParser($this);
        $parser->parse($response->getContent());
        $this->_page->acceptPageEnd();

        return $this->_page;
    }

    /**
     *    Creates an empty page.
     *
     *    @return SimplePage        New unparsed page.
     */
    public function &_createPage($response)
    {
        $page = &new SimplePage($response);

        return $page;
    }

    /**
     *    Creates the parser used with the builder.
     *
     *    @param $listener SimpleSaxListener   Target of parser.
     *
     *    @return SimpleSaxParser              Parser to generate
     *                                         events for the builder.
     */
    public function &_createParser(&$listener)
    {
        $parser = &new SimpleHtmlSaxParser($listener);

        return $parser;
    }

    /**
     *    Start of element event. Opens a new tag.
     *
     *    @param string $name         Element name.
     *    @param hash $attributes     Attributes without content
     *                                are marked as true.
     *
     *    @return bool             False on parse error.
     */
    public function startElement($name, $attributes)
    {
        $factory = &new SimpleTagBuilder();
        $tag = $factory->createTag($name, $attributes);
        if (!$tag) {
            return true;
        }
        if ($tag->getTagName() == 'label') {
            $this->_page->acceptLabelStart($tag);
            $this->_openTag($tag);

            return true;
        }
        if ($tag->getTagName() == 'form') {
            $this->_page->acceptFormStart($tag);

            return true;
        }
        if ($tag->getTagName() == 'frameset') {
            $this->_page->acceptFramesetStart($tag);

            return true;
        }
        if ($tag->getTagName() == 'frame') {
            $this->_page->acceptFrame($tag);

            return true;
        }
        if ($tag->isPrivateContent() && !isset($this->_private_content_tag)) {
            $this->_private_content_tag = &$tag;
        }
        if ($tag->expectEndTag()) {
            $this->_openTag($tag);

            return true;
        }
        $this->_page->acceptTag($tag);

        return true;
    }

    /**
     *    End of element event.
     *
     *    @param string $name        Element name.
     *
     *    @return bool            False on parse error.
     */
    public function endElement($name)
    {
        if ($name == 'label') {
            $this->_page->acceptLabelEnd();

            return true;
        }
        if ($name == 'form') {
            $this->_page->acceptFormEnd();

            return true;
        }
        if ($name == 'frameset') {
            $this->_page->acceptFramesetEnd();

            return true;
        }
        if ($this->_hasNamedTagOnOpenTagStack($name)) {
            $tag = array_pop($this->_tags[$name]);
            if ($tag->isPrivateContent() && $this->_private_content_tag->getTagName() == $name) {
                unset($this->_private_content_tag);
            }
            $this->_addContentTagToOpenTags($tag);
            $this->_page->acceptTag($tag);

            return true;
        }

        return true;
    }

    /**
     *    Test to see if there are any open tags awaiting
     *    closure that match the tag name.
     *
     *    @param string $name        Element name.
     *
     *    @return bool            True if any are still open.
     */
    public function _hasNamedTagOnOpenTagStack($name)
    {
        return isset($this->_tags[$name]) && (count($this->_tags[$name]) > 0);
    }

    /**
     *    Unparsed, but relevant data. The data is added
     *    to every open tag.
     *
     *    @param string $text        May include unparsed tags.
     *
     *    @return bool            False on parse error.
     */
    public function addContent($text)
    {
        if (isset($this->_private_content_tag)) {
            $this->_private_content_tag->addContent($text);
        } else {
            $this->_addContentToAllOpenTags($text);
        }

        return true;
    }

    /**
     *    Any content fills all currently open tags unless it
     *    is part of an option tag.
     *
     *    @param string $text        May include unparsed tags.
     */
    public function _addContentToAllOpenTags($text)
    {
        foreach (array_keys($this->_tags) as $name) {
            for ($i = 0, $count = count($this->_tags[$name]); $i < $count; ++$i) {
                $this->_tags[$name][$i]->addContent($text);
            }
        }
    }

    /**
     *    Parsed data in tag form. The parsed tag is added
     *    to every open tag. Used for adding options to select
     *    fields only.
     *
     *    @param SimpleTag $tag        Option tags only.
     */
    public function _addContentTagToOpenTags(&$tag)
    {
        if ($tag->getTagName() != 'option') {
            return;
        }
        foreach (array_keys($this->_tags) as $name) {
            for ($i = 0, $count = count($this->_tags[$name]); $i < $count; ++$i) {
                $this->_tags[$name][$i]->addTag($tag);
            }
        }
    }

    /**
     *    Opens a tag for receiving content. Multiple tags
     *    will be receiving input at the same time.
     *
     *    @param SimpleTag $tag        New content tag.
     */
    public function _openTag(&$tag)
    {
        $name = $tag->getTagName();
        if (!in_array($name, array_keys($this->_tags))) {
            $this->_tags[$name] = array();
        }
        $this->_tags[$name][] = &$tag;
    }
}

/**
 *    A wrapper for a web page.
 */
class SimplePage
{
    public $_links;
    public $_title;
    public $_last_widget;
    public $_label;
    public $_left_over_labels;
    public $_open_forms;
    public $_complete_forms;
    public $_frameset;
    public $_frames;
    public $_frameset_nesting_level;
    public $_transport_error;
    public $_raw;
    public $_text;
    public $_sent;
    public $_headers;
    public $_method;
    public $_url;
    public $_base = false;
    public $_request_data;

    /**
     *    Parses a page ready to access it's contents.
     *
     *    @param SimpleHttpResponse $response     Result of HTTP fetch.
     */
    public function SimplePage($response = false)
    {
        $this->_links = array();
        $this->_title = false;
        $this->_left_over_labels = array();
        $this->_open_forms = array();
        $this->_complete_forms = array();
        $this->_frameset = false;
        $this->_frames = array();
        $this->_frameset_nesting_level = 0;
        $this->_text = false;
        if ($response) {
            $this->_extractResponse($response);
        } else {
            $this->_noResponse();
        }
    }

    /**
     *    Extracts all of the response information.
     *
     *    @param SimpleHttpResponse $response    Response being parsed.
     */
    public function _extractResponse($response)
    {
        $this->_transport_error = $response->getError();
        $this->_raw = $response->getContent();
        $this->_sent = $response->getSent();
        $this->_headers = $response->getHeaders();
        $this->_method = $response->getMethod();
        $this->_url = $response->getUrl();
        $this->_request_data = $response->getRequestData();
    }

    /**
     *    Sets up a missing response.
     */
    public function _noResponse()
    {
        $this->_transport_error = 'No page fetched yet';
        $this->_raw = false;
        $this->_sent = false;
        $this->_headers = false;
        $this->_method = 'GET';
        $this->_url = false;
        $this->_request_data = false;
    }

    /**
     *    Original request as bytes sent down the wire.
     *
     *    @return mixed              Sent content.
     */
    public function getRequest()
    {
        return $this->_sent;
    }

    /**
     *    Accessor for raw text of page.
     *
     *    @return string        Raw unparsed content.
     */
    public function getRaw()
    {
        return $this->_raw;
    }

    /**
     *    Accessor for plain text of page as a text browser
     *    would see it.
     *
     *    @return string        Plain text of page.
     */
    public function getText()
    {
        if (!$this->_text) {
            $this->_text = SimpleHtmlSaxParser::normalise($this->_raw);
        }

        return $this->_text;
    }

    /**
     *    Accessor for raw headers of page.
     *
     *    @return string       Header block as text.
     */
    public function getHeaders()
    {
        if ($this->_headers) {
            return $this->_headers->getRaw();
        }

        return false;
    }

    /**
     *    Original request method.
     *
     *    @return string        GET, POST or HEAD.
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     *    Original resource name.
     *
     *    @return SimpleUrl        Current url.
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     *    Base URL if set via BASE tag page url otherwise.
     *
     *    @return SimpleUrl        Base url.
     */
    public function getBaseUrl()
    {
        return $this->_base;
    }

    /**
     *    Original request data.
     *
     *    @return mixed              Sent content.
     */
    public function getRequestData()
    {
        return $this->_request_data;
    }

    /**
     *    Accessor for last error.
     *
     *    @return string        Error from last response.
     */
    public function getTransportError()
    {
        return $this->_transport_error;
    }

    /**
     *    Accessor for current MIME type.
     *
     *    @return string    MIME type as string; e.g. 'text/html'
     */
    public function getMimeType()
    {
        if ($this->_headers) {
            return $this->_headers->getMimeType();
        }

        return false;
    }

    /**
     *    Accessor for HTTP response code.
     *
     *    @return int    HTTP response code received.
     */
    public function getResponseCode()
    {
        if ($this->_headers) {
            return $this->_headers->getResponseCode();
        }

        return false;
    }

    /**
     *    Accessor for last Authentication type. Only valid
     *    straight after a challenge (401).
     *
     *    @return string    Description of challenge type.
     */
    public function getAuthentication()
    {
        if ($this->_headers) {
            return $this->_headers->getAuthentication();
        }

        return false;
    }

    /**
     *    Accessor for last Authentication realm. Only valid
     *    straight after a challenge (401).
     *
     *    @return string    Name of security realm.
     */
    public function getRealm()
    {
        if ($this->_headers) {
            return $this->_headers->getRealm();
        }

        return false;
    }

    /**
     *    Accessor for current frame focus. Will be
     *    false as no frames.
     *
     *    @return array    Always empty.
     */
    public function getFrameFocus()
    {
        return array();
    }

    /**
     *    Sets the focus by index. The integer index starts from 1.
     *
     *    @param int $choice    Chosen frame.
     *
     *    @return bool           Always false.
     */
    public function setFrameFocusByIndex($choice)
    {
        return false;
    }

    /**
     *    Sets the focus by name. Always fails for a leaf page.
     *
     *    @param string $name    Chosen frame.
     *
     *    @return bool        False as no frames.
     */
    public function setFrameFocus($name)
    {
        return false;
    }

    /**
     *    Clears the frame focus. Does nothing for a leaf page.
     */
    public function clearFrameFocus()
    {
    }

    /**
     *    Adds a tag to the page.
     *
     *    @param SimpleTag $tag        Tag to accept.
     */
    public function acceptTag(&$tag)
    {
        if ($tag->getTagName() == 'a') {
            $this->_addLink($tag);
        } elseif ($tag->getTagName() == 'base') {
            $this->_setBase($tag);
        } elseif ($tag->getTagName() == 'title') {
            $this->_setTitle($tag);
        } elseif ($this->_isFormElement($tag->getTagName())) {
            for ($i = 0; $i < count($this->_open_forms); ++$i) {
                $this->_open_forms[$i]->addWidget($tag);
            }
            $this->_last_widget = &$tag;
        }
    }

    /**
     *    Opens a label for a described widget.
     *
     *    @param SimpleFormTag $tag      Tag to accept.
     */
    public function acceptLabelStart(&$tag)
    {
        $this->_label = &$tag;
        unset($this->_last_widget);
    }

    /**
     *    Closes the most recently opened label.
     */
    public function acceptLabelEnd()
    {
        if (isset($this->_label)) {
            if (isset($this->_last_widget)) {
                $this->_last_widget->setLabel($this->_label->getText());
                unset($this->_last_widget);
            } else {
                $this->_left_over_labels[] = SimpleTestCompatibility::copy($this->_label);
            }
            unset($this->_label);
        }
    }

    /**
     *    Tests to see if a tag is a possible form
     *    element.
     *
     *    @param string $name     HTML element name.
     *
     *    @return bool         True if form element.
     */
    public function _isFormElement($name)
    {
        return in_array($name, array('input', 'button', 'textarea', 'select'));
    }

    /**
     *    Opens a form. New widgets go here.
     *
     *    @param SimpleFormTag $tag      Tag to accept.
     */
    public function acceptFormStart(&$tag)
    {
        $this->_open_forms[] = &new SimpleForm($tag, $this);
    }

    /**
     *    Closes the most recently opened form.
     */
    public function acceptFormEnd()
    {
        if (count($this->_open_forms)) {
            $this->_complete_forms[] = array_pop($this->_open_forms);
        }
    }

    /**
     *    Opens a frameset. A frameset may contain nested
     *    frameset tags.
     *
     *    @param SimpleFramesetTag $tag      Tag to accept.
     */
    public function acceptFramesetStart(&$tag)
    {
        if (!$this->_isLoadingFrames()) {
            $this->_frameset = &$tag;
        }
        ++$this->_frameset_nesting_level;
    }

    /**
     *    Closes the most recently opened frameset.
     */
    public function acceptFramesetEnd()
    {
        if ($this->_isLoadingFrames()) {
            --$this->_frameset_nesting_level;
        }
    }

    /**
     *    Takes a single frame tag and stashes it in
     *    the current frame set.
     *
     *    @param SimpleFrameTag $tag      Tag to accept.
     */
    public function acceptFrame(&$tag)
    {
        if ($this->_isLoadingFrames()) {
            if ($tag->getAttribute('src')) {
                $this->_frames[] = &$tag;
            }
        }
    }

    /**
     *    Test to see if in the middle of reading
     *    a frameset.
     *
     *    @return bool        True if inframeset.
     */
    public function _isLoadingFrames()
    {
        if (!$this->_frameset) {
            return false;
        }

        return ($this->_frameset_nesting_level > 0);
    }

    /**
     *    Test to see if link is an absolute one.
     *
     *    @param string $url     Url to test.
     *
     *    @return bool        True if absolute.
     */
    public function _linkIsAbsolute($url)
    {
        $parsed = new SimpleUrl($url);

        return (boolean) ($parsed->getScheme() && $parsed->getHost());
    }

    /**
     *    Adds a link to the page.
     *
     *    @param SimpleAnchorTag $tag      Link to accept.
     */
    public function _addLink($tag)
    {
        $this->_links[] = $tag;
    }

    /**
     *    Marker for end of complete page. Any work in
     *    progress can now be closed.
     */
    public function acceptPageEnd()
    {
        while (count($this->_open_forms)) {
            $this->_complete_forms[] = array_pop($this->_open_forms);
        }
        foreach ($this->_left_over_labels as $label) {
            for ($i = 0, $count = count($this->_complete_forms); $i < $count; ++$i) {
                $this->_complete_forms[$i]->attachLabelBySelector(
                        new SimpleById($label->getFor()),
                        $label->getText());
            }
        }
    }

    /**
     *    Test for the presence of a frameset.
     *
     *    @return bool        True if frameset.
     */
    public function hasFrames()
    {
        return (boolean) $this->_frameset;
    }

    /**
     *    Accessor for frame name and source URL for every frame that
     *    will need to be loaded. Immediate children only.
     *
     *    @return boolean/array     False if no frameset or
     *                              otherwise a hash of frame URLs.
     *                              The key is either a numerical
     *                              base one index or the name attribute.
     */
    public function getFrameset()
    {
        if (!$this->_frameset) {
            return false;
        }
        $urls = array();
        for ($i = 0; $i < count($this->_frames); ++$i) {
            $name = $this->_frames[$i]->getAttribute('name');
            $url = new SimpleUrl($this->_frames[$i]->getAttribute('src'));
            $urls[$name ? $name : $i + 1] = $this->expandUrl($url);
        }

        return $urls;
    }

    /**
     *    Fetches a list of loaded frames.
     *
     *    @return array/string    Just the URL for a single page.
     */
    public function getFrames()
    {
        $url = $this->expandUrl($this->getUrl());

        return $url->asString();
    }

    /**
     *    Accessor for a list of all links.
     *
     *    @return array   List of urls with scheme of
     *                    http or https and hostname.
     */
    public function getUrls()
    {
        $all = array();
        foreach ($this->_links as $link) {
            $url = $this->_getUrlFromLink($link);
            $all[] = $url->asString();
        }

        return $all;
    }

    /**
     *    Accessor for URLs by the link label. Label will match
     *    regardess of whitespace issues and case.
     *
     *    @param string $label    Text of link.
     *
     *    @return array           List of links with that label.
     */
    public function getUrlsByLabel($label)
    {
        $matches = array();
        foreach ($this->_links as $link) {
            if ($link->getText() == $label) {
                $matches[] = $this->_getUrlFromLink($link);
            }
        }

        return $matches;
    }

    /**
     *    Accessor for a URL by the id attribute.
     *
     *    @param string $id       Id attribute of link.
     *
     *    @return SimpleUrl       URL with that id of false if none.
     */
    public function getUrlById($id)
    {
        foreach ($this->_links as $link) {
            if ($link->getAttribute('id') === (string) $id) {
                return $this->_getUrlFromLink($link);
            }
        }

        return false;
    }

    /**
     *    Converts a link tag into a target URL.
     *
     *    @param SimpleAnchor $link    Parsed link.
     *
     *    @return SimpleUrl            URL with frame target if any.
     */
    public function _getUrlFromLink($link)
    {
        $url = $this->expandUrl($link->getHref());
        if ($link->getAttribute('target')) {
            $url->setTarget($link->getAttribute('target'));
        }

        return $url;
    }

    /**
     *    Expands expandomatic URLs into fully qualified
     *    URLs.
     *
     *    @param SimpleUrl $url        Relative URL.
     *
     *    @return SimpleUrl            Absolute URL.
     */
    public function expandUrl($url)
    {
        if (!is_object($url)) {
            $url = new SimpleUrl($url);
        }
        $location = $this->getBaseUrl() ? $this->getBaseUrl() : new SimpleUrl();

        return $url->makeAbsolute($location->makeAbsolute($this->getUrl()));
    }

    /**
     *    Sets the base url for the page.
     *
     *    @param SimpleTag $tag    Base URL for page.
     */
    public function _setBase(&$tag)
    {
        $url = $tag->getAttribute('href');
        $this->_base = new SimpleUrl($url);
    }

    /**
     *    Sets the title tag contents.
     *
     *    @param SimpleTitleTag $tag    Title of page.
     */
    public function _setTitle(&$tag)
    {
        $this->_title = &$tag;
    }

    /**
     *    Accessor for parsed title.
     *
     *    @return string     Title or false if no title is present.
     */
    public function getTitle()
    {
        if ($this->_title) {
            return $this->_title->getText();
        }

        return false;
    }

    /**
     *    Finds a held form by button label. Will only
     *    search correctly built forms.
     *
     *    @param SimpleSelector $selector       Button finder.
     *
     *    @return SimpleForm                    Form object containing
     *                                          the button.
     */
    public function &getFormBySubmit($selector)
    {
        for ($i = 0; $i < count($this->_complete_forms); ++$i) {
            if ($this->_complete_forms[$i]->hasSubmit($selector)) {
                return $this->_complete_forms[$i];
            }
        }
        $null = null;

        return $null;
    }

    /**
     *    Finds a held form by image using a selector.
     *    Will only search correctly built forms.
     *
     *    @param SimpleSelector $selector  Image finder.
     *
     *    @return SimpleForm               Form object containing
     *                                     the image.
     */
    public function &getFormByImage($selector)
    {
        for ($i = 0; $i < count($this->_complete_forms); ++$i) {
            if ($this->_complete_forms[$i]->hasImage($selector)) {
                return $this->_complete_forms[$i];
            }
        }
        $null = null;

        return $null;
    }

    /**
     *    Finds a held form by the form ID. A way of
     *    identifying a specific form when we have control
     *    of the HTML code.
     *
     *    @param string $id     Form label.
     *
     *    @return SimpleForm    Form object containing the matching ID.
     */
    public function &getFormById($id)
    {
        for ($i = 0; $i < count($this->_complete_forms); ++$i) {
            if ($this->_complete_forms[$i]->getId() == $id) {
                return $this->_complete_forms[$i];
            }
        }
        $null = null;

        return $null;
    }

    /**
     *    Sets a field on each form in which the field is
     *    available.
     *
     *    @param SimpleSelector $selector    Field finder.
     *    @param string $value               Value to set field to.
     *
     *    @return bool                    True if value is valid.
     */
    public function setField($selector, $value, $position = false)
    {
        $is_set = false;
        for ($i = 0; $i < count($this->_complete_forms); ++$i) {
            if ($this->_complete_forms[$i]->setField($selector, $value, $position)) {
                $is_set = true;
            }
        }

        return $is_set;
    }

    /**
     *    Accessor for a form element value within a page.
     *
     *    @param SimpleSelector $selector    Field finder.
     *
     *    @return string/boolean             A string if the field is
     *                                       present, false if unchecked
     *                                       and null if missing.
     */
    public function getField($selector)
    {
        for ($i = 0; $i < count($this->_complete_forms); ++$i) {
            $value = $this->_complete_forms[$i]->getValue($selector);
            if (isset($value)) {
                return $value;
            }
        }

        return;
    }
}
