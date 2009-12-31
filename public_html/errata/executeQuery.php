<?php

include('../../framework/configs/init_environment.php');
/*
$database=new \library\services\qdb\MysqlAccess('phpDB');

$queryString="select count(*) as 'record count' from sessions;";

$result=$database->executeQueryString($queryString, 'recordArray');
dump($result);
*/

class executeQuery{
	private $database;
	public function __construct(){
		$this->database=new \library\services\qdb\MysqlAccess();
	}

	public function model($modelProcess, $parameterArray=''){
		$displayInfo=array('modelProcess'=>$modelProcess);
		switch ($modelProcess){
			case 'getDatabaseList':
				$displayInfo['dbResultArray']=$this->database->executeQueryString('show databases;');
				break;
			case 'processQuery':
				$this->database->setDatabase($parameterArray['dbName']);
				$displayInfo['recordListArray']=$this->database->executeQueryString($parameterArray['queryString']);
				$displayInfo['rowCount']=$this->database->getValue('rowCount');
				break;
		}
		
		return $displayInfo;
	}
	
	public function controller($operation='', $parameterArray=''){
	
		if (empty($operation)){
			if (!empty($_GET['operation'])){
				$operation=$_GET['operation'];
				$parameterArray=$_GET;
			}
			elseif (!empty($_POST['operation'])){
				$operation=$_POST['operation'];
				$parameterArray=$_POST;
			}
		}
		
		switch ($operation){
			default:
			case 'index':
				$displayInfo['dbList']=$this->model('getDatabaseList');
				$outString=$this->view('index', $displayInfo);
				break;
				
			case 'display':
				$displayInfo['dbList']=$this->model('getDatabaseList');
				
				$specialTest=contains('drop', $parameterArray['queryString']);
				$specialTest=($specialTest or contains('delete', $parameterArray['queryString']));
				$specialTest=($specialTest or contains('update', $parameterArray['queryString']));
			
				if (!$specialTest or $parameterArray['safety']=='safe'){
					$displayInfo=array_merge($displayInfo, $this->model('processQuery', $parameterArray));
					$displayInfo['pageTitle']="({$parameterArray['dbName']}) {$parameterArray['queryString']}";
					$outString=$this->view('display', $displayInfo);
				}
				else{
					$displayInfo['needSafety']=true;
					$displayInfo['pageTitle']="Execute Query: Need Safety Confirmation";
					$outString=$this->view('index', $displayInfo);
				}
				
				break;
		}
		
		return $outString;
	}
	
	public function view($viewName, $displayInfo){
		$outString='';	
		//we always need the header
			$optionString=$this->generateOptions($displayInfo['dbList']['dbResultArray'], 'Database', $_GET['dbName']);
			if (!empty($_GET['queryString'])){
				$queryString=$_GET['queryString'];
			}
			else{
				$queryString='show tables';
			}
			$headerString="
				<select name='dbName'>
					<option alue=''>--SELECT DATABASE--</option>
					$optionString
				</select>
				<input type=text name=queryString value='$queryString' style='width:500px;font-size:10pt;color:orange;' />
				<input type=submit />
				<input type=hidden name='operation' value='display' />
			";
			
		switch ($viewName){
			case 'index':
				$titleString="
					<title>{$displayInfo['pageTitle']}</title>
				";
				break;
			case 'display':
				$resultArray=$displayInfo['recordListArray'];
				
				$tmp=$this->showArray($resultArray);
				
				$displayString="
				$tmp<BR>
				";
				$titleString="
					<title>{$displayInfo['pageTitle']}</title>
				";
			
				break;
		}
		
		if ($displayInfo['needSafety']){
			$safetyString="
			<div style=color:red;font-weight:bold;>
			Please confirm this query by checking here: <input type=checkbox name='safety' value='safe' />
			<span style=color:gray;margin-left:5px;font-weight:normal;>(Required for queries containing drop, delete or update.)</span>
			</div>";
		}
		else{
			$safetyString='';
			}
			
		$outString="
		<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01//EN'>
		<html>
		<head>
		  $titleString
		</head>
		
		<body style='font-family:sans-serif;font-size:10pt;'>
		<form method=get action=''>
			$headerString
			$safetyString
			$displayString
		</form>
		</body>
		</html>
		";
		
		return $outString;
	}
	
	private function generateOptions($inArray, $fieldName, $selectedValue=''){
		if (is_array($inArray)){
						$optionString='';
						foreach ($inArray as $label=>$data){
						
							if ($data[$fieldName]==$selectedValue){ $checked='selected';}
							else{$checked='';}
							$optionString.="<option $checked >{$data[$fieldName]}</option>";
						}
					}
					else{
						$optionString='<div style=color:red;>no items available for popup</div>';
					}
		return $optionString;
	}
	
	private function showArray($inArray){
		
					$color1='#ddffdd';
					$color2='#ddddff';
					$bgColor=$color1;
		$count=count($inArray);
		
		if (is_array($inArray)){
			foreach($inArray as $label=>$data){
				$bodyString.="
					<tr style=background:white;font-weight:bold;color:#bbbbbb;><td colspan=3>Record #$label of $count</td></tr>
				";
				foreach ($data as $label2=>$data2){
					if ($bgColor==$color1){$bgColor=$color2;} else {$bgColor=$color1;}
					$bodyString.="
						<tr style=background:$bgColor;><td style=padding-left:35px;background:white;>&nbsp;</td><td style=padding-left:10px;padding-right:10px;>$label2</td><td style=width:100%;padding-left:10px;>$data2</td></tr>
					";
				}
			}
			$blockString="
				<table style=font-size:10pt;font-family:sans-serif;>
					$bodyString
				</table>
			";
		}
		else{
			$blockString="<div style=color:red;>No Records in Dataset</div>";
		}
		return $blockString;
	}

}//end of executeQuery

$page=new executeQuery();
echo $page->controller();