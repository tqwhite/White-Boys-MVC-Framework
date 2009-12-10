<?php
$outString='';
for ($i=0; $i<10; $i=$i+1){
	$outString.="<option value='$i' >$i</option>";
}
echo "
<form method='post' action='$url'>
	<div>
		<select name='varName'>
			$outString
		</select>
		<input type=submit />
	</div>
</form>
";
?>

<iframe width="600px" height="500px" src="http://m.supersaas.com/schedule/demo/Therapist"></iframe>