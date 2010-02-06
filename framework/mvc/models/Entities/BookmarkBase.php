<?php

namespace Entities;

/** @Entity @Table(name="bookmarks") */
class BookmarkBase extends BaseEntity {
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** @Column(type="string", length=255) */
    protected $url;
    
    /** @Column(type="string", length=255) */
    protected $anchorText;

//
//end of declarations =============================================
//

    public function getId() {
        return $this->id;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getAnchorText() {
        return $this->anchorText;
    }

    public function setUrl($item) {
        $this->url = $item;
    }

    public function setAnchorText($item) {
        $this->anchorText = $item;
    }

    public function setFromArray($inArray){
		if (is_array($inArray)){
			foreach ($inArray as $label=>$data){
				if (property_exists($this, $label)){
					$this->$label=$data;
				}
			}
		}
    }
}

