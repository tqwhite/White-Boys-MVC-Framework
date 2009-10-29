<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
	if (!function_exists("processSubstitutions")) 
			{
				function processSubstitutions($template, $inputArray, $ctlArray='', $parmArray='', $evalArrays='', $dataArrays='')
					{
						reset($inputArray);
						global $PHP_SELF; //TQ made this global for use in eval code, 7/07
						$recordArray=$inputArray;
						$patternArray=$globalPatternArray; $replaceArray=$globalReplaceArray;
						if (is_array($parmArray)) //this is placed here specifically to prevent the override of inputArray values
							{
								while (list($label, $data)=each($parmArray))
									{
										if (!is_int($label))
											{
												$tag="<!$label!>";
												if (is_string($data))
													{
														$patternArray[$tag]=$tag;
														$replaceArray[$tag]=$data;
														$inx=$inx+1;
														}
												}
										}
								}
						while (list($label, $data)=each($recordArray))
							{
								if (!is_int($label))
									{
										$tag="<!$label!>";
										$patternArray[$tag]=$tag;
										$replaceArray[$tag]=$data;
										$inx=$inx+1;
										}
								}
						//execute eval arrays
							//reset eval arrays
								reset($evalArrays['calc']);
								reset($evalArrays['conv']);
							if ($evalArrays['conv'])
								{
									while (list($label, $data)=each($evalArrays['conv']))
										{
											if (!is_int($label))
												{
													$tag="<!$label!>";
													
													$data=str_replace($patternArray, $replaceArray, $data);
													$pattern=$tag;
													
													if ($ctlArray["debug"]) { echo "<HR>Conv: ".htmlentities($tag)."=".htmlentities($data)."<BR>";}
													
													$replace=eval($data);
													
													$patternArray[$tag]=$pattern;
													$replaceArray[$tag]=$replace;
													
													
													if ($ctlArray["debug"]) { echo "<P>Conv: $tag result=".htmlentities($replaceArray[$tag])."<HR>";}
													$inx=$inx+1;
													}
											}
									}
							if ($evalArrays['calc'])
								{
									while (list($label, $data)=each($evalArrays['calc'])) //init replacement with field values
										{
											if (!is_int($label))
												{
													$tag="<!$label!>";
													$patternArray[$tag]=$tag;
													$data=str_replace($patternArray, $replaceArray, $data);
													if ($ctlArray["debug"]) { echo "Eval: ".htmlentities($tag)."=".htmlentities($data)."<BR>";}
													$replaceArray[$tag]=eval($data);
													$inx=$inx+1;
													}
											}
									}
						reset($patternArray); reset($replaceArray);
						$outString=str_replace($patternArray, $replaceArray, $template);
						return($outString);
						}
				}
	
	
	
	if (!function_exists('replaceArray')) //and this one is new
			{
				function replaceArray($inString, $inArray)
					{
						$i=0;
						while (list($label, $data)=each($inArray))
							{
								$patternArray[$i]="<!$label!>";
								$replaceArray[$i]="$data";
								$i=$i+1;
								}
						$outString=str_replace($patternArray, $replaceArray, $inString);
						return($outString);
						}
				}
				
if (!function_exists('processText')) //and this one is new
	{
		function processText($inString, $autoParagraphs='on', $processOutline='off', $activeURLs='off', $keepLineBreaksFlag='false', $skipURLdecode='true')
			{
				//also defined in showOutline in categoryCtl
				//changed $skipURLdecode default to 'true' 11/2005 TQ, I think I pretty much always want to skip that special processing
				//plus it was causing all plus signs (+) to disappear from all content
				// added this below to compensate $workingString=stripslashes($workingString);
				if (!function_exists('activeURLs2')) //I have to declare these versions because I can't seem to access this class's methods
					{
						//=========NOTE: this is used outside of classLibs, don't add '$this->' refs ==========
function activeURLs2($textString)
	{
		$pattern="^http://([a-zA-Z0-9_./\?=\&\-]*)[ \n\r\t].*";
		$pattern="^http://([a-zA-Z0-9:%$,\+_./\?=\&\-]*)[< \x0D\x0A\n\r\t'>].*";
		
		$inx=1;
		$workingString=$textString;
		$nextTagInx=strpos($workingString, 'http');
		$workingString=substr($workingString, $nextTagInx); //through the end of string
		$workingString.=" -"; //ereg doesn't seem to like finding no space or character after last URL
		while (ereg($pattern, $workingString, $partsArray))
			{
				
				$data=$partsArray[1];
				$garbage='http://'.$data;
				$replaceTag="<!$inx!>";
				
				$replaceArray[$inx]="<A href=http://$data>$data</A>";
				$patternArray[$inx]=$garbage;
				
				$inx=$inx+1;
				$partsArray="";
				$data="";
				
				$workingString=substr($workingString, 3);
				$nextTagInx=strpos($workingString, 'http');
				$workingString=substr($workingString, $nextTagInx); //through the end of string
				
				}
		$textString=str_replace($patternArray, $replaceArray, $textString);
		return($textString);
		}

						// ==================================================START of processOutline
	function processOutline2($inString)
		{
			$cr=chr(13);
			$lineArray=explode($cr, $inString);
			while (list($label, $line)=each($lineArray))
				{
					$length=strlen($line);
					$prefix='';
					for ($i=1; $i<$length, $i=$i+1;)
						{
							$char=substr($line, $i, 1);
							if ($char!=' ')
								{ break; }
							$prefix.='&nbsp;';
							}
						$line=$prefix.trim($line);
						$outString.=$line.$cr;
					}
			$outString=rtrim($outString);
			return ($outString);
			}

						}
				$workingString=$inString;
				if ($skipURLdecode=='false')
					{
						$workingString=stripslashes(urldecode($workingString));
						}
				if ($autoParagraphs=='on')
					{
						$cr=chr(13); $nl=chr(10);
						$patternArray[$inx]="$cr$nl$cr$nl"; $replaceArray[$inx]="<P>"; $inx=$inx+1;
						$patternArray[$inx]="$cr$cr"; $replaceArray[$inx]="<P>"; $inx=$inx+1;
						if ($keepLineBreaksFlag=='keepLineBreaks' or $keepLineBreaksFlag=='true' or $keepLineBreaksFlag=='on')
							{
								$patternArray[$inx]="$cr$nl"; $replaceArray[$inx]="<BR>"; $inx=$inx+1;
								$patternArray[$inx]="$cr"; $replaceArray[$inx]="<BR>"; $inx=$inx+1;
								}
						$workingString=str_replace($patternArray, $replaceArray, $workingString);
						}
				if ($activeURLs=='on')
					{
						$workingString=activeURLs2($workingString);
						}
				if ($processOutline=='on')
					{
						$workingString=processOutline2($workingString);
						}
				/*
				if ($activeURLs=='on')
					{
						$workingString=$this->activeURLs($workingString);
						}
				*/
				$workingString=stripslashes($workingString);
				return($workingString);
				}
		}				
	
	
	if (!function_exists('replaceCompoundTags')) //and this one is new
			{
				function replaceCompoundTags($inString, $dataArray2, $parmArray="noDBtbl", $debug="")
	{
		//this was replacePseudoTags but has legacy functions removed
		//this is called (as a macro) by processTemplateArray in channelInitPages, categoryCtl and channelCtl
		$workingString=$inString;
		$imageLink=$parmArray['imageLink'];
		$pattern="<!([a-zA-Z0-9:()' ;/,\.$=-_]+)!>.*";
		$patternLength=strlen($pattern);
		$stringLength=strlen($workingString);
		$partsArray="";
		$stringIndex=-1;
		do //EXTRACT <!fields!> from message
			{
				$foundFlag=ereg($pattern, $workingString, $itemArray);
				$parameterArray='';
				if ($foundFlag>0 or $foundFlag===0) //create tag list and get values
					{
						
						$item=$itemArray[1];
						if ($item) { $partsArray[$item]="found"; }
						$tagParts=explode(":", $item);
							$tagID=$tagParts[0];
							$tagData=$tagParts[1];
						
						if ($tagData!='') //null tagData means it's not a compound tag, ignore
							{
								$inx=urlencode($item); //creates unique and locatable index
								$valueFound='false';
								switch ($tagID)
									{
										/*case 'macro':
											//getPIXurl2($queryValue, $mode="5", $size="large", $queryField='glossName', $specHeight="", $specWidth="", $altTag="")
											function getPIXurl2($queryValue, $mode="5", $size="large", $queryField='glossName', $specHeight="", $specWidth="", $altTag="")
												{
													global $imageLink;
													return($imageLink->getPIXurl2($queryValue, $mode, $size, $queryField, $specHeight, $specWidth, $altTag));
													}
											$tmp=eval($tagData);
											if ($tmp!='')
												{
													$valuesArray[$inx]=$tmp;
													$valueFound='true';
													}
											break;
											*/
										case 'image':
											$firstSpace=strpos($tagData, " ");
											$glossName=substr($tagData, 0, $firstSpace);
											$tagData=substr($tagData, $firstSpace);
											$tagData=trim($tagData);
											$tagArray=explode(" ", $tagData);
											if (!$glossName) {$glossName=$tagData;}
											
											while (list($label, $data)=each($tagArray))
												{                                                                                                                                                                                                                                                       
													$parmElementArray=explode("=", $data);
													$parameterArray[$parmElementArray[0]]=$parmElementArray[1];
													//height=00
													//width=00
													//size=large or  small
													//scale=0%
													//function=getPIXurl2
													}
											if (!$parameterArray['function']) {$parameterArray['function']='getPIXurl2';}
											if ($parameterArray['function']=='getPIXurl2' or 5==6)
												{
													$fName=$parameterArray['function'];
													$tmp=$imageLink->$fName($glossName, $parameterArray);
													if ($tmp!='')
														{
															
															$valuesArray[$inx]=$tmp;
															$valueFound='true';
															}
													}
												else
													{
														$tmp=" (Function Name in pseudoTag, '{$parameterArray['function']}', not known.) ";
														if ($tmp!='')
															{
																$valuesArray[$inx]=$tmp;
																$valueFound='true';
																}
														}
											break;
										default:
											if ($dataArray2[$tagID][$tagData])
												{
													$tmp=$dataArray2[$tagID][$tagData];
													if ($tmp!='')
														{
															$valuesArray[$inx]=$tmp;
															$valueFound='true';
															}
													}
											break;
										}
								if ($valueFound=='true')
									{
										$stringArray[$inx]="<!$item!>";  //ie, patternArray using later language
										}
								}
						}
				//prepare the remainder of the string
					$tag="<!$item!>";
					$workingString=strchr($workingString, $tag);
					$workingString=substr($workingString, strlen($tag));
					$stringLength=strlen($workingString);
					
					$itemArray="";
					$item="";
				}
			while ($stringLength>0);
		if ($debug=='true') { echo "<P><B>Compound TAG Replacement</B><BR>"; }
		
		if ($debug=='true') { echo "<P>inString=".htmlentities($inString)."<P>"; }
		if ($debug=='true') { while (list($label, $data)=each($stringArray)) {  echo "rreplacing ".htmlentities($stringArray[$label])." with ".htmlentities($valuesArray[$label])." (inx=$label)<BR>"; } }
		
		$outString=str_replace($stringArray, $valuesArray, $inString);
		
		if ($debug=='true') { echo "<P>outString=$outString<P>"; }
		return($outString);
		}

				}
				
				
function processTemplateArray($template, $inputArray, $inCtlArray='', $inParmArray='', $inEvalArrays='', $inDataArrays='')
	{
	//init functions
	
		
		

		
	//grab global elements if available
		if (!$inCtlArray) { global $inCtlArray;}
		if (!$inParmArray) { global $inParmArray;}
		if (!$inEvalArrays) { global $inEvalArrays;}
		if (!$inDataArrays) { global $inDataArrays;}
	//establish default arrays (some default items are not applicable in this single record context)
		//create default$ctlArray
			$ctlArray["itemTemplate"]="<!bodyText!><P>";
			$ctlArray["blockTemplate"]="<!itemText!><!editLink!>";
			$ctlArray["startRec"]="0";
			$ctlArray["count"]="1";
			$ctlArray["orderString"]="seqNum DESC";
			$ctlArray["queryString"]="channelCode='<!channelCode!>' and siteCode='<!siteCode!>'"; //<!channelCode!> and <!siteCode!> come from $parmArray
			$ctlArray["debug"]=""; //if it's defined at all, it triggers SQL messages and other stuff to help figure out what's wrong
			//ctlArray['autoParagraphs'] added 2/05, if present serves as default if not present in data record
			//label $ctlArray items
				$ctlArray["nextLinkLabel"]="next";
				$ctlArray["prevLinkLabel"]="prev";
				$ctlArray["moreLinkLabel"]="more";
				$ctlArray["permaLinkLabel"]="#";
				$ctlArray["viewItemLabel"]="view";
			
		//create default $parmArray
			global $siteCode;
			$parmArray["siteCode"]='siteCode not defined by global or parmArray';
			$parmArray["userID"]="";
			global $imageLink;
			$parmArray["imageLink"]=$imageLink;
			$parmArray["previewPage"]=$PHP_SELF; //the page we return to after manageChannel entry
			$parmArray["editPage"]=$PHP_SELF; //manageChannel.php
			$parmArray["infoPage"]=$PHP_SELF; //the place to go to view a permalink
			$parmarray['editPermission']='false'; //=='true' turns on the default eval for <!editLink!>
		//create default $evalArrays (blockCalc, itemCalc, conv)
			
			//$evalArrays['conv']["lastUseTime"]='return(date("m/d/y h:i a", "<!lastUseTime!>"));'; //redefine <!lastUseTime!>
			//$evalArrays['conv']["creationTime"]='return(date("m/d/y h:i a", "<!creationTime!>"));'; //redefine <!creationTime!>
			//$evalArrays['conv']["bodyText"]='return(processText("<!bodyText!>", "<!autoParagraphs!>", "<!processOutLine!>", "<!activeURLs!>"));';
			//$evalArrays['conv']["headline"]='return(processText("<!headline!>", "<!autoParagraphs!>", "<!processOutLine!>", "<!activeURLs!>"));';
			$evalArrays['conv']["bodyText"]='return(stripslashes(processText($recordArray["bodyText"], "<!autoParagraphs!>", "<!processOutLine!>", "<!activeURLs!>", "<!keepLineBreaksFlag!>")));';
			$evalArrays['conv']["headline"]='return(stripslashes(processText($recordArray["headline"], "<!autoParagraphs!>", "<!processOutLine!>", "<!activeURLs!>", "<!keepLineBreaksFlag!>")));';
			
			$evalArrays['calc']['slashedBodyText']='return($recordArray["bodyText"]);';
			$evalArrays['calc']['slashedHeadline']='return($recordArray["headline"]);';
			$evalArrays['calc']['permaLink']='return("<A href=<!infoPage!>?permaLink=<!itemID!>><!permaLinkLabel!></A>");';
			$evalArrays['calc']['itemEditLink']='if ("<!editPermission!>"=="true") {return(" (<A href=<!editPage!>?operation=editChannel&itemID=<!itemID!>&channelCode=<!channelCode!>&siteCode=<!siteCode!>&previewPage=<!previewPage!>><SPAN class=editLink>e</SPAN></A>/<A href=<!editPage!>?operation=newChannel&channelCode=<!channelCode!>&siteCode=<!siteCode!>&previewPage=<!previewPage!>><SPAN class=editLink>n</SPAN></A>)");} else{return("");}';
			
			// revised for the following 10/2004 $evalArrays['blockCalc']['moreLink']='return("<A href=<!infoPage!>?operation=viewMore&channelCode=<!channelCode!>&startRec=<!nextRecNo!>><!moreLinkLabel!></A>");'; //create new <!moreLink!>
			$inEvalArrays['blockCalc']['moreLink']='return("<A href=<!infoPage!>?operation=viewMore&channelCode=<!channelCode!>&siteCode=<!siteCode!>&startRec=<!nextRecNo!>><!moreLinkLabel!></A>");'; //create new <!moreLink!>
			$evalArrays['blockCalc']['editLink']='if ("<!editPermission!>"=="true") {return(" (<A href=<!editPage!>?operation=editChannel&itemID=<!lastItemID!>&channelCode=<!channelCode!>&siteCode=<!siteCode!>&previewPage=<!previewPage!>><SPAN class=editLink>e</SPAN></A>/<A href=<!editPage!>?operation=newChannel&channelCode=<!channelCode!>&siteCode=<!siteCode!>&previewPage=<!previewPage!>><SPAN class=editLink>n</SPAN></A>)");} else{return("");}';
			//$evalArrays['blockCalc']['editLink']='return(" (<A href=<!editPage!>?operation=editChannel&itemID=<!lastItemID!>&channelCode=<!channelCode!>&siteCode=<!siteCode!>&previewPage=<!previewPage!> class=editLink>e</A>/<A href=<!editPage!>?operation=newChannel&channelCode=<!channelCode!>&siteCode=<!siteCode!>&previewPage=<!previewPage!> class=editLink>n</A>)");';
			//blockCalc only refers to last record's field values and are calculated last
			
		//create default $dataArrays
			$dataArrays['glossary']['codeCopyright']="Copyright TQ White II 2003 - ".date("Y");
	//update arrays with incoming (user) parameters
		if ($inCtlArray) { reset($inCtlArray); while (list($label, $data)=each($inCtlArray)) { $ctlArray[$label]=$data; } } //add/replace incoming parameters
		if ($inParmArray) { reset($inParmArray); while (list($label, $data)=each($inParmArray)) { $parmArray[$label]=$data; } } //add/replace incoming parameters
		if ($channelCode) { $parmArray["channelCode"]=$channelCode; }//the parameter in the function call is for convenience, the routine operates on arrays
		if ($inEvalArrays)
			{
				reset($inEvalArrays);
				while (list($glossName, $array)=each($inEvalArrays))
					{
						if (is_array($array))
							{
								while (list($label, $data)=each($array))
									{
										$evalArrays[$glossName][$label]=$data;
										}
								}
						}
				}
		if (is_array($inDataArrays))
			{
				reset($inDataArrays);
				while (list($glossName, $array)=each($inDataArrays))
					{
						if (is_array($array))
							{
								while (list($label, $data)=each($array))
									{
										$dataArrays[$glossName][$label]=$data;
										}
								}
						}
				}
	//********************************************
	//<B>this usually  is called from channelInitPages</B>
	//********************************************
	
	if (!is_array($inputArray))
		{
			$outString="<DIV>processTemplateArray() says: input is not a record array</DIV>";
			}
		elseif (is_array(pos($inputArray))) //ie, array of arrays from getRecordSet
			{
				reset($inputArray);
				$recInx=1;
				//$dataArrays['prevRec'] could be initialized from calling prog
				while (list($label, $recordArray)=each($inputArray))
					{
						$recordArray['recInx']=$recInx; $recInx++;
						$tmp=processSubstitutions($template, $recordArray, $ctlArray, $parmArray, $evalArrays, $dataArrays);
						if ($ctlArray["debug"]=='true') { echo "<B>simple tag replacement Result</B><P>processString=".htmlentities($tmp)."<P>"; }
						$tmp=replaceCompoundTags($tmp, $dataArrays, $parmArray, $ctlArray['debug']);
						$outString.=$tmp;
						$dataArrays['prevRec']=$recordArray;
						}
						
				}
			else //conventional, array of scalars
				{
					$recordArray=$inputArray;
					if (!$recInx)
						{
							$recInx=1;
							}
						else
							{
								$recInx=$recInx+1;
								}
					$recordArray['recInx']=$recInx;
					$tmp=processSubstitutions($template, $recordArray, $ctlArray, $parmArray, $evalArrays, $dataArrays);
					if ($ctlArray["debug"]=='true') { echo "<B>simple tag replacement Result</B><P>processString=".htmlentities($tmp)."<P>"; }
					$tmp=replaceCompoundTags($tmp, $dataArrays, $parmArray, $ctlArray['debug']);
					$outString.=$tmp;
					}
		return($outString);
	}
