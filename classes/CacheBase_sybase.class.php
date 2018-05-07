<?php

	/*
	 * Outil de cache à deux niveaux :
	 * - utilisant les éléments static de PHP, permettant de s'assurer qu'une requete n'est pas exécutée 2 fois sur une meme page
	 * - utilisant MEMCACHE
	 *
	 * Configuration du niveau : attribut LEVEL
	 * - LEVEL=0 : pas de cache
	 * - LEVEL=1 : cache uniquement en memoire
	 * - LEVEL=2 : cache en memoire et via memcached
	 *
	 * fonctionne comme un Singleton :
	 * $cache=CacheBase_sybase::getCache();
	 *
	 * puis :
	 * $cache->set("cle","valeur");
	 * $valeur=$cache->get("cle");
	 *
	 * Pour mettre en cache le résultat d'un SELECT :
	 * $cache->sybase_query("requete",$link)
	 *
	 * Pour mettre en cache le résultat d'un COUNT SQL :
	 * $cache->sybase_query_count("requete",$link)
	 *
	 * Pour changer temporairement le LEVEL
	 * $cache->switchLevel(x)  -> la prochaine opération (sybase_query...) se fera avec ce level temporaire
	 *
	 */
	class CacheBase_sybase
	{
		public static $AGE=30;
		public static $LEVEL=1;



		private $levelhistory;
		private $result_cache = array();
		// singleton
		private static $cache=null;
		function __construct()
		{
			$this->levelhistory=CacheBase_sybase::$LEVEL;
		}

		public static function getCache()
		{
			if(!CacheBase_sybase::$cache)
				CacheBase_sybase::$cache=new CacheBase_sybase(); 
			return CacheBase_sybase::$cache;
		}

		public function switchLevel($level)
		{
			$this->levelhistory=CacheBase_sybase::$LEVEL;
			CacheBase_sybase::$LEVEL=$level;
		}
		private function switchBackLevel()
		{
			CacheBase_sybase::$LEVEL=$this->levelhistory;
		}

		private function getMemcache()
		{
			if(CacheBase_sybase::$LEVEL!=2)
				return null;
			$memcache = new Memcache();
			return $memcache;
		}

		private function setCache2($key,$value)
		{
			if(CacheBase_sybase::$LEVEL!=2) return FALSE;
			$this->getMemcache()->set($key,$value, false, CacheBase_sybase::$AGE);
		}

		private function getCache2($key)
		{
			if(CacheBase_sybase::$LEVEL!=2) return FALSE;
			return $this->getMemcache()->get($key);
		}

		public function get($key)
		{
			if(CacheBase_sybase::$LEVEL==0) return FALSE;

			$hash = hash('md5',$key);
			$retour = !isset($this->result_cache[$hash]) ? FALSE : $this->result_cache[$hash];
		    if (!$retour) // ce n'est pas dans le niveau 1
            {
 	           	$retour=$this->getCache2($key);
	           	if($retour==FALSE) // ce n'est pas dans le niveau 2
            		return FALSE;
            }
            return $retour;
		}
		public function set($key,$value)
		{
			if(CacheBase_sybase::$LEVEL==0) return;

		    $hash = hash('md5', $key);
			$this->result_cache[$hash]=$value;
			$this->setCache2($key,$value);
		}

		public function sybase_query($query,$link, $clazz = false)
		{
         	$data=$this->get($query);
            if (!$data)
            {
            	$data = array();

            	if($link==null)
					$resul=sybase_query($query);
				else
					$resul=sybase_query($query,$link);

				while($resul && $row = $this->sybase_fetch_object($resul, $clazz))
				{
					$data[]=$row;
				}
				$this->set($query,$data);
            }
            $this->switchBackLevel();
            return $data;
		}

		public function sybase_fetch_object($resul, $clazz = false)
		{
			return $clazz ? sybase_fetch_object($resul, $clazz) : sybase_fetch_object($resul);
		}

		public function sybase_query_count($query,$link)
		{
			$num=$this->get($query);

            if ($num<0 || $num=="")
            {
				$resul=sybase_query($query,$link);
				if ($resul)
					$num=sybase_num_rows($resul);
				else
					$num = 0;

				$this->set($query,$num);
            }
            $this->switchBackLevel();
            return $num;
		}

		public function reset_cache(){
			$this->result_cache = array();
		}
	}
?>