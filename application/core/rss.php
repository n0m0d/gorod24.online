<?php
class RSS extends DOMDocument {
	private $channel;
	
	public function __construct($title, $link, $description) {
		parent::__construct('1.0', 'utf-8');
		$this->formatOutput = true;
		$root = $this->appendChild($this->createElement('rss'));
		$root->setAttribute('version', '2.0');
		$channel = $root->appendChild($this->createElement('channel'));
		$channel->appendChild($this->createElement('title', $title));
		$channel->appendChild($this->createElement('link', $link));
		$channel->appendChild($this->createElement('description', $description));
		$this->channel = $channel;
	}
	
	public function addItem($title, $link, $description, $aOptCategory = null, DateTime $aOptPubDate = null) {
		$item = $this->createElement('item');
		$item->appendChild($this->createElement('title', $title));
		$item->appendChild($this->createElement('link', $link));
		$item->appendChild($this->createElement('guid', $link));
		
		if (!is_null($aOptCategory) && strlen(trim($aOptCategory)) > 0) {
		$category = trim($aOptCategory);
		$categoryElement = $this->createElement('category');
		$categoryElement->appendChild($this->createCDATASection($category));
		$item->appendChild($categoryElement);
		}
		
		if (!is_null($aOptPubDate) && ($aOptPubDate instanceof \DateTime)) {
		$item->appendChild($this->createElement('pubDate', $aOptPubDate->format(\DateTime::RFC2822)));
		}
		
		$descriptionElement = $this->createElement('description');
		$descriptionElement->appendChild($this->createCDATASection($description));
		$item->appendChild($descriptionElement);
		$this->channel->appendChild($item);
		return $this;
	}
}
?>