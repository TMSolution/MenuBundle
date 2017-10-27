<?php

// src/AppBundle/Entity/Log.php

namespace TMSolution\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="menu_item_type")
 */
class MenuItemType {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=300, nullable=false)
     */
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menuItemType")
     */
    protected $menuItems; 

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return MenuItemType
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add menuItem
     *
     * @param \TMSolution\MenuBundle\Entity\MenuItem $menuItem
     *
     * @return MenuItemType
     */
    public function addMenuItem(\TMSolution\MenuBundle\Entity\MenuItem $menuItem)
    {
        $this->menuItems[] = $menuItem;

        return $this;
    }

    /**
     * Remove menuItem
     *
     * @param \TMSolution\MenuBundle\Entity\MenuItem $menuItem
     */
    public function removeMenuItem(\TMSolution\MenuBundle\Entity\MenuItem $menuItem)
    {
        $this->menuItems->removeElement($menuItem);
    }

    /**
     * Get menuItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }
}
