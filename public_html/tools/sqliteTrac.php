<?php
include('../../framework/configs/init_environment.php');



class HandleDatabase{

public $dbFilename='/Users/tqwhite/Desktop/trac.db';
public $sourceFileName='/Users/tqwhite/Documents/ERDC/projects/internal tools mgmt/trac/exportOldPlanInfov8.txt';

private $_standardColumnArray=array();
private $_fieldListArray=array();
private $_dbHandle;
private $badQueryArray=array();
private $goodQueryArray=array();
private $auxTableNameArray=array();
private $customFieldsList=array();

private $dryRun;

public $goodSourceArray=array();
public $statusMessages=array();
public $addedComponents=array();

public function __construct($dryRun=false,$wipeDB=false){

	$this->dryRun=$dryRun;
	
	$this->auxTableNameArray['component']=true;
	$this->auxTableNameArray['milestone']=true;
	$this->auxTableNameArray['version']=true;

	$tmp=file_exists($this->dbFilename);
	$this->statusMessages[]="<div style=color:green;>database file_exists({$this->dbFilename})=$tmp</div>";
	
	$tmp=file_exists($this->dbFilename);
	$this->statusMessages[]="<div style=color:green;margin-bottom:10px;>source file_exists($this->sourceFileName)=$tmp</div>";
	
	$this->_dbHandle = new PDO("sqlite:{$this->dbFilename}");
	
	if ($wipeDB and $dryRun==false){
		$result = $this->_dbHandle->query("delete from ticket")->execute();
			$this->statusMessages[]="<div style=color:blue;>Deleted all records from 'ticket'</div>";
		$result = $this->_dbHandle->query("delete from ticket_custom")->execute();
			$this->statusMessages[]="<div style=color:blue;>Deleted all records from 'ticket_custom'</div>";
		foreach ($this->auxTableNameArray as $label=>$data){
			$result = $this->_dbHandle->query("delete from $label")->execute();
			$this->statusMessages[]="<div style=color:blue;>Deleted all records from '$label'</div>";
		}
	}
	elseif ($wipeDB){
		$this->statusMessages[]="<div style=color:blue;>Dry run flag prevented wipeDB from deleting records.</div>";
	}
	
	$rowResult = $this->_dbHandle->query("PRAGMA table_info(ticket)")->fetchAll(PDO::FETCH_ASSOC);
	
	if (is_array($rowResult)){
		foreach ($rowResult as $data){
			$this->_standardColumnArray[$data['name']]=true;
		}
	}
	else{
		echo "<div style=color:red;font-size:18pt;>The database has no standard fields. That means something is very, very wrong and we cannot
			do this. Find a good database and try again.<BR>
			The database file is at: {$this->dbFilename}</div>";
		exit;
		}

}

public function getInputFile($sourceFileName=null){

	if (empty($sourceFileName)){$sourceFileName=$this->sourceFileName;}
	
	$data=file_get_contents($sourceFileName);
	
	$rawSourceArray=explode("\r\n", $data);
	
	$recNum=0;
	foreach ($rawSourceArray as $itemRec){
		if ($recNum==0){
			$this->_fieldListArray=explode("\t", $itemRec);
		}
		else{
			$itemArray=explode("\t", $itemRec);
			if (count($itemArray)==count($this->_fieldListArray)){
				if ($recNum>-1 and $recNum<100000){
	
					$this->goodSourceArray[]=$this->_splitRecords($itemArray);
					}
				}
				else{
				$countRec=count($itemArray); $countStd=count($this->_fieldListArray);
					$this->statusMessages[]="<div style=color:red;margin-bottom:15px;>Input data error: Not enough columns ($countRec s/b $countStd) for row number <span style=color:green;>$recNum</span>. It was not moved to the database:<div style=margin-left:20px;color:black;font-size:10pt;>".dump($itemArray, 'record data', false, false).'</div></div>';
				}
		}
		$recNum++;
	}
	
	array_unshift($this->statusMessages, "<div style=color:blue;>Processing $recNum input records <span style=font-size:80%>(including header and any invalid ones)</span></div>");
}

private function _splitRecords($itemArray){
	$tmpArray=array_combine($this->_fieldListArray, $itemArray);

	$outArray['standardPart']=array();
	$outArray['customPart']=array();
/*	
foreach ($this->_fieldListArray as $label=>$data){
	echo "$label=".urlencode($data)."<BR>";
}
echo "<HR>";	
foreach ($itemArray as $label=>$data){
	echo "$label=".urlencode($data)."<P>";
}
echo "<HR>";	
foreach ($tmpArray as $label=>$data){
	echo "$label=".urlencode($data)."<P>";
}
exit;
*/
	foreach ($tmpArray as $label=>$data){
	
		if ($this->_standardColumnArray[$label]){
			$outArray['standardPart'][$label]=$data;
		}
		else{
			$outArray['customPart'][$label]=$data;
			$this->customFieldsList[$label][$data]=$data;
		}
		
		if ($label=='id' or $label=='ticket'){
			$outArray['customPart']['ticket']=$data;
		}
	
	}
	return $outArray;
}

private function arrayToInsert($tableName, $inArray){

$nameString='';
$valueString='';

foreach ($inArray as $label=>$data){
$data=str_replace("'", '"', $data);
$data=str_replace("<P>", "\r\n\r\n", $data);
$data=str_replace("<BR>", "\r\n", $data);
$data=str_replace("<p>", "\r\n\r\n", $data);
$data=str_replace("<br>", "\r\n", $data);

	$nameString.="'$label', ";
	$valueString.="'$data', ";

}


$nameString=preg_replace('/, $/', '', $nameString);
$valueString=preg_replace('/, $/', '', $valueString);

$queryString="insert into $tableName ($nameString) values ($valueString);";

return $queryString;

}


private function _createArraysForCustom($inArray){
	$ticket=$inArray['ticket'];
	
	$outArray=array();
	
	foreach ($inArray as $label=>$data){
		if ($label!='ticket'){
			$itemArray=array();
			$itemArray['ticket']=$ticket;
			$itemArray['value']=$data;
			$itemArray['name']=$label;
			
			$outArray[]=$itemArray;
			}
	}
	
	return $outArray;
}

private function _createAuxSqlArrays($auxTableNameArray, $ticketNumber, $queryArray){


	$selectionFieldName='name'; //Trac names it the same in all tables
	$descriptionFieldName='description'; //Trac names it the same in all tables

	foreach ($queryArray as $auxTableName=>$auxFieldValue){
		if ($auxTableNameArray[$auxTableName]){
			if (!$this->_checkAuxExists($auxTableName, $selectionFieldName, $auxFieldValue)){
			
				$auxDataArray=array();
				$auxDataArray[$selectionFieldName]=$auxFieldValue;
				$auxDataArray[$descriptionFieldName]=$auxFieldValue;
				
				$queryString=$this->arrayToInsert($auxTableName, $auxDataArray);
				$outArray["$auxTableName:$auxFieldValue"]=$queryString;
				$this->addedAuxilliaries[$auxTableName][$auxFieldValue]=true;
			}
		}
	}
	return $outArray;
}

private function _checkAuxExists($tableName, $selectionFieldName, $value){
	$query="select count(*) from $tableName where $selectionFieldName='$value'";
	$result = $this->_dbHandle->query($query)->fetchAll(PDO::FETCH_ASSOC);
	if ($result[0]['count(*)']>0){
		return true;
	}
	else
	{
		return false;
	}
}
private function _fixTime($inData){
	if (!empty($inData)){
		return strtotime($inData);
	}
	else
	{
		return time();
	}
}
public function saveToDb(){

	$this->queryStringArray=array();
	
	foreach ($this->goodSourceArray as $label=>$data){
	
		$data['standardPart']['time']=$this->_fixTime($data['standardPart']['time']);
		$data['standardPart']['changetime']=$this->_fixTime($data['standardPart']['changetime']);
		
		$itemRecArray=$this->arrayToInsert('ticket', $data['standardPart']);
		$this->queryStringArray[]=$itemRecArray;
				
		$auxSqlArrays=$this->_createAuxSqlArrays($this->auxTableNameArray, $data['standardPart']['id'], $data['standardPart']);

		if ($auxSqlArrays){

			$this->queryStringArray=array_replace($this->queryStringArray, $auxSqlArrays);

		}
		
		if (is_array($data['customPart'])){
			$customStrings=array();
			foreach($this->_createArraysForCustom($data['customPart']) as $data2){
				$customStrings[]=$this->arrayToInsert('ticket_custom', $data2);
			}
			$this->queryStringArray=array_merge($this->queryStringArray, $customStrings);
		}
	}
	
 	if ($this->dryRun==true){ echo "<div style=color:red;font-size:18pt;>not writing db recs</div>";}
	
	$errorCount=0;
	$goodCount=0;
	foreach ($this->queryStringArray as $data){
		
		if ($this->dryRun!=true){
		$result=$this->_dbHandle->prepare($data)->execute(); //write database write here
		}

		if ($result!=true){
			$errorCount++;
			$this->badQueryArray[]=$data;
			$this->statusMessages[]="<div style=font-size:8pt;><span style=color:red;>Error ($result) for query:</span> $data</div>";
		}
		else{
			$this->goodQueryArray[]=$data;
			$goodCount++;
		}

	}
	
		
		if ($errorCount==0){
			$this->statusMessages[]="<div style=color:green;margin-top:10px;>$goodCount queries processed. No errors reported.</div>";
		}
		else{
			$this->statusMessages[]="<div style=color:red;margin-top:10px;>$errorCount queries reported errors.</div>";
		}
		
		//	$result = $this->_dbHandle->query('SELECT * from ticket')->fetchAll(PDO::FETCH_ASSOC);
		//	dump($result);
}

public function displayAuxilliary(){
	if (is_array($this->addedAuxilliaries)){
		foreach ($this->addedAuxilliaries as $label=>$data){
			echo "
				<div style='border:1pt solid gray;margin:5px;padding:5px;'>New <span style=color:green;font-size:125%;font-weight:bold;>$label</span> elements:<P>";
			foreach ($data as $label2=>$data2){
				echo "<div style=margin-left:15pt;>$label2</div>";
			}
		echo "</div>";
		}
	}
	else{
	
		echo "
		<div style='border:1pt solid gray;margin:5px;padding:5px;'>
			No added auxilliary elements.
			</div>
			";
		}
}

public function displayCustomFields($dontCareList){
	if (is_array($this->customFieldsList)){
		foreach ($this->customFieldsList as $label=>$data){
		if ($dontCareList[$label]){break;}
			echo "
				<div style='border:1pt solid gray;margin:5px;padding:5px;'>Custom Field Data <span style=color:green;font-size:125%;font-weight:bold;>$label</span> elements:<P>";
			foreach ($data as $label2=>$data2){
				echo "<div style=margin-left:15pt;>$data2</div>";
			}
		echo "<div style=color:red;margin-top:10px;>maybe should be added to trac.ini</div>";
		echo "</div>";
		}
	}
	else{
	
		echo "
		<div style='border:1pt solid gray;margin:5px;padding:5px;'>
			No added auxilliary elements.
			</div>
			";
		}
}


public function displayStatus(){
	foreach ($this->statusMessages as $data){
		echo "<div>$data</div>";
	}
}


public function displayQueries(){

/* could be helpful sometimes
* 
* $count=count($this->badQueryArray);
* if ($count>0){
* 	echo "<div style='border:1pt solid gray;margin:5px;padding:5px;margin-top:20px;'>
* 		<div style=font-weight:bold;margin:10px;color:red;>$count Bad Queries:</div>";
* 	foreach ($this->badQueryArray as $data){
* 		echo "<div style=font-family:courier;font-size:10pt;margin-bottom:5px;>$data</div>";
* 	}
* 		echo "</div>";
* 	}
* 	else{
* 		echo "<div style=color:green;>No bad queries</div>";
* 		}
*/		
		
$count=count($this->goodQueryArray);
if ($count>0){
	echo "<div style='border:1pt solid gray;margin:5px;padding:5px;margin-top:20px;'>
		<div style=font-weight:bold;margin:10px;color:green;>$count Successful Queries:</div>";
	foreach ($this->goodQueryArray as $data){
		echo "<div style=font-family:courier;font-size:10pt;margin-bottom:5px;>$data</div>";
	}
		echo "</div>";
	}
	else{
		echo "<div style=color:red;>No successful queries</div>";
		}
		
}

}//end of class
echo "<div style=font-family:sans-serif>";

	$dontCareList['timetext']=true;
	$dontCareList['commenttext']=true;
	
	$dryRunFlag=false;
	$wipeDbFlag=true;
	
	$dbHandle=new HandleDatabase($dryRunFlag, $wipeDbFlag);
	$dbHandle->getInputFile();
	$dbHandle->saveToDb();
	$dbHandle->displayCustomFields($dontCareList);
	$dbHandle->displayStatus();
	$dbHandle->displayQueries();

echo "</div>";

//$result = $this->_dbHandle->query("update ticket set description='desc: hello world' where id=3")->execute();

//$result = $this->_dbHandle->query('SELECT * from ticket where id=3')->fetchAll(PDO::FETCH_ASSOC);


