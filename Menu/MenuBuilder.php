<?php

namespace TMSolution\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder {

    protected $factory;
    protected $orm;
    protected $rootMenuItemName;

    const LINK = 1;
    const CATALOG = 2;
    const SEPARATOR = 3;
    const ROOT = 4;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, $orm, $rootMenuItemName) {
        $this->orm = $orm;
        $this->factory = $factory;
        $this->rootMenuItemName = $rootMenuItemName;
    }

    public function createMainMenu(RequestStack $requestStack) {

        $repository = $this->orm->getRepository('TMSolution\MenuBundle\Entity\MenuItem');
        $rootMenuItem = $repository->findOneByName($this->rootMenuItemName);

        if (!$rootMenuItem) {
            throw new \Exception(sprintf('There is no root menu item: "%s"', $this->rootMenuItemName));
        }
        $menu = $this->factory->createItem('root');

        //$children = $repository->childrenHierarchy($rootMenuItem);

        $tree = $this->buildTree($repository, $rootMenuItem);
        $this->createMenu($menu, $tree);
        return $menu;
    }

    protected function buildTree($repository, $rootMenuItem) {
        $queryBuilder = $repository->getNodesHierarchyQueryBuilder(
                        $rootMenuItem, false, [], false
                )
                ->select('node,menuItemType')
                ->innerJoin('node.menuItemType', 'menuItemType');
// sample for user managment integration       
//            ->innerJoin('Core\SecurityBundle\Entity\Right', 'right', 'WITH', 'node.objectidentity = right.objectidentity and right.viewRight=1')
//            ->where('right.user = :user or right.role in (:roles)');
//            $qb->setParameters([
//                'user' => $this->user,
//                'roles' => $this->user->getRoles()
//            ]);
        $nodes = $queryBuilder->getQuery()->getArrayResult();
        return $repository->buildTree($nodes, []);
    }

    protected function createMenu($menu, $children) {

// actually not used,         
//        usort($children, function($a, $b) {
//            return $a['position'] - $b['position'];
//        });

        foreach ($children as $child) {

            $routeParameters = [];
            
            if ($child['routeParameters']) {
                
                $routeParameters = $child['routeParameters'];
            }

            try {
                $menuItemId = $child['menuItemType']['id'];

                if ($menuItemId == self::LINK) {

                    $menu->addChild($child['name'], ['route' => $child['route'],
                        'routeParameters' => $routeParameters
                    ])->setExtra('translation_domain', 'TMSolutionMenuBundle');
                } else if ($menuItemId == self::CATALOG) {

                    $menu->addChild($child['name'], ['uri' => '#',
                        'routeParameters' => $routeParameters
                    ])->setExtra('translation_domain', 'TMSolutionMenuBundle');
                } else if ($menuItemId == self::SEPARATOR) {

                    $menu->addChild('separator', ['separator' => true]);
                }
            } catch (\Exception $e) {

                throw new \Exception(sprintf('Error generating for route: "%s" - %s', $child["name"], $e->getMessage()));
            }

            $this->createMenu($menu[$child['name']], $child['__children']);
        }
    }

}
