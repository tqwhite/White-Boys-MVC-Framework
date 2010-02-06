<?php

namespace Entities;

/** @Entity @Table(name="bookmarkLists") */
class BookmarkListBase extends BaseEntity {
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** @Column(type="string", length=255) */
    protected $title;
    
    /** @Column(type="string", length=20) */
    protected $code; //note: the standard field name code refers to an externally useful, unique code, eg, product code
    
   /**
   * @ManyToMany(targetEntity="BookmarkBase")
   * @JoinTable(name="bookmarkLists_bookmarks",
   *      joinColumns={@JoinColumn(name="bookmark_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="bookmarkList_id", referencedColumnName="id")}
   *      )
   */
  protected $bookmarks;
  
//
//end of declarations =============================================
//
	
    public function addBookmark(Bookmark $bookmark) {
        $this->bookmarks[]=$bookmark;
    }

    public function getBookmarks() {
    
        return $this->bookmarks;
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
} //end of class

