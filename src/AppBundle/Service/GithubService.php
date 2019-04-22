<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class GithubService
{
    private $container;
    private $url = "https://api.github.com/repos/alexdu98/Ambiguss";
    private $opts = array(
        'http' => array(
            'method' => 'GET',
            'header' => array('User-Agent: Ambiguss')
        )
    );

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function get($url)
    {
        $json = file_get_contents($url, false, stream_context_create($this->opts));
        return json_decode($json);
    }

    public function getLastDev()
    {
        $url = $this->url . "/commits/develop";
        return $this->get($url);
    }

    public function getActualCommit()
    {
        $tag = $this->getActualTag();

        return $this->get($tag->commit->url);
    }

    public function getAllTags()
    {
        $url = $this->url . "/tags";
        return $this->get($url);
    }

    public function getActualTag()
    {
        $version = 'v' . $this->container->getParameter('version');

        foreach ($this->getAllTags() as $tag) {
            if ($tag->name == $version) {
                return $tag;
            }
        }

        return null;
    }

}
