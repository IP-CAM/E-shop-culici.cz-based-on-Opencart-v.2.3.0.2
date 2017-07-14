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
		<?php if (isset($diff->error)) { ?>
		
			<div class="panel-body text-danger">
				<?php echo $diff->error; ?>
			</div>
			
			<div class="panel-body">
				<?php echo $log; ?>
			</div>			
			<div class="panel-footer">
				<strong class='text-danger'>Synchronizaci nelze provést.</strong>
			</div>			
			
		<?php } else { ?>
				
			<div class="panel-body">
				<strong>Nové výrobky</strong>
			</div>
			
			<div class="panel-body">
				<strong>Výrobky, které je potřeba skrýt</strong>
			</div>
			
			<div class="panel-body">
				<strong>Výrobky, které je potřeba zobrazit</strong>
			</div>			
				
			<div class="panel-footer">
				<a href="<?php echo $sync_url; ?>" class="btn btn-danger"><i class="fa fa-refresh"></i> Spustit synchronizaci!</a>
			</div>
			
		<?php } ?>	
		</div>
	</div>
</div>

<?php echo $footer; ?>