<?php

namespace UniversalAnalytics\Track;

use UniversalAnalytics\Contracts\JsonableInterface;
use UniversalAnalytics\Contracts\ArrayableInterface;
use UniversalAnalytics\Contracts\ValidableInterface;
use UniversalAnalytics\Exception\InvalidAttributeException;

abstract class Entity implements ArrayableInterface, JsonableInterface, ValidableInterface
{
    /**
     * Shortname for differentiating in Attribute Map.
     */
    protected $shortName = 'entity';

    /**
     * Attributes for tracking entity.
     */
    protected $attributes = array();

    /**
     * Google Attributes for tracking entity
     * These are machine-friendly but not humany friendly,
     * so we do some converting for our human friends.
     */
    protected $googleAttributes = array();

    /**
     * Required attributes
     * Use Google attributes
     *  rather than "human" ones.
     */
    protected $required = array();

    /**
     * All attributes mapping
     * for "human" to "robot" attribute mapping.
     */
    protected $attributeMap = array(
        'type' => 't',

        /* Page */
        'page:hostname' => 'dh',
        'page:page' => 'dp',
        'page:title' => 'dt',

        /* Event */
        'event:category' => 'ec',
        'event:action' => 'ea',
        'event:label' => 'el',
        'event:value' => 'ev',

        /* Commerce Trans */
        'commercetrans:id' => 'ti',
        'commercetrans:affiliation' => 'ta',
        'commercetrans:revenue' => 'tr',
        'commercetrans:shipping' => 'ts',
        'commercetrans:tax' => 'tt',
        'commercetrans:currency_code' => 'cu',

        /* Commerce Item */
        'commerceitem:id' => 'ti',
        'commerceitem:name' => 'in',
        'commerceitem:price' => 'ip',
        'commerceitem:quantity' => 'iq',
        'commerceitem:code' => 'ic',
        'commerceitem:variation' => 'iv',
        'commerceitem:currency_code' => 'cu',

        /* Social Interaction */
        'social:action' => 'sa',
        'social:network' => 'sn',
        'social:target' => 'st',

        /* Exception */
        'exception:description' => 'exd',
        'exception:fatal' => 'exf',

        /* User Timing */
        'user:category' => 'utc',
        'user:variable' => 'utv',
        'user:time' => 'utt',
        'user:label' => 'utl',

    );

    public function __construct($data = array())
    {
        if (count($data) > 0) {
            $this->build($data);
        }
    }

    /**
     * Add object attributes via array.
     *
     * @param Array     Key => Value array of attributes
     *
     * @return Entity
     */
    public function build(Array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Magic Method - retreive
     * from $attributes array.
     *
     * @param String  Attribute name
     *
     * @return Mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Magic Method - set to
     * $attributes array.
     *
     * @param String    Name of attribute
     * @param Mixed     Value of attribute
     *
     * @throws UniversalAnalytics\Exception\InvalidAttributeException
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->attributes)) {
            $this->attributes[$key] = $value;

            $this->googleAttributes[$this->attributeMap[$this->shortName.':'.$key]] = $value;
        } else {
            throw new InvalidAttributeException('Invalid attribute "'.$key.'" passed to '.get_class($this).'.');
        }
    }

    public function valid()
    {
        foreach ($this->required as $key) {
            // If not set or is null, it's invalid, based on required attributes
            if (!isset($this->googleAttributes[$key]) || is_null($this->googleAttributes[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return array representation of this object
     * $attributes array.
     *
     * @return Array
     */
    public function toArray($google = false)
    {
        if ($google) {
            return $this->googleAttributes;
        }

        return $this->attributes;
    }

    /**
     * Return array representation of this object
     * JSON format of $attributes array.
     *
     * @param Int   Options as required by json_encode function
     *
     * @return String JSON representation in string format
     */
    public function toJson($google = false, $options = 0)
    {
        if ($google) {
            return json_encode($this->googleAttributes);
        }

        return json_encode($this->attributes, $options);
    }
}
