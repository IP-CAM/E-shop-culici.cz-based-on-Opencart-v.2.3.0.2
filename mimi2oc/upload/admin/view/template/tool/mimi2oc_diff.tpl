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
		<?php if (count($diff->errors)) { ?>
		
			<div class="panel-body text-danger">
				<?php echo join('<br>', $diff->errors); ?>
			</div>
		<?php } ?>
		
			<div class="panel-body">
				<strong>Noví výrobci</strong>
				<br>
				<?php 
				if (count($diff->new_manufacturers))
					foreach ($diff->new_manufacturers as $m)
						echo $m['name'].'<br>';
				else 
					echo '(žádný nový výrobce)';
				?>
			</div>		
				
			<div class="panel-body">
				<strong>Nové výrobky</strong>
				<br>
			</div>
			
			<div class="panel-body">
				<strong>Výrobky, které je potřeba skrýt</strong>
				<br>
			</div>
			
			<div class="panel-body">
				<strong>Výrobky, které je potřeba zobrazit</strong>
				<br>
			</div>
			
			<div class="panel-footer">
				<a href="<?php echo $sync_url; ?>" class="btn btn-danger"><i class="fa fa-refresh"></i> Spustit synchronizaci!</a>
			</div>
		</div>
		
		<div class="panel panel-info">
			<div class="panel-body">
				<strong>LOG:</strong>
				<br>
				<hr>
				<?php echo $diff->log; ?>
			</div>
		</div>		
	</div>
</div>

<?php echo $footer; ?>