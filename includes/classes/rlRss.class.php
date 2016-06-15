<?php
class rlRss extends reefless
{
    var $items = array('title', 'link', 'description');
    var $items_number = 5;
    var $mXmlParser = null;
    var $mLevel = null;
    var $mTag = null;
    var $mKey = null;
    var $mItem = false;
    var $mRss = array();
    function clear()
    {
        $this->mXmlParser = null;
        $this->mLevel     = null;
        $this->mTag       = null;
        $this->mKey       = null;
        $this->mItem      = false;
        $this->mRss       = array();
    }
    function startElement($parser, $name)
    {
        $this->mLevel++;
        $this->mTag = strtolower($name);
        if ('item' == $this->mTag) {
            $this->mItem = true;
            $this->mKey++;
        }
    }
    function endElement($parser, $name)
    {
        $this->mLevel--;
        if ('item' == $this->mTag) {
            $this->mItem = false;
        }
    }
    function charData($parser, $data)
    {
        if ($this->mKey <= $this->items_number) {
            $data  = trim($data);
            $items = $this->items;
            foreach ($items as $item) {
                if ($item == $this->mTag && $this->mItem) {
                    if (!empty($data)) {
                        $this->mRss[$this->mKey][$item] .= $data;
                    }
                }
            }
        }
    }
    function createParser($content)
    {
        $this->mXmlParser = xml_parser_create();
        xml_set_element_handler($this->mXmlParser, array(
            &$this,
            "startElement"
        ), array(
            &$this,
            "endElement"
        ));
        xml_set_character_data_handler($this->mXmlParser, array(
            &$this,
            "charData"
        ));
        xml_parse($this->mXmlParser, $content);
        xml_parser_free($this->mXmlParser);
    }
    function getRssContent()
    {
        return $this->mRss;
    }
}