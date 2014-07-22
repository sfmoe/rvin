<html>
<head>
	<title>Is there a Ravinia show today?</title>
	<style type="text/css">
	.yes {font-size: 3em; color: tomato;}
	.showlist .show-item {
		width: 100%;
		max-width : 500px;
		margin: 0 auto;
		border: 1px solid #c0c0c0;
		margin-bottom: 5px;
	}

	.showlist ul{
		list-style: none;
		padding: 0;
		margin: 0;
	}
	strong {
		color: tomato;
	}
	</style>
</head>
<body>

<div style="text-align: center;">Today is <?php echo date("l, M d, Y"); ?>
<h1>Is there a Ravinia Show today?</h1>

<?php
require_once './rav.php';
$rv = new Rav;
if($rv->isthere == true){
	echo "<div class='yes'>YES!</div>";
?>
<div class="showlist">
<?php
foreach ($rv->theshows as $shw) {
	?>
	<div class="show-item">
		<h2><?php echo $shw->Title; ?></h2>
		<ul>
			<li>Location: <?php echo $shw->ShowVenue; ?></li>
			<li>Gates Open: <?php echo $shw->GatesOpen; ?></li>
			<li><strong>Start Time</strong>: <?php echo $shw->ShowStart; ?></li>
		
		</ul>
	</div>
	<?php
}
?>
</div>
<?php
}else{
		echo "<div style=\"font-size: 3em; color: tomato;\">NO :(</div>";
}
?>
</div>
</body>
</html>