<?php

namespace tools;

/**
 * Epp2Array, EPP/XML to a simple readable (usable) array
 *
 * An EPP (http://en.wikipedia.org/wiki/Extensible_Provisioning_Protocol), with Namespace support, to a simple Array
 * Still usable for normal XML too. CDATA is supported.
 * Also, there's hardly any (good) error handling so be careful of that. Mainly because of 'no DTD' errors (wont validate)
 * @author Peter Notenboom <peter@petern.nl>
 * @version 1.4
 * @package Xml2Array
 */

class Xml2Array
{

    /**
     * xml in raw string
     * @access private
     * @var string
     * @see loadXML()
     */
    private static $xml_raw;

    /**
     * xml in DOM Object
     * @access private
     * @var object
     * @see loadXML()
     */
    private static $xml;

    /**
     * Loads the XML in raw string + DOM object. Should return true/false if it's an OK xml or not..
     * @param string $xml_raw
     * @return boolean
     */
    public static function loadXML($xml_raw)
    {
        libxml_use_internal_errors(true);
        self::$xml_raw = $xml_raw;
        self::$xml = new \DOMDocument('1.0', 'utf-8');
        self::$xml->loadXML($xml_raw);

        if (is_object(self::$xml) === true) {
            //double check
            //if (self::$xml->validate()) { //Warning: DOMDocument::validate(): no DTD found!
            return is_object(simplexml_load_string($xml_raw));
        }
    }

    /**
     * Return whole Array by giving a complete XML $xml_raw string
     * @return array
     */
    public static function getArray()
    {
        if (self::$xml) {
            return self::processArray(self::$xml);
        }
    }

    /**
     * Return all namespaces in an Array, might be useful somehow
     * Key = prefix, value = uri
     * @return array
     */
    public static function getNamespaces()
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string(self::$xml_raw);
        if ($xml) {
            return $xml->getNameSpaces(true);
        } else {
            //$errors = libxml_get_errors();
            //libxml_clear_errors();
            return false;
        }
    }

    /**
     * Return only the partial namespace/prefix array of the XML. Call getNamespaces() to see which namespaces there are.
     * Returns an empty array if there's nothing found.
     * @param string $namespace
     * @return array
     */
    public static function getArrayNS($namespace)
    {
        libxml_use_internal_errors(true);
        $result = array();

        $namespaceURIS = self::getNamespaces(self::$xml_raw);
        if (isset($namespaceURIS[$namespace])) {
            $namespaces = self::$xml->getElementsByTagNameNS($namespaceURIS[$namespace], '*');
            if ($namespaces->length > 0) {
                //Todo: foreach is kinda overkill. But EPP isn't that large. (added a break for now)
                foreach ($namespaces as $element) {
                    $elements[] = self::processArray($element);
                    break;
                }
                $result = $elements[0];
            }
            return $result;
        } else {
            return $result;
        }
    }

    /**
     * Return only the tags that match given at $xml_tag_name inside the namespace. Call getNamespaces() to see which namespaces there are.
     * Returns an empty array if there's nothing found.
     * @param string $namespace
     * @param string $xml_tag_name
     * @param boolean $one_result use this is you're already sure you only get 1 key (or want the first one)
     * @return array
     */
    public static function getArrayElement($namespace, $xml_tag_name, $one_result = false)
    {
        libxml_use_internal_errors(true);
        $result = array();

        $namespaceURIS = self::getNamespaces();
        if (isset($namespaceURIS[$namespace])) {
            $namespaces = self::$xml->getElementsByTagNameNS($namespaceURIS[$namespace], $xml_tag_name);
            if ($namespaces->length > 0) {
                foreach ($namespaces as $element) {
                    $result[] = self::processArray($element);
                }
            }
            return $one_result ? $result[0] : $result;
        } else {
            return $result;
        }
    }

    /**
     * Return only the attribute values that match given at $xml_tag_name inside the namespace. Call getNamespaces() to see which namespaces there are.
     * Returns an empty array if there's nothing found.
     * @param string $namespace
     * @param string $xml_tag_name
     * @param string $attribute_name
     * @param boolean $one_result use this is you're already sure you only get 1 key (or want the first one)
     * @return array
     */
    public static function getArrayAttribute($namespace, $xml_tag_name, $attribute_name, $one_result = false)
    {
        $result = array();

        $namespaceURIS = self::getNamespaces();
        if (isset($namespaceURIS[$namespace])) {
            $namespaces = self::$xml->getElementsByTagNameNS($namespaceURIS[$namespace], $xml_tag_name);
            if ($namespaces->length > 0) {
                foreach ($namespaces as $element) {
                    $result[] = $element->attributes -> getNamedItem($attribute_name) -> value;
                }
                return $one_result ? $result[0] : $result;
            }
        } else {
            return $result;
        }
    }

    /**
     * Processes the xml DOM, nodes and namespaces and turns it into an array.
     * Largely based on: http://php.net/manual/en/book.dom.php#93717
     * @param object $root DOMDocument
     * @return array
     */
    private static function processArray($root)
    {
        $result = array();

        if ($root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ($attrs as $attr) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }
        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['_value'] = $child->nodeValue;
                    if (count($result) == 1) {
                        return $result['_value'];
                    } else {
                        return $result;
                    }
                } elseif ($child->nodeType == XML_CDATA_SECTION_NODE) {
                    return $child->nodeValue;
                }
            }
            $groups = array();
            foreach ($children as $child) {
                $childNode = array();
                if (!isset($result[$child->nodeName])) {
                    $childNode = self::processArray($child);
                    //Needs to return atleast 1 result
                    if (count($childNode) >= 1) {
                        $result[$child->nodeName] = $childNode;
                    }
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = array($result[$child->nodeName]);
                        $groups[$child->nodeName] = 1;
                    }
                    $childNode = self::processArray($child);
                    //Needs to return atleast 1 result
                    if (count($childNode) >= 1) {
                        $result[$child->nodeName][] = $childNode;
                    }
                }
            }
        }
        return $result;
    }
}