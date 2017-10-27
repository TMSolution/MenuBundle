<?php

namespace TMSolution\MenuBundle\Tests\Menu;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of EntityAnalyzer
 *
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>
 */
class MenuKrenelTest extends KernelTestCase {


    static protected $orm;

    static public function setUpBeforeClass() {
        self::bootKernel();
        self::$orm = static::$kernel->getContainer()
                ->get('tm_solution_menu.main_menu');
        
        

    }

}
