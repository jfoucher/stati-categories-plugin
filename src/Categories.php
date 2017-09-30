<?php

namespace Stati\Plugin\Categories;

use Stati\Event\SiteEvent;
use Stati\Event\SettingTemplateVarsEvent;
use Stati\Plugin\Plugin;
use Stati\Site\Site;
use Stati\Site\SiteEvents;
use Stati\Liquid\TemplateEvents;

class Categories extends Plugin
{
    protected $name = 'categories';

    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::DID_READ_SITE => 'onAfterSiteRead',
        );
    }

    public function onAfterSiteRead(SiteEvent $event)
    {
        $site = $event->getSite();

        $posts = $site->getPosts();
        $config = $site->getConfig();

        $categories = [];
        foreach ($posts as $post) {
            if ($post->category) {
                // post has a single category;
                if (!isset($categories[$post->category])) {
                    $categories[$post->category] = [$post];
                } else {
                    $categories[$post->category][] = $post;
                }
            }
            if ($post->categories) {
                $postCategories = $post->categories;
                if (is_string($postCategories)) {
                    $postCategories = explode(' ', $postCategories);
                }
                foreach ($postCategories as $category) {
                    if (!isset($categories[$category])) {
                        $categories[$category] = [$post];
                    } else {
                        $categories[$category][] = $post;
                    }
                }
            }
        }

        $config['categories'] = $categories;
        $site->setConfig($config);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
