<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class GithubService
{
    private $container;
    private $url;
    private $opts = array(
        'http' => array(
            'method' => 'GET',
            'header' => array('User-Agent: Ambiguss')
        )
    );

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->url = $container->getParameter('app.github_api') . '/repos/' . $container->getParameter('app.ambiguss_repo');
    }

    private function get($url)
    {
        $json = file_get_contents($url, false, stream_context_create($this->opts));
        return json_decode($json);
    }

    public function getLastDev()
    {
        $url = $this->url . "/commits/master";
        return $this->get($url);
    }

    public function getActualCommit()
    {
        $tag = $this->getActualTag();

        if (!empty($tag)) {
            return $this->get($tag->commit->url);
        }

        return null;
    }

    public function getAllTags()
    {
        $url = $this->url . "/tags";
        return $this->get($url);
    }

    public function getActualTag()
    {
        $version = 'v' . $this->container->getParameter('app.version');

        foreach ($this->getAllTags() as $tag) {
            if ($tag->name == $version) {
                return $tag;
            }
        }

        return null;
    }

}
