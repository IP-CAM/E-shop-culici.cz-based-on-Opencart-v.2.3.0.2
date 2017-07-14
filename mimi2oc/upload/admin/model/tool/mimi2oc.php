<?php
class ModelToolMimi2oc extends Model 
{
	private $_mimiUrl = "https://www.mimibazar.cz/";
	private $_productUrl = "https://www.mimibazar.cz/inzerat/";
	private $_startUrl = "bazar.php?user=32370";
	
	static private $_log = array();
	
	private function log($text, $indent = 0)
	{
		ModelToolMimi2oc::$_log[] = str_repeat('&nbsp;', $indent).$text;
	}
	
	public function log_as_string()
	{
		return join('<br>', ModelToolMimi2oc::$_log);
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
	 * Fce stahne seznam alb a id jejich vyrobku ze stranky mimibazaru.
	 * @param unknown $result
	 */
	private function _getMimibazarData($result)
	{
		//vytvorim seznam alb
		$this->log('<strong>Hledani alb</strong>');
		$doc = $this->_get_file($this->_mimiUrl.$this->_startUrl);
				
		$result->albums = array();
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
				$album->pages = array();
				$album->productIds = array();
				$result->albums[] = $album;
				$this->log('Album: '.$name.' ('.$href.')', 2);
			}
		}
		$this->log('');
		
		//projdu alba
		foreach ($result->albums as $album)
		{
			$this->log('Zpracovani alba: <strong>'.$album->name.'</strong>');
			
			$this->log('Stranky', 2);
			//najdu odkazy na vsechny strany alba
			//pokud ma album vice stranek, uvodni (prvni) stranka obsahuje i odkaz sama na sebe.
			$doc = $this->_get_file($album->href);
			
			foreach ($doc->getElementsByTagName('a') as $a)
			{
				$href = str_replace('&amp;', '&', $a->getAttribute('href'));
				if (strpos($href, '?strana=') === 0 && !isset($album->pages[$href]))
				{
					$album->pages[$href] = $href;
					$this->log($href, 4);
				}
			}
			
			if (count($album->pages) == 0)
			{
				//pouze jedna strana
				$album->pages[$album->href] = $album->href;
				$this->log($album->href, 4);
			}
			$this->log('');
		
			//najdu vsechny produkty
			$this->log('Produkty', 2);
			foreach ($album->pages as $page)
			{
				if ($page[0] == '?')
					$doc = $this->_get_file($this->_mimiUrl.'bazar.php'.$page);
				else
					$doc = $this->_get_file($page);
				
				foreach ($doc->getElementsByTagName('a') as $a)
				{
					$href = $a->getAttribute('href');
					if (strpos($href, $this->_productUrl) === 0)
					{
						$id = $this->_extractID($href);
						if (!isset($album->productIds[$id]))
						{
							$this->log($id, 4);
							$album->productIds[$id] = $id;
						}
					}
				}
			}
			$this->log('');
		}
	}
	
	public function diff() 
	{
		$result = new stdClass();
		$result->error = 'diff - TODO';
		
		$this->_getMimibazarData($result);
		//$this->_analyzeData($result);
		
		return $result;
	}
}
