<?php

class ExtendedSimpleXMLElement extends \SimpleXMLElement
{
    /**
     * Add the ability to write CDATA to XML elements
     * 
     * @param String $cdataText
     * @return DOMNode
     */
    public function addCDATA($cdataText)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdataText));

        return $node;
    }

    /**
     * Get the names of the child nodes as an array
     * 
     * @return Array $children
     */
    public function getChildNames()
    {
        $children = [];

        foreach ($this->children() as $tag => $child)
        {
            $children[] = $tag;
        }

        return $children;
    }

    /**
     * Get the value of a node
     * 
     * @return mixed $value
     */
    public function getNodeValue()
    {
        $node = dom_import_simplexml($this);

        return trim($node->nodeValue);
    }

    /**
     * Add a child with attributes.
     * 
     * @param String $element
     * @param Array $attributes
     * @return void
     */
    public function addChildWithAttributes($element, $value, $attributes)
    {
        $elem = ($value !== null ? $this->addChild($element, $value) : $this->addChild($element));
        $elem->addAttributes($attributes);

        return $elem;
    }

    /**
     * Add multiple attributes to a child.
     * 
     * @param Array $attributes
     * @return void
     */
    public function addAttributes($attributes)
    {
        if(in_array(0, array_keys($attributes)))
        {
            if(gettype($attributes[0]) != 'array')
            {
                $attributes = [$attributes];
            }
        }

        foreach ($attributes as $attribute)
        {
            if(count($attribute) == 2)
            {
                $this->addAttribute($attribute[0], $attribute[1]);
            }
            else
            {
                /**
                 * The value at index 2 should be a schema URL. The only reason that is needed is when 
                 * and attribute with a prefix, such as xml:lang, is being applied. 
                 * 
                 * SimpleXMLElement base class will strip away the prefix unless a schema URL is provided. No clue why.
                 * 
                 * The schema URL can be any value, even www.google.com, which is stupid, but neccessary unless we plan on 
                 * overriding the addAttribute method
                 * 
                 * (╯°□°）╯︵ ┻━┻,
                 * 
                 */
                $this->addAttribute($attribute[0], $attribute[1], $attribute[2]);
            }
        }
    }

    /**
     * Save the XML object to a formatted file.
     * 
     * @param String $filepath
     * @return Boolean
     */
    public function saveXMLDocument($filepath)
    {
        $file = fopen($filepath, "w");
        fwrite($file, $this->asXML());
        fclose($file);
        
        $simpleXMLObj = simplexml_load_file($filepath);

        $domXML = new \DomDocument('1.0');
        $domXML->preserveWhiteSpace = false;
        $domXML->formatOutput = true;
        $domXML->loadXML($simpleXMLObj->asXML());

        $domXML->save($filepath);
    }
}
