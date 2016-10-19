<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Route;

use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Class RoutesCache.
 *
 * @author  Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class RoutesCache extends \Sonata\AdminBundle\Route\RoutesCache
{
    /**
     * @param AdminInterface $admin
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function load(AdminInterface $admin)
    {
        $routes = array();
        foreach ($admin->getRoutes()->getElements() as $code => $route) {
            $routes[$code] = $route->getDefault('_sonata_name');
        }
        return $routes;
    }
}
