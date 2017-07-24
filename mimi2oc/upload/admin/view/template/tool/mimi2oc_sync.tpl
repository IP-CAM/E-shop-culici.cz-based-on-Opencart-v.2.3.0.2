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
		<?php if (count($sync->errors)) { ?>
		
			<div class="panel-body text-danger">
				<?php echo join('<br>', $sync->errors); ?>
			</div>
		<?php } ?>
		
			<div class="panel-body">
				<strong>Průběh synchronizace:</strong>
				<br>
				<hr>
				<?php echo $sync->log; ?>
			</div>		
		
			<div class="panel-footer">
				<a href="<?php echo $diff_url; ?>" class="btn btn-default"><i class="fa fa-exchange"></i> Znovu spustit porovnání!</a>
			</div>
		</div>
	</div>
</div>

<?php echo $footer; ?>