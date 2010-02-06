<?php
namespace mvc\models;

class BookmarkList extends \mvc\models\BaseModel{

	private $bookmarkList;
	private $listName;

public function __construct(){
	parent::__construct();

}


/**
* getList looks up the bookmark list specified by listName
* and returns the list
* 
* @param listName
* @return array of (Bookmark)
* @author TQ White II
*
*/
public function getList($listName){
		$tmp=new \Entities\BookmarkListBase(); //not sure if this is giving the line below something to refer to or working around a bug, but it doesn't work if it's not here
		$this->bookmarkList = $this->entityManagerInstance->getRepository('\Entities\BookmarkListBase')->findBy(array('code'=>$listName));
		$this->listName=$listName;
		return ($this->bookmarkList[0]->getBookmarks());
}

/**
* getArray converts a bookmarkList that has already been initialized into an associative array.
* This is mainly useful for JSON transmission to external pages.
* 
* @param none
* @return array of bookmarkList public and protected properties
* @author TQ White II
*
*/
public function getArray($getSubbordinates=true){
		$bookmarkListArray=array();
		
		if (count($this->bookmarkList)>1){
			foreach($this->bookmarkList as $label=>$data){
				$bookmarkListArray[]=$data->getArray($getSubbordinates);
			}
		}
		else{
			$bookmarkListArray[]=$this->bookmarkList[0]->getArray($getSubbordinates);
		}
		
		return $bookmarkListArray;

		}
		

public function getListHeaders(){
		$tmp=new \Entities\BookmarkListBase(); //not sure if this is giving the line below something to refer to or working around a bug, but it doesn't work if it's not here
		$this->bookmarkList = $this->entityManagerInstance->getRepository('\Entities\BookmarkListBase')->findAll();
	}

public function addBookmark($dataArray){

		$bookmark = new \Entities\BookmarkBase;
							
		$bookmark->setUrl($dataArray['url']);
		$bookmark->setAnchorText($dataArray['anchorText']);
		
		$this->entityManagerInstance->persist($bookmark);
		
	//	$bookmark->getBookmarkList()->add($bookmarkList);
		$this->bookmarkList[0]->addBookmark($bookmark);

		
		$this->entityManagerInstance->persist($this->bookmarkList[0]);
		$this->entityManagerInstance->flush();

}

} //end of class