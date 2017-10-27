<?php

namespace TMSolution\MenuBundle\Tests\Functional\Model\Admin\Menuitem;

use TMSolution\GeneratorBundle\Generator\Faker\Populator;
use Flexix\GeneratorBundle\Util\CommandTestCase;
use AppBundle\Model\Admin\User\Model;



/**
 * Description of ModelTest
 *
 * @author Mariusz Piela <mariusz.piela@tmsolution.pl>
 */
class SmokeTest extends CommandTestCase {

    protected static $model;
    protected static $entityManager;
    protected static $client; 
    
    protected static $entityClass='TMSolution\MenuBundle\Entity\MenuItem';
    protected static $username='donatello@tmnt.com';
    protected static $password='cowabunga';
    protected static $bundle= 'TMSolution\MenuBundle';
    
    public static  function setUpBeforeClass() {
    
        self::bootKernel();
        $drop=self::runCommand(self::$kernel, 'doctrine:schema:drop -f');
        $update=self::runCommand(self::$kernel, 'doctrine:schema:update -f');
        $generate=self::runCommand(self::$kernel, sprintf('tmsolution:generate:fixture --bundle %s --dir Test --quantity 3 --silent',self::$bundle));
        $loadBase=self::runCommand(self::$kernel, sprintf('tmsolution:fixtures:load --fixtures   ./src/%s/DataFixtures/Basic  --silent',self::$bundle));
        $load=self::runCommand(self::$kernel, sprintf('tmsolution:fixtures:load --fixtures   ./src/%s/DataFixtures/Test  --silent',self::$bundle));
        self::$entityManager= static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();
        self::$model = new Model(self::$entityManager);
    
     
    }
    
    protected function getEntity()
    {
    
        $generator = \TMSolution\GeneratorBundle\Generator\Faker\Factory::create('pl');
        $customColumnFormatter = array(); 
        $populator = new Populator($generator, self::$entityManager);
        $populator->addEntity(self::$entityClass, 1, $customColumnFormatter);
        $result = $populator->execute(self::$entityManager,false);
        return $result[0];
    }
    
    protected function printError($crawler){

            $error = $crawler->filter('div.text-exception > h1');
            $line = $crawler->filter('a.file_link');
            
            if ($error->count()) {
                echo sprintf("\nError: %s, %s\n",$error->text(),$line->text());
            }

    }
       
    protected  function setUp()
    {
        self::$client = static::createClient();
        $route='/login';
        $crawler = self::$client->request('GET', $route);
        $form = $crawler->selectButton('_submit')->form(array(
            '_username'  => self::$username,
            '_password'  => self::$password
        ));

        self::$client->submit($form);
   
    }
    
    protected function  getToken($crawler,$formTypeName)
    {
        $extract = $crawler->filter(sprintf('input[name="%s"]',$formTypeName))->extract(array('value'));
        return  $extract[0];
    }
      
    public function testIndex()
    {
        $route='/Admin/MenuItem/index';
        $crawler = self::$client->request('GET', $route);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route));
    }
    public function testNew()
    {
        $route='/Admin/MenuItem/new';
        $crawler = self::$client->request('GET', $route);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route));
    }
  
    public function testCreate()
    {
        $route='/Admin/MenuItem/new';
        $crawler = self::$client->request('GET', $route);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route));
        
        $entity=$this->getEntity();
        $form = $crawler->selectButton('create')->form([
                'Admin_MenuItem_insert[_token]'  => $this->getToken($crawler,'Admin_MenuItem_insert[_token]'),                
                'Admin_MenuItem_insert[name]'  => $entity->getName(),                
                'Admin_MenuItem_insert[route]'  => $entity->getRoute(),                
                'Admin_MenuItem_insert[lft]'  => $entity->getLft(),                
                'Admin_MenuItem_insert[lvl]'  => $entity->getLvl(),                
                'Admin_MenuItem_insert[rgt]'  => $entity->getRgt(),                
                'Admin_MenuItem_insert[position]'  => $entity->getPosition(),                
                'Admin_MenuItem_insert[parent]'  => $entity->getParent()->getId(),                
                'Admin_MenuItem_insert[root]'  => $entity->getRoot()->getId(),    
        ]);

        self::$client->submit($form);
        $crawler = self::$client->followRedirect();
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code during inserting %s ",$route));
    } 

    public function testEdit()
    {
        $route='/Admin/MenuItem/edit/1';
        $crawler = self::$client->request('GET', $route);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route));    
    }
    
    public function testUpdate()
    {
        $route='/Admin/MenuItem/edit/1';
        $crawler = self::$client->request('GET', $route);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route)); 
        
        $entity=$this->getEntity();
        $form = $crawler->selectButton('update')->form([
                'Admin_MenuItem_edit[_token]'  => $this->getToken($crawler,'Admin_MenuItem_edit[_token]'),                
                'Admin_MenuItem_edit[name]'  => $entity->getName(),                
                'Admin_MenuItem_edit[route]'  => $entity->getRoute(),                
                'Admin_MenuItem_edit[lft]'  => $entity->getLft(),                
                'Admin_MenuItem_edit[lvl]'  => $entity->getLvl(),                
                'Admin_MenuItem_edit[rgt]'  => $entity->getRgt(),                
                'Admin_MenuItem_edit[position]'  => $entity->getPosition(),                
                'Admin_MenuItem_edit[parent]'  => $entity->getParent()->getId(),                
                'Admin_MenuItem_edit[root]'  => $entity->getRoot()->getId(),    
        ]);

        self::$client->submit($form);
        $crawler = self::$client->followRedirect();
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code during update",$route));
    } 

    public function testDeleteForm()
    {
        $route='/Admin/MenuItem/delete/1';
        $crawler = self::$client->request('GET', $route);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route)); 
       
    }
    
    public function testDelete()
    {
        $route='/Admin/MenuItem/delete/1';
        $crawler = self::$client->request('GET', $route);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route)); 
        
        $form = $crawler->selectButton('delete')->form(array(
            'Admin_MenuItem_delete[_token]'  => $this->getToken($crawler,'Admin_MenuItem_delete[_token]'),
        ));

        self::$client->submit($form);
        $crawler = self::$client->followRedirect();
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code during delete",$route));
    } 
    
    public function testList()
    {
        $route='/Admin/MenuItem/list';
        $crawler = self::$client->request('GET', $route,[],[],['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route)); 
       
    } 
    
    public function testFilter()
    {
        $route='/Admin/MenuItem/filter';
        $crawler = self::$client->request('GET', $route,[],[],['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route)); 
        $entity=$this->getEntity();
        $form = $crawler->selectButton('filter')->form(
        [
            'Admin_MenuItem_filter' =>[                
                'name'  => $entity->getName(),                
                'route'  => $entity->getRoute(),                
                'lft'  => $entity->getLft(),                
                'lvl'  => $entity->getLvl(),                
                'rgt'  => $entity->getRgt(),                
                'position'  => $entity->getPosition(),                
                'parent'  => $entity->getParent()->getId(),                
                'root'  => $entity->getRoot()->getId(), 
            ]
        ]);
        
        self::$client->submit($form);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code during delete",$route));
        
    }    
        
    public function testTypeahead()
    {
        $route='/Admin/MenuItem/typeahead/name';
        $entity=$this->getEntity();
        $crawler = self::$client->request('GET', $route,[
            'Admin_MenuItem_filter' =>[                
                'name'  => $entity->getName(),                
                'route'  => $entity->getRoute(),                
                'lft'  => $entity->getLft(),                
                'lvl'  => $entity->getLvl(),                
                'rgt'  => $entity->getRgt(),                
                'position'  => $entity->getPosition(),                
                'parent'  => $entity->getParent()->getId(),                
                'root'  => $entity->getRoot()->getId(), 
            ]
        ],[],['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->printError($crawler);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode(), sprintf("Unexpected HTTP status code for GET %s",$route)); 
       
    }}



   