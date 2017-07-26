<?php
class ModelToolMimi2oc extends Model 
{
	private $_mimiUrl = "https://www.mimibazar.cz/";
	private $_productUrl = "https://www.mimibazar.cz/inzerat/";
	private $_startUrl = "bazar.php?user=32370";
	
	private $data;
	private $_log = array();
	
	private $categories;
	private $manufacturers;
	
	function __construct($registry)
	{
		parent::__construct($registry);
		
		$this->load->model('catalog/category');
		$this->categories = $this->model_catalog_category->getCategories();
		$keys = array_keys($this->categories);
		foreach ($keys as $key)
			$this->categories[$key]['lower_name'] = strtolower($this->categories[$key]['name']);

		$this->load->model('catalog/manufacturer');
		$this->manufacturers = $this->model_catalog_manufacturer->getManufacturers();
		$keys = array_keys($this->manufacturers);
		foreach ($keys as $key)
			$this->manufacturers[$key]['lower_name'] = strtolower($this->manufacturers[$key]['name']);		
	}
	
	private function log($text, $indent = 0)
	{
		$indent = str_repeat('&nbsp;', $indent);
		$this->_log[] = $indent.str_replace('<br>', "<br>$indent", $text);
	}
	
	public function log_as_string()
	{
		return join('<br>', $this->_log);
	}	
	
	/**
	 * Fce stáhne požadovanou html stránku, vytvoří a vrátí její DOM. 
	 * @param unknown $url
	 * @return DOMDocument
	 */
	private function _get_file($url)
	{
		$this->log('get_file: '.$url);
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = FALSE;
		@$dom->loadHTMLFile($url);
		return $dom;
	}
	
	private function _extractID($p)
	{
		//$p vypada takto nejak: "https://www.mimibazar.cz/inzerat/54634116-0/detske-bavlnene-tricko"
		$path = $p;
		$p = substr($p, strlen($this->_productUrl));
	
		$i = strpos($p, '/');
		if ($i !== FALSE)
			$p = substr($p, 0, $i);
	
		$i = strpos($p, '-');
		if ($i !== FALSE)
			$p = substr($p, 0, $i);
	
		return $p;
	}
	
	/**
	 * Fce vrati kategorii, odpovidajici zadanemu nazvu mimibazar alba 
	 * @param unknown $name
	 */
	private function _mapMimibazarCategory($name)
	{
		$lower_name = strtolower($name);
		
		if ($lower_name == '(neuveden)')
			$lower_name = '(neuveden)';
		if (strpos($lower_name, 'dřevěné') !== FALSE)
			$lower_name = 'dřevěné hračky';
		else if (strpos($lower_name, 'plastové') !== FALSE)
			$lower_name = 'plastové hračky';
		else if (strpos($lower_name, 'kreativní') !== FALSE)
			$lower_name = 'kreativní hračky';
		else if (strpos($lower_name, 'hry') !== FALSE)
			$lower_name = 'stolní hry';
		else if (strpos($lower_name, 'stavebnice') !== FALSE)
			$lower_name = 'stavebnice';
		
		else if (strpos($lower_name, 'punčocháče') !== FALSE)
			$lower_name = 'punčocháče, legíny';		
		else if (strpos($lower_name, 'dámské') !== FALSE)
			$lower_name = 'dámské prádlo';
		else if (strpos($lower_name, 'doplňky') !== FALSE)
			$lower_name = 'doplňky oblečení';

		else
		{
			if (!isset($this->data->default_mapped_categories[$lower_name]))
				$this->data->default_mapped_categories[$lower_name] = $name;
			$lower_name = 'dětské oblečení';
		}
			 
		foreach ($this->categories as $category)
			if (strpos($category['lower_name'], $lower_name) !== FALSE)
				return $category;

		$s = 'Chybí kategorie: '.$lower_name;
		if (!isset($this->data->errors[$s]))
			$this->data->errors[$s] = $s;
	}
	
	/**
	 * Fce vrati vyrobce, odpovidajici zadanemu mimibazarovemu nazvu
	 * @param unknown $name
	 */
	private function _mapMimibazarManufacturer($name)
	{
		$lower_name = strtolower($name);
		foreach ($this->manufacturers as $manufacturer)
			if ($manufacturer['lower_name'] == $lower_name)
				return $manufacturer;
			
		foreach ($this->data->new_manufacturers as $manufacturer)
			if ($manufacturer['lower_name'] == $lower_name)
				return $manufacturer;	
			
		//novy vyrobce			
		$manufacturer = array('manufacturer_id' => 0, 'name' => $name, 'lower_name' => $lower_name);
		$this->data->new_manufacturers[] = $manufacturer;

		return $manufacturer;
	}	
	
	/**
	 * Stahne vsechny informace o tomto vyrobku ze stranek mimibazaru.
	 *
	 * @param unknown $id
	 * @param stdClass $album
	 * @return void
	 */
	private function _handleMimiProduct($id, $album)
	{
		$doc = $this->_get_file($this->_productUrl.$id);
	
		$product = new stdClass();
		$product->name = '???';
		$product->description = '';
		$product->manufacturer = $this->_mapMimibazarManufacturer('(neuveden)');
		$product->photos = array();
	
		$product->variants = array(new stdClass());
		$variant = $product->variants[0];
		$variant->mimiId = $id;
		$variant->price = '';
		$variant->size = '';
		$variant->proportions = '';
	
		$container = $doc->getElementById('trup');
		if (!$container)
			return;
	
		$items = $container->getElementsByTagName('*');
		foreach ($items as $item)
		{
			$itemprop = $item->getAttribute('itemprop');
	
			//info pro produkt
			if ($item->tagName == 'span' && $itemprop == 'name')
				$product->name = $item->textContent;
			else if ($item->tagName == 'div' && $itemprop == 'description')
				$product->description = $item->textContent;
			else if ($item->tagName == 'span' && $itemprop == 'brand')
				$product->manufacturer = $this->_mapMimibazarManufacturer($item->textContent);
				
			//info pro tuto konkretni variantu
			else if ($item->tagName == 'span' && $itemprop == 'price')
				$variant->price = (int)$item->textContent;
			else if ($item->tagName == 'div' && strpos($item->textContent, 'VELIKOST: ') === 0)
				$variant->size = substr($item->textContent, strlen('VELIKOST: '));
			else if ($item->tagName == 'div' && strpos($item->textContent, 'ROZMĚRY: ') === 0)
				$variant->proportions = substr($item->textContent, strlen('ROZMĚRY: '));
				
			//fotky
			else if ($item->tagName == 'a' && $item->getAttribute('class') == 'cb-foto-g2')
				$product->photos[] = $item->getAttribute('href');
		}
	
		//bud se jedna o novy produkt, nebo o variantu jiz existujiciho
		$uid = md5($product->name.'#'.$product->description.'#'.$product->manufacturer['name']);
		if (isset($album->products[$uid]))
		{
			//varianta existujiciho vyrobku
			$album->products[$uid]->variants[] = $variant;
			//pridam pripadne nove fotky
			foreach ($product->photos as $photo)
				if (!in_array($photo, $album->products[$uid]->photos))
					$album->products[$uid]->photos[] = $photo;
		}
		else
		{
			//novy produkt
			$album->products[$uid] = $product;
		}
	}	
	
	/**
	 * Najdu vsechny produkty na zadane strance alba.
	 * @param unknown $page
	 * @param unknown $album
	 */	
	private function _handleAlbumPage($page, $album)
	{
		//najdu vsechny produkty
		//protoze na jeden produkt existuji duplicitni odkazy, 
		//tak si eviduji, ktere produkty uz jsem zpracoval
		$handledProducts = array();
		
		foreach ($page->getElementsByTagName('a') as $a)
		{
			$href = $a->getAttribute('href');
			if (strpos($href, $this->_productUrl) === 0)
			{
				$id = $this->_extractID($href);
				if (!in_array($id, $handledProducts))
				{
					$handledProducts[] = $id;
					$this->_handleMimiProduct($id, $album);
				}
			}
		}
	}
	
	/**
	 * Fce stahne seznam alb a id jejich vyrobku ze stranky mimibazaru.
	 */
	private function _getMimibazarData()
	{
		//vytvorim seznam alb
		$this->log('<strong>Hledani alb</strong>');
		$doc = $this->_get_file($this->_mimiUrl.$this->_startUrl);
				
		foreach ($doc->getElementsByTagName('a') as $a)
		{
			$href = $a->getAttribute('href');
			$name = $a->textContent;
			if ($href != '' && strpos($href, 'bazar.php?album=') !== FALSE &&
					strpos($name, 'Soukro') !== 0 && $name != 'Zdarma' && $name != 'CELÉ FOTOALBUM')
			{
				$album = new stdClass();
				$album->href = $href;
				$album->name = $name;
				$album->category = $this->_mapMimibazarCategory($name);
				$album->products = array();
				$this->data->albums[] = $album;
				
				$h = '<a href="'.$album->href.'" target="_blank">'.$album->name.'</a>'; 
				$this->log('Album: '.$h, 2);
				$this->log('Kategorie: '.$album->category['name'], 4);
			}
		}
		$this->log('');
		
		//projdu alba, najdu produkty
		foreach ($this->data->albums as $album)
		//$album = $this->data->albums[8];
		{
			$this->log('Zpracovani alba: <strong>'.$album->name.'</strong>');
			
			//najdu odkazy na vsechny strany alba
			//pokud ma album vice stranek, uvodni (prvni) stranka obsahuje i odkaz sama na sebe.
			$doc = $this->_get_file($album->href);
			$numPages = 0;
			
			foreach ($doc->getElementsByTagName('a') as $a)
			{
				$href = str_replace('&amp;', '&', $a->getAttribute('href'));
				if (strpos($href, '?strana=') === 0)
				{
					$numPages++;
					$page = $this->_get_file($this->_mimiUrl.'bazar.php'.$href);
					$this->_handleAlbumPage($page, $album);
				}
			}
			
			if ($numPages == 0)
			{
				//album ma jen tuto jednu hlavni stranu
				$this->_handleAlbumPage($doc, $album);
			}
			
			//do logu vypisu nalezene produkty
			$this->log('Produkty', 2);
			foreach ($album->products as $product)
			{
				$this->log($product->name, 4);
				$this->log('<small>popis: '.$product->description.'</small>', 4);
				$this->log('<small>výrobce: '.$product->manufacturer['name'].'</small>', 4);
				$this->log('<small>počet variant: '.count($product->variants).'</small>', 4);
					
				foreach ($product->variants as $variant)
				{
					$h = '<a href="'.$this->_productUrl.$variant->mimiId.'" target="_blank">'.$variant->mimiId.'</a>';
					$this->log('<small>mimi ID: '.$h
							.', cena: '.$variant->price
							.', velikost: '.$variant->size
							.', rozměry: '.$variant->proportions
							.'</small>', 8);
				}
			}
			$this->log('');			
		}	
	}
	

	
	/**
	 * Fce prida nove vyrobce 
	 * @param unknown $data
	 */
	private function _syncManufacturers($diff)
	{
		if (isset($diff->new_manufacturers) && count($diff->new_manufacturers))
		{
			foreach ($diff->new_manufacturers as $m)
			{
				$data = array(
						'name' => $m['name'],
						'sort_order' => 0,
						'manufacturer_store' => array(0), 
						'keyword' => $m['name'],
				);
				
				$id = $this->model_catalog_manufacturer->addManufacturer($data);
				$this->log('Přidán výrobce: '.$id.' - '.$m['name']);
				//TODO doplnit id vyrobce k vyrobkum 
			}
		}
	}
	
	/**
	 * Porovnani s mimibazarem
	 * @return stdClass
	 */
	public function diff() 
	{
		$this->data = new stdClass();
		$this->data->errors = array('todo' => 'diff - TODO');
		$this->data->albums = array();
		$this->data->new_manufacturers = array();
		$this->data->default_mapped_categories = array();
		
		$this->_getMimibazarData();
		
		$this->data->log = $this->log_as_string();
		return $this->data;
	}
	
	/**
	 * Provedeni synchronizace s mimibazarem.
	 * @param unknown $diff
	 * @return stdClass
	 */
	public function sync($diff)
	{
		$this->data = new stdClass();
		$this->data->errors = array('todo' => 'sync - TODO');
		
		if (!$diff)
		{
			$this->data->errors[] = 'Chybí výsledek porovnání.';
		}
		else 
		{
			//vyrobci
			$this->_syncManufacturers($diff);

		}

		$this->data->log = $this->log_as_string();
		return $this->data;
	}
	
}
