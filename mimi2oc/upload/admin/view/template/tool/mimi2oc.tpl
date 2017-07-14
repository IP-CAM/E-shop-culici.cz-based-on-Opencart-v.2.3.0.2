<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div>
				<h1><?php echo $heading_title; ?></h1>
				<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
				</ul>
			</div>
		</div>
	</div>	

	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="<?php echo $diff_url; ?>" class="btn btn-primary"><i class="fa fa-exchange"></i> Spustit porovnání</a>
			</div>
		</div>
	</div>
</div>

<?php echo $footer; ?>