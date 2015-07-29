<?php

/**
 *  Base include file for SimpleTest.
 *
 *  @version    $Id: tag.php 1723 2008-04-08 00:34:10Z lastcraft $
 */

/**#@+
 * include SimpleTest files
 */
require_once dirname(__FILE__).'/parser.php';
require_once dirname(__FILE__).'/encoding.php';
/**#@-*/

/**
 *    HTML or XML tag.
 */
class SimpleTag
{
    public $_name;
    public $_attributes;
    public $_content;

    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param string $name        Tag name.
     *    @param hash $attributes    Attribute names and
     *                               string values. Note that
     *                               the keys must have been
     *                               converted to lower case.
     */
    public function SimpleTag($name, $attributes)
    {
        $this->_name = strtolower(trim($name));
        $this->_attributes = $attributes;
        $this->_content = '';
    }

    /**
     *    Check to see if the tag can have both start and
     *    end tags with content in between.
     *
     *    @return bool        True if content allowed.
     */
    public function expectEndTag()
    {
        return true;
    }

    /**
     *    The current tag should not swallow all content for
     *    itself as it's searchable page content. Private
     *    content tags are usually widgets that contain default
     *    values.
     *
     *    @return bool        False as content is available
     *                           to other tags by default.
     */
    public function isPrivateContent()
    {
        return false;
    }

    /**
     *    Appends string content to the current content.
     *
     *    @param string $content        Additional text.
     */
    public function addContent($content)
    {
        $this->_content .= (string) $content;
    }

    /**
     *    Adds an enclosed tag to the content.
     *
     *    @param SimpleTag $tag    New tag.
     */
    public function addTag(&$tag)
    {
    }

    /**
     *    Accessor for tag name.
     *
     *    @return string       Name of tag.
     */
    public function getTagName()
    {
        return $this->_name;
    }

    /**
     *    List of legal child elements.
     *
     *    @return array        List of element names.
     */
    public function getChildElements()
    {
        return array();
    }

    /**
     *    Accessor for an attribute.
     *
     *    @param string $label    Attribute name.
     *
     *    @return string          Attribute value.
     */
    public function getAttribute($label)
    {
        $label = strtolower($label);
        if (!isset($this->_attributes[$label])) {
            return false;
        }

        return (string) $this->_attributes[$label];
    }

    /**
     *    Sets an attribute.
     *
     *    @param string $label    Attribute name.
     *
     *    @return string $value   New attribute value.
     */
    public function _setAttribute($label, $value)
    {
        $this->_attributes[strtolower($label)] = $value;
    }

    /**
     *    Accessor for the whole content so far.
     *
     *    @return string       Content as big raw string.
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     *    Accessor for content reduced to visible text. Acts
     *    like a text mode browser, normalising space and
     *    reducing images to their alt text.
     *
     *    @return string       Content as plain text.
     */
    public function getText()
    {
        return SimpleHtmlSaxParser::normalise($this->_content);
    }

    /**
     *    Test to see if id attribute matches.
     *
     *    @param string $id        ID to test against.
     *
     *    @return bool          True on match.
     */
    public function isId($id)
    {
        return ($this->getAttribute('id') == $id);
    }
}

/**
 *    Base url.
 */
class SimpleBaseTag extends SimpleTag
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleBaseTag($attributes)
    {
        $this->SimpleTag('base', $attributes);
    }

    /**
     *    Base tag is not a block tag.
     *
     *    @return bool       false
     */
    public function expectEndTag()
    {
        return false;
    }
}

/**
 *    Page title.
 */
class SimpleTitleTag extends SimpleTag
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleTitleTag($attributes)
    {
        $this->SimpleTag('title', $attributes);
    }
}

/**
 *    Link.
 */
class SimpleAnchorTag extends SimpleTag
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleAnchorTag($attributes)
    {
        $this->SimpleTag('a', $attributes);
    }

    /**
     *    Accessor for URL as string.
     *
     *    @return string    Coerced as string.
     */
    public function getHref()
    {
        $url = $this->getAttribute('href');
        if (is_bool($url)) {
            $url = '';
        }

        return $url;
    }
}

/**
 *    Form element.
 */
class SimpleWidget extends SimpleTag
{
    public $_value;
    public $_label;
    public $_is_set;

    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param string $name        Tag name.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleWidget($name, $attributes)
    {
        $this->SimpleTag($name, $attributes);
        $this->_value = false;
        $this->_label = false;
        $this->_is_set = false;
    }

    /**
     *    Accessor for name submitted as the key in
     *    GET/POST variables hash.
     *
     *    @return string        Parsed value.
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     *    Accessor for default value parsed with the tag.
     *
     *    @return string        Parsed value.
     */
    public function getDefault()
    {
        return $this->getAttribute('value');
    }

    /**
     *    Accessor for currently set value or default if
     *    none.
     *
     *    @return string      Value set by form or default
     *                        if none.
     */
    public function getValue()
    {
        if (!$this->_is_set) {
            return $this->getDefault();
        }

        return $this->_value;
    }

    /**
     *    Sets the current form element value.
     *
     *    @param string $value       New value.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        $this->_value = $value;
        $this->_is_set = true;

        return true;
    }

    /**
     *    Resets the form element value back to the
     *    default.
     */
    public function resetValue()
    {
        $this->_is_set = false;
    }

    /**
     *    Allows setting of a label externally, say by a
     *    label tag.
     *
     *    @param string $label    Label to attach.
     */
    public function setLabel($label)
    {
        $this->_label = trim($label);
    }

    /**
     *    Reads external or internal label.
     *
     *    @param string $label    Label to test.
     *
     *    @return bool         True is match.
     */
    public function isLabel($label)
    {
        return $this->_label == trim($label);
    }

    /**
     *    Dispatches the value into the form encoded packet.
     *
     *    @param SimpleEncoding $encoding    Form packet.
     */
    public function write(&$encoding)
    {
        if ($this->getName()) {
            $encoding->add($this->getName(), $this->getValue());
        }
    }
}

/**
 *    Text, password and hidden field.
 */
class SimpleTextTag extends SimpleWidget
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleTextTag($attributes)
    {
        $this->SimpleWidget('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->_setAttribute('value', '');
        }
    }

    /**
     *    Tag contains no content.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }

    /**
     *    Sets the current form element value. Cannot
     *    change the value of a hidden field.
     *
     *    @param string $value       New value.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        if ($this->getAttribute('type') == 'hidden') {
            return false;
        }

        return parent::setValue($value);
    }
}

/**
 *    Submit button as input tag.
 */
class SimpleSubmitTag extends SimpleWidget
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleSubmitTag($attributes)
    {
        $this->SimpleWidget('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->_setAttribute('value', 'Submit');
        }
    }

    /**
     *    Tag contains no end element.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }

    /**
     *    Disables the setting of the button value.
     *
     *    @param string $value       Ignored.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        return false;
    }

    /**
     *    Value of browser visible text.
     *
     *    @return string        Visible label.
     */
    public function getLabel()
    {
        return $this->getValue();
    }

    /**
     *    Test for a label match when searching.
     *
     *    @param string $label     Label to test.
     *
     *    @return bool          True on match.
     */
    public function isLabel($label)
    {
        return trim($label) == trim($this->getLabel());
    }
}

/**
 *    Image button as input tag.
 */
class SimpleImageSubmitTag extends SimpleWidget
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleImageSubmitTag($attributes)
    {
        $this->SimpleWidget('input', $attributes);
    }

    /**
     *    Tag contains no end element.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }

    /**
     *    Disables the setting of the button value.
     *
     *    @param string $value       Ignored.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        return false;
    }

    /**
     *    Value of browser visible text.
     *
     *    @return string        Visible label.
     */
    public function getLabel()
    {
        if ($this->getAttribute('title')) {
            return $this->getAttribute('title');
        }

        return $this->getAttribute('alt');
    }

    /**
     *    Test for a label match when searching.
     *
     *    @param string $label     Label to test.
     *
     *    @return bool          True on match.
     */
    public function isLabel($label)
    {
        return trim($label) == trim($this->getLabel());
    }

    /**
     *    Dispatches the value into the form encoded packet.
     *
     *    @param SimpleEncoding $encoding    Form packet.
     *    @param int $x                  X coordinate of click.
     *    @param int $y                  Y coordinate of click.
     */
    public function write(&$encoding, $x, $y)
    {
        if ($this->getName()) {
            $encoding->add($this->getName().'.x', $x);
            $encoding->add($this->getName().'.y', $y);
        } else {
            $encoding->add('x', $x);
            $encoding->add('y', $y);
        }
    }
}

/**
 *    Submit button as button tag.
 */
class SimpleButtonTag extends SimpleWidget
{
    /**
     *    Starts with a named tag with attributes only.
     *    Defaults are very browser dependent.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleButtonTag($attributes)
    {
        $this->SimpleWidget('button', $attributes);
    }

    /**
     *    Check to see if the tag can have both start and
     *    end tags with content in between.
     *
     *    @return bool        True if content allowed.
     */
    public function expectEndTag()
    {
        return true;
    }

    /**
     *    Disables the setting of the button value.
     *
     *    @param string $value       Ignored.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        return false;
    }

    /**
     *    Value of browser visible text.
     *
     *    @return string        Visible label.
     */
    public function getLabel()
    {
        return $this->getContent();
    }

    /**
     *    Test for a label match when searching.
     *
     *    @param string $label     Label to test.
     *
     *    @return bool          True on match.
     */
    public function isLabel($label)
    {
        return trim($label) == trim($this->getLabel());
    }
}

/**
 *    Content tag for text area.
 */
class SimpleTextAreaTag extends SimpleWidget
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleTextAreaTag($attributes)
    {
        $this->SimpleWidget('textarea', $attributes);
    }

    /**
     *    Accessor for starting value.
     *
     *    @return string        Parsed value.
     */
    public function getDefault()
    {
        return $this->_wrap(SimpleHtmlSaxParser::decodeHtml($this->getContent()));
    }

    /**
     *    Applies word wrapping if needed.
     *
     *    @param string $value      New value.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        return parent::setValue($this->_wrap($value));
    }

    /**
     *    Test to see if text should be wrapped.
     *
     *    @return bool        True if wrapping on.
     */
    public function _wrapIsEnabled()
    {
        if ($this->getAttribute('cols')) {
            $wrap = $this->getAttribute('wrap');
            if (($wrap == 'physical') || ($wrap == 'hard')) {
                return true;
            }
        }

        return false;
    }

    /**
     *    Performs the formatting that is peculiar to
     *    this tag. There is strange behaviour in this
     *    one, including stripping a leading new line.
     *    Go figure. I am using Firefox as a guide.
     *
     *    @param string $text    Text to wrap.
     *
     *    @return string         Text wrapped with carriage
     *                           returns and line feeds
     */
    public function _wrap($text)
    {
        $text = str_replace("\r\r\n", "\r\n", str_replace("\n", "\r\n", $text));
        $text = str_replace("\r\n\n", "\r\n", str_replace("\r", "\r\n", $text));
        if (strncmp($text, "\r\n", strlen("\r\n")) == 0) {
            $text = substr($text, strlen("\r\n"));
        }
        if ($this->_wrapIsEnabled()) {
            return wordwrap(
                    $text,
                    (integer) $this->getAttribute('cols'),
                    "\r\n");
        }

        return $text;
    }

    /**
     *    The content of textarea is not part of the page.
     *
     *    @return bool        True.
     */
    public function isPrivateContent()
    {
        return true;
    }
}

/**
 *    File upload widget.
 */
class SimpleUploadTag extends SimpleWidget
{
    /**
     *    Starts with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleUploadTag($attributes)
    {
        $this->SimpleWidget('input', $attributes);
    }

    /**
     *    Tag contains no content.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }

    /**
     *    Dispatches the value into the form encoded packet.
     *
     *    @param SimpleEncoding $encoding    Form packet.
     */
    public function write(&$encoding)
    {
        if (!file_exists($this->getValue())) {
            return;
        }
        $encoding->attach(
                $this->getName(),
                implode('', file($this->getValue())),
                basename($this->getValue()));
    }
}

/**
 *    Drop down widget.
 */
class SimpleSelectionTag extends SimpleWidget
{
    public $_options;
    public $_choice;

    /**
     *    Starts with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleSelectionTag($attributes)
    {
        $this->SimpleWidget('select', $attributes);
        $this->_options = array();
        $this->_choice = false;
    }

    /**
     *    Adds an option tag to a selection field.
     *
     *    @param SimpleOptionTag $tag     New option.
     */
    public function addTag(&$tag)
    {
        if ($tag->getTagName() == 'option') {
            $this->_options[] = &$tag;
        }
    }

    /**
     *    Text within the selection element is ignored.
     *
     *    @param string $content        Ignored.
     */
    public function addContent($content)
    {
    }

    /**
     *    Scans options for defaults. If none, then
     *    the first option is selected.
     *
     *    @return string        Selected field.
     */
    public function getDefault()
    {
        for ($i = 0, $count = count($this->_options); $i < $count; ++$i) {
            if ($this->_options[$i]->getAttribute('selected') !== false) {
                return $this->_options[$i]->getDefault();
            }
        }
        if ($count > 0) {
            return $this->_options[0]->getDefault();
        }

        return '';
    }

    /**
     *    Can only set allowed values.
     *
     *    @param string $value       New choice.
     *
     *    @return bool            True if allowed.
     */
    public function setValue($value)
    {
        for ($i = 0, $count = count($this->_options); $i < $count; ++$i) {
            if ($this->_options[$i]->isValue($value)) {
                $this->_choice = $i;

                return true;
            }
        }

        return false;
    }

    /**
     *    Accessor for current selection value.
     *
     *    @return string      Value attribute or
     *                        content of opton.
     */
    public function getValue()
    {
        if ($this->_choice === false) {
            return $this->getDefault();
        }

        return $this->_options[$this->_choice]->getValue();
    }
}

/**
 *    Drop down widget.
 */
class MultipleSelectionTag extends SimpleWidget
{
    public $_options;
    public $_values;

    /**
     *    Starts with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function MultipleSelectionTag($attributes)
    {
        $this->SimpleWidget('select', $attributes);
        $this->_options = array();
        $this->_values = false;
    }

    /**
     *    Adds an option tag to a selection field.
     *
     *    @param SimpleOptionTag $tag     New option.
     */
    public function addTag(&$tag)
    {
        if ($tag->getTagName() == 'option') {
            $this->_options[] = &$tag;
        }
    }

    /**
     *    Text within the selection element is ignored.
     *
     *    @param string $content        Ignored.
     */
    public function addContent($content)
    {
    }

    /**
     *    Scans options for defaults to populate the
     *    value array().
     *
     *    @return array        Selected fields.
     */
    public function getDefault()
    {
        $default = array();
        for ($i = 0, $count = count($this->_options); $i < $count; ++$i) {
            if ($this->_options[$i]->getAttribute('selected') !== false) {
                $default[] = $this->_options[$i]->getDefault();
            }
        }

        return $default;
    }

    /**
     *    Can only set allowed values. Any illegal value
     *    will result in a failure, but all correct values
     *    will be set.
     *
     *    @param array $desired      New choices.
     *
     *    @return bool            True if all allowed.
     */
    public function setValue($desired)
    {
        $achieved = array();
        foreach ($desired as $value) {
            $success = false;
            for ($i = 0, $count = count($this->_options); $i < $count; ++$i) {
                if ($this->_options[$i]->isValue($value)) {
                    $achieved[] = $this->_options[$i]->getValue();
                    $success = true;
                    break;
                }
            }
            if (!$success) {
                return false;
            }
        }
        $this->_values = $achieved;

        return true;
    }

    /**
     *    Accessor for current selection value.
     *
     *    @return array      List of currently set options.
     */
    public function getValue()
    {
        if ($this->_values === false) {
            return $this->getDefault();
        }

        return $this->_values;
    }
}

/**
 *    Option for selection field.
 */
class SimpleOptionTag extends SimpleWidget
{
    /**
     *    Stashes the attributes.
     */
    public function SimpleOptionTag($attributes)
    {
        $this->SimpleWidget('option', $attributes);
    }

    /**
     *    Does nothing.
     *
     *    @param string $value      Ignored.
     *
     *    @return bool           Not allowed.
     */
    public function setValue($value)
    {
        return false;
    }

    /**
     *    Test to see if a value matches the option.
     *
     *    @param string $compare    Value to compare with.
     *
     *    @return bool           True if possible match.
     */
    public function isValue($compare)
    {
        $compare = trim($compare);
        if (trim($this->getValue()) == $compare) {
            return true;
        }

        return trim($this->getContent()) == $compare;
    }

    /**
     *    Accessor for starting value. Will be set to
     *    the option label if no value exists.
     *
     *    @return string        Parsed value.
     */
    public function getDefault()
    {
        if ($this->getAttribute('value') === false) {
            return $this->getContent();
        }

        return $this->getAttribute('value');
    }

    /**
     *    The content of options is not part of the page.
     *
     *    @return bool        True.
     */
    public function isPrivateContent()
    {
        return true;
    }
}

/**
 *    Radio button.
 */
class SimpleRadioButtonTag extends SimpleWidget
{
    /**
     *    Stashes the attributes.
     *
     *    @param array $attributes        Hash of attributes.
     */
    public function SimpleRadioButtonTag($attributes)
    {
        $this->SimpleWidget('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->_setAttribute('value', 'on');
        }
    }

    /**
     *    Tag contains no content.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }

    /**
     *    The only allowed value sn the one in the
     *    "value" attribute.
     *
     *    @param string $value      New value.
     *
     *    @return bool           True if allowed.
     */
    public function setValue($value)
    {
        if ($value === false) {
            return parent::setValue($value);
        }
        if ($value != $this->getAttribute('value')) {
            return false;
        }

        return parent::setValue($value);
    }

    /**
     *    Accessor for starting value.
     *
     *    @return string        Parsed value.
     */
    public function getDefault()
    {
        if ($this->getAttribute('checked') !== false) {
            return $this->getAttribute('value');
        }

        return false;
    }
}

/**
 *    Checkbox widget.
 */
class SimpleCheckboxTag extends SimpleWidget
{
    /**
     *    Starts with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleCheckboxTag($attributes)
    {
        $this->SimpleWidget('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->_setAttribute('value', 'on');
        }
    }

    /**
     *    Tag contains no content.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }

    /**
     *    The only allowed value in the one in the
     *    "value" attribute. The default for this
     *    attribute is "on". If this widget is set to
     *    true, then the usual value will be taken.
     *
     *    @param string $value      New value.
     *
     *    @return bool           True if allowed.
     */
    public function setValue($value)
    {
        if ($value === false) {
            return parent::setValue($value);
        }
        if ($value === true) {
            return parent::setValue($this->getAttribute('value'));
        }
        if ($value != $this->getAttribute('value')) {
            return false;
        }

        return parent::setValue($value);
    }

    /**
     *    Accessor for starting value. The default
     *    value is "on".
     *
     *    @return string        Parsed value.
     */
    public function getDefault()
    {
        if ($this->getAttribute('checked') !== false) {
            return $this->getAttribute('value');
        }

        return false;
    }
}

/**
 *    A group of multiple widgets with some shared behaviour.
 */
class SimpleTagGroup
{
    public $_widgets = array();

    /**
     *    Adds a tag to the group.
     *
     *    @param SimpleWidget $widget
     */
    public function addWidget(&$widget)
    {
        $this->_widgets[] = &$widget;
    }

    /**
     *    Accessor to widget set.
     *
     *    @return array        All widgets.
     */
    public function &_getWidgets()
    {
        return $this->_widgets;
    }

    /**
     *    Accessor for an attribute.
     *
     *    @param string $label    Attribute name.
     *
     *    @return bool         Always false.
     */
    public function getAttribute($label)
    {
        return false;
    }

    /**
     *    Fetches the name for the widget from the first
     *    member.
     *
     *    @return string        Name of widget.
     */
    public function getName()
    {
        if (count($this->_widgets) > 0) {
            return $this->_widgets[0]->getName();
        }
    }

    /**
     *    Scans the widgets for one with the appropriate
     *    ID field.
     *
     *    @param string $id        ID value to try.
     *
     *    @return bool          True if matched.
     */
    public function isId($id)
    {
        for ($i = 0, $count = count($this->_widgets); $i < $count; ++$i) {
            if ($this->_widgets[$i]->isId($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     *    Scans the widgets for one with the appropriate
     *    attached label.
     *
     *    @param string $label     Attached label to try.
     *
     *    @return bool          True if matched.
     */
    public function isLabel($label)
    {
        for ($i = 0, $count = count($this->_widgets); $i < $count; ++$i) {
            if ($this->_widgets[$i]->isLabel($label)) {
                return true;
            }
        }

        return false;
    }

    /**
     *    Dispatches the value into the form encoded packet.
     *
     *    @param SimpleEncoding $encoding    Form packet.
     */
    public function write(&$encoding)
    {
        $encoding->add($this->getName(), $this->getValue());
    }
}

/**
 *    A group of tags with the same name within a form.
 */
class SimpleCheckboxGroup extends SimpleTagGroup
{
    /**
     *    Accessor for current selected widget or false
     *    if none.
     *
     *    @return string/array     Widget values or false if none.
     */
    public function getValue()
    {
        $values = array();
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            if ($widgets[$i]->getValue() !== false) {
                $values[] = $widgets[$i]->getValue();
            }
        }

        return $this->_coerceValues($values);
    }

    /**
     *    Accessor for starting value that is active.
     *
     *    @return string/array      Widget values or false if none.
     */
    public function getDefault()
    {
        $values = array();
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            if ($widgets[$i]->getDefault() !== false) {
                $values[] = $widgets[$i]->getDefault();
            }
        }

        return $this->_coerceValues($values);
    }

    /**
     *    Accessor for current set values.
     *
     *    @param string/array/boolean $values   Either a single string, a
     *                                          hash or false for nothing set.
     *
     *    @return bool                       True if all values can be set.
     */
    public function setValue($values)
    {
        $values = $this->_makeArray($values);
        if (!$this->_valuesArePossible($values)) {
            return false;
        }
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            $possible = $widgets[$i]->getAttribute('value');
            if (in_array($widgets[$i]->getAttribute('value'), $values)) {
                $widgets[$i]->setValue($possible);
            } else {
                $widgets[$i]->setValue(false);
            }
        }

        return true;
    }

    /**
     *    Tests to see if a possible value set is legal.
     *
     *    @param string/array/boolean $values   Either a single string, a
     *                                          hash or false for nothing set.
     *
     *    @return bool                       False if trying to set a
     *                                          missing value.
     */
    public function _valuesArePossible($values)
    {
        $matches = array();
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            $possible = $widgets[$i]->getAttribute('value');
            if (in_array($possible, $values)) {
                $matches[] = $possible;
            }
        }

        return ($values == $matches);
    }

    /**
     *    Converts the output to an appropriate format. This means
     *    that no values is false, a single value is just that
     *    value and only two or more are contained in an array.
     *
     *    @param array $values           List of values of widgets.
     *
     *    @return string/array/boolean   Expected format for a tag.
     */
    public function _coerceValues($values)
    {
        if (count($values) == 0) {
            return false;
        } elseif (count($values) == 1) {
            return $values[0];
        } else {
            return $values;
        }
    }

    /**
     *    Converts false or string into array. The opposite of
     *    the coercian method.
     *
     *    @param string/array/boolean $value  A single item is converted
     *                                        to a one item list. False
     *                                        gives an empty list.
     *
     *    @return array                       List of values, possibly empty.
     */
    public function _makeArray($value)
    {
        if ($value === false) {
            return array();
        }
        if (is_string($value)) {
            return array($value);
        }

        return $value;
    }
}

/**
 *    A group of tags with the same name within a form.
 *    Used for radio buttons.
 */
class SimpleRadioGroup extends SimpleTagGroup
{
    /**
     *    Each tag is tried in turn until one is
     *    successfully set. The others will be
     *    unchecked if successful.
     *
     *    @param string $value      New value.
     *
     *    @return bool           True if any allowed.
     */
    public function setValue($value)
    {
        if (!$this->_valueIsPossible($value)) {
            return false;
        }
        $index = false;
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            if (!$widgets[$i]->setValue($value)) {
                $widgets[$i]->setValue(false);
            }
        }

        return true;
    }

    /**
     *    Tests to see if a value is allowed.
     *
     *    @param string    Attempted value.
     *
     *    @return bool  True if a valid value.
     */
    public function _valueIsPossible($value)
    {
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            if ($widgets[$i]->getAttribute('value') == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     *    Accessor for current selected widget or false
     *    if none.
     *
     *    @return string/boolean   Value attribute or
     *                             content of opton.
     */
    public function getValue()
    {
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            if ($widgets[$i]->getValue() !== false) {
                return $widgets[$i]->getValue();
            }
        }

        return false;
    }

    /**
     *    Accessor for starting value that is active.
     *
     *    @return string/boolean      Value of first checked
     *                                widget or false if none.
     */
    public function getDefault()
    {
        $widgets = &$this->_getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; ++$i) {
            if ($widgets[$i]->getDefault() !== false) {
                return $widgets[$i]->getDefault();
            }
        }

        return false;
    }
}

/**
 *    Tag to keep track of labels.
 */
class SimpleLabelTag extends SimpleTag
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleLabelTag($attributes)
    {
        $this->SimpleTag('label', $attributes);
    }

    /**
     *    Access for the ID to attach the label to.
     *
     *    @return string        For attribute.
     */
    public function getFor()
    {
        return $this->getAttribute('for');
    }
}

/**
 *    Tag to aid parsing the form.
 */
class SimpleFormTag extends SimpleTag
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleFormTag($attributes)
    {
        $this->SimpleTag('form', $attributes);
    }
}

/**
 *    Tag to aid parsing the frames in a page.
 */
class SimpleFrameTag extends SimpleTag
{
    /**
     *    Starts with a named tag with attributes only.
     *
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    public function SimpleFrameTag($attributes)
    {
        $this->SimpleTag('frame', $attributes);
    }

    /**
     *    Tag contains no content.
     *
     *    @return bool        False.
     */
    public function expectEndTag()
    {
        return false;
    }
}
