<?php
namespace Entities;

/** 
 * Base Entity
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class BaseEntity extends \mvc\BaseClass{

/**
* getArray gets all protected and public properties and bundles them into an associative 
* array. This is done mainly to allow object properties to be exported via JSON
*
* @param none, expects the entity to have been initialized with stuff to include in the array
* @return none
* @author TQ White II
*
*/
	public function getArray($getSubbordinates=true){
		$varList=get_object_vars($this);
		$outArray=array();
		foreach($varList as $label=>$data){
			if (is_object($data) or is_array($data)){
				if ($getSubbordinates===true){
					foreach ($data as $label2=>$data2){
						if (method_exists($data2, 'getArray')){
							$outArray[$label][]=$data2->getArray($getSubbordinates);
						}
						else{
							$outArray[$label][]=$data2;
						}
					}
				}
			}
			else{
				$outArray[$label]=$data;
			}
		}
		return($outArray);
	}
	
} //end of class