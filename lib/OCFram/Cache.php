<?php
namespace OCFram;

class Cache
{	
	use FileWriter;
	
	// PROPERTIES //
	
	protected $entity; // Entité News ou Comment pour récupération d'id de news
	protected $page; // Objet Page comportant le FrontendControler pour la mise en cache des vues
	
	protected $now; // temps actuel
	protected $dateInterval; // durée du cache
	protected $expire; // expiration du cache
	
	// CONSTRUCTOR //
	
	public function __construct($entity = '', $page = '')
	{
		$this->now = new \DateTime;
		$this->dateInterval = \DateInterval::createFromDateString('1 hour');
		$this->setExpire($this->now, $this->dateInterval);
	
		if (!empty($entity))
		{
			$this->setEntity($entity);
		}
		
		if (!empty($page))
		{
			$this->setPage($page);
		}
	}
	
	// METHODS //
	
	// Création des caches de vues
	public function saveCacheView()
	{
		$controller = $this->page->app()->controller();
		
		// initialisation du cache de vue de l'index
		if ($controller->view() === 'index')
		{
			$viewFile = '../tmp/cache/views/' . $controller->app()->name() . '_' . $controller->module() . '_' . $controller->view() .'.txt';
		}
		
		// initialisation du caches des vues de news unique
		if ($controller->view() === 'show')
		{
			$viewFile = '../tmp/cache/views/' . $controller->app()->name() . '_' . $controller->module() . '_' . $controller->view() .'News' . $this->page->vars()['news']->id() . '.txt';
		}
		
		$viewContent = $this->expire . "\n" . $this->page->getGeneratedPage();		
		$this->write($viewFile, $viewContent);
	}
	
	// Création des caches de données
	public function saveCacheData()
	{
		$pageVars = $this->page->vars();
		
		// initialisation et création du cache des données de news
		$newsDataFile = '../tmp/cache/data/news' . $pageVars['news']->id() . '.txt';
		$newsData = serialize($pageVars['news']);
		
		$newsDataContent = $this->expire . "\n" . $newsData;
		$this->write($newsDataFile, $newsDataContent);
		
		// initialisation et création du cache des données de liste de commentaires
		if (isset($pageVars['comments']))
		{
			$commentsData = [];
			
			$commentsDataFile = '../tmp/cache/data/commentsOfNews' . $pageVars['news']->id() . '.txt';
			
			foreach ($pageVars['comments'] as $comment)
			{
				$commentsData[] = $comment;
			}
			
			$commentsDataContent = $this->expire . "\n" . serialize($commentsData);
			$this->write($commentsDataFile, $commentsDataContent);
		}
	}
	
	// Suppression des caches de données et de vues de news uniques
	public function unlinkDataAndShowCaches()
	{
		if ($this->entity instanceof \Entity\Comment)
		{
			$commentsFile = '../tmp/cache/data/commentsOfNews' . $this->entity->news() . '.txt';
			$newsFile = '../tmp/cache/data/news' . $this->entity->news() . '.txt';
			$showFile = '../tmp/cache/views/Frontend_News_showNews' . $this->entity->news() . '.txt';
		}
		else
		{
			$commentsFile = '../tmp/cache/data/commentsOfNews' . $this->entity->id() . '.txt';
			$newsFile = '../tmp/cache/data/news' . $this->entity->id() . '.txt';
			$showFile = '../tmp/cache/views/Frontend_News_showNews' . $this->entity->id() . '.txt';
		}
		
		
		if (file_exists($commentsFile))
		{
			unlink($commentsFile);
		}
		
		if (file_exists($newsFile))
		{
			unlink($newsFile);
		}
		
		if (file_exists($showFile))
		{
			unlink($showFile);
		}
	}
	
	// Suppression du cache de vue de l'index
	public function unlinkIndexCache()
	{
		$file = '../tmp/cache/views/Frontend_News_index.txt';
		
		if (file_exists($file))
		{
			unlink($file);
		}
	}
	
	// SETTERS //
	
	public function setExpire($now, $interval)
	{
		$this->expire = '[Date d\'expiration] : le ' . $now->add($interval)->format('d/m/Y à H\hi') . "\n" . 
						'[Timestamp] : ' . (time() + 3600);
	}
	
	public function setPage(Page $page)
	{
		$this->page = $page;
	}
	
	public function setEntity(Entity $entity)
	{
		$this->entity = $entity;
	}
}