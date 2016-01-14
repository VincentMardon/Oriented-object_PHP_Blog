<?php
namespace OCFram;

class HTTPResponse extends ApplicationComponent
{
  protected $page;

  public function addHeader($header)
  {
    header($header);
  }

  public function redirect($location)
  {
    header('Location: '.$location);
    exit;
  }

  public function redirect404()
  {
    $this->page = new Page($this->app);
    $this->page->setContentFile(__DIR__.'/../../Errors/404.html');
    
    $this->addHeader('HTTP/1.0 404 Not Found');
    
    $this->send();
  }
  
  public function send()
  {
	$cache = new Cache(null, $this->page);
  	
  	if ($this->page->app->name() === 'Frontend')
  	{
  		// Sauvegarde et affichage selon le timestamp du cache d'index
  		if ($this->page->app->controller()->action() === 'index')
  		{
  			if (file_exists($indexFile = '../tmp/cache/views/Frontend_News_index.txt'))
  			{
  				// si le timestamp du fichier de cache est toujours supérieur au timestamp actuel
  				if ((int) file_get_contents($indexFile, FILE_USE_INCLUDE_PATH, null, 59) > time())
  				{
  					$cache->saveCacheView();
  					
  					// echo
  					echo file_get_contents($indexFile, FILE_USE_INCLUDE_PATH, null, 69);
  					exit;
  				}
  				else
  				{
  					$cache->saveCacheView();
  					
  					exit($this->page->getGeneratedPage());
  				}
  			}
  		}
  		
  		// sauvegarde des caches de données et de vue de news unique puis affichage de la vue selon le timestamp
  		if ($this->page->app->controller()->action() == 'show')
  		{	
  			if (file_exists($showFile = '../tmp/cache/views/Frontend_News_showNews' . $this->page->vars()['news']->id() . '.txt'))
			{
				if ((int) file_get_contents($showFile, FILE_USE_INCLUDE_PATH, null, 59) > time())
				{
					$cache->saveCacheData();
					$cache->saveCacheView();
					
					// echo
					echo file_get_contents($showFile, FILE_USE_INCLUDE_PATH, null, 69);
					exit;
				}
				else
				{
					$cache->saveCacheData();
					$cache->saveCacheView();
					
					exit($this->page->getGeneratedPage());
				}
			}
			
			
  		}
    }
    else
    {
     	exit($this->page->getGeneratedPage());
    }
  }

  public function setPage(Page $page)
  {
    $this->page = $page;
  }

  // Changement par rapport à la fonction setcookie() : le dernier argument est par défaut à true
  public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
  {
    setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
  }
}