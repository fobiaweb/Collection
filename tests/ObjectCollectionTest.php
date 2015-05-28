<?php
/**
 * CollectionTest class  - CollectionTest.php file
 *
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2015 Dmitriy Tyurin
 */

namespace Fobia\Test;

use Fobia\ObjectCollection;

class Item
{

    public $name;
    public $key;

    function __construct($name = 'default', $key = null, $keyName = null,
                         $keyValue = null)
    {
        $this->name = $name;
        $this->key  = $key;

        if ($keyName !== null) {
            $this->$keyName = $keyValue;
        }
    }
}

/**
 * ObjectCollectionTest class
 *
 * @package   Fobia\Test
 */
class ObjectCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Fobia\ObjectCollection
     */
    protected $object;
    protected $handler_error_level;

    const DEFAULT_COUNT = 10;

    /**
     * Создает список элементов
     * Каждый элемент имеет
     *  id   - порядковый номер
     *  name - имя (на основе id)
     *  type - типо (у всех new)
     *  group - группа элемента (Общее кол. создаваемых элементов деляться на 5 групп)
     *  param1 - каждая группа имеет параметры paramX, кол. которых возрвстают с возрвстанием группы
     *  param2, param3...
     *
     * @param int $count
     * @param bool $create_obj
     * @return array
     */
    public function createListItems($count = null, $create_obj = false)
    {
        $groups = 5;
        if ($count === null) {
            $count = self::DEFAULT_COUNT;
        }
        $data = array();
        $group_count = ceil($count / $groups);

        for ($i = 0; $i < $count; $i++ ) {
            $_current_group =  ceil(($i + 1) / $group_count);
            $item = array(
                'id'   => $i,
                'name' => "name_" . $i,
                'type' => 'new',
                'group' => $_current_group,
            );
            for($g = 1; $g < $_current_group; $g++) {
                $item["param$g"] = null;
            }

            if ($create_obj) {
                $item = (object) $item;
            }
            $data[] = $item;
        }

        return $data;
    }

    /**
     *
     * @param int $count
     * @return \Fobia\ObjectCollection
     */
    protected function createObjectCollection($count = 1)
    {
        $objectCollection = new ObjectCollection();

        for ($index = 0; $index < $count; $index ++ ) {
            $objectCollection->addAt(new Item("name_$index", $index));
        }
        return $objectCollection;
    }

    protected function setErrorHandler($errno_level = 256)
    {
        $this->handler_error_level = error_reporting(0);
        set_error_handler(function($errno) use ($errno_level) {
            if ( ! ($errno & $errno_level)) {
                return;
            }
            throw new \Exception("Error", $errno);
        });
    }

    protected function restoreErrorHandler()
    {
        restore_error_handler();
        error_reporting($this->handler_error_level);
        $this->handler_error_level = null;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ObjectCollection(array(new Item()));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if ($this->handler_error_level !== null) {
            $this->restoreErrorHandler();
        }
    }
    /*     * ***********************************************************************
     * TEST FUNCTION
     * *********************************************************************** */

    /**
     * @covers Fobia\ObjectCollection::__construct
     * @covers Fobia\ObjectCollection::_resor
     */
    public function testConstruct1()
    {
        $collection = new ObjectCollection();
        $this->assertInstanceOf('Fobia\ObjectCollection', $collection);
        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers Fobia\ObjectCollection::__construct
     * @covers Fobia\ObjectCollection::_resor
     */
    public function testConstruct2()
    {
        $arr        = array(
            array('name' => 'o_1'),
            array('name' => 'o_1'),
            array('name' => 'o_1'),
        );
        $collection = new ObjectCollection($arr);
        $this->assertInstanceOf('Fobia\ObjectCollection', $collection);
        $this->assertEquals(3, $collection->count());
    }

    /**
     * @covers Fobia\ObjectCollection::eq
     */
    public function testEq()
    {
        $item0 = new Item();
        $item1 = new Item();
        $item2 = new Item();

        $collection = new ObjectCollection(array(
            $item0, $item1, $item2
        ));

        $this->assertEquals($item0, $collection->eq());
        $this->assertEquals($item0, $collection->eq(0));
        $this->assertEquals($item1, $collection->eq(1));
        $this->assertNull(@$collection->eq(3));
    }

    /**
     * @covers Fobia\ObjectCollection::index
     */
    public function testIndex()
    {
        $obj = $this->object->eq();
        $k = $this->object->index($obj);
        $this->assertEquals(0, $k[0]);

        $obj1 = new Item('new');
        $this->object->addAt($obj1);
        $k = $this->object->index($obj1);
        $this->assertEquals(1, $k[0]);
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAtDefault().
     */
    public function testAddAtDefault()
    {
        $obj = new Item('new');
        $this->object->addAt($obj);

        $this->assertEquals(2, $this->object->count());
        $this->assertEquals($obj, $this->object->eq(1));
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAtArrayParametr().
     */
    public function testAddAtArrayParametr()
    {
        $collection = new ObjectCollection();
        $collection->addAt(array('n' => 1));
        $collection->addAt(array('n' => 2));
        $collection->addAt(array('n' => 3));
        $collection->addAt(array('n' => 4));
        $this->assertCount(4, $collection);

        $collection->addAt(array('n' => 4));
        $collection->addAt(array('n' => 4));
        $collection->unique(false);
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAtFirst().
     */
    public function testAddAtFirst()
    {
        $obj = new Item('new');

        $this->object->addAt($obj, 0);
        $this->assertEquals(2, $this->object->count());
        $this->assertEquals($obj, $this->object->eq(0));
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAtOther().
     */
    public function testAddAtOther()
    {
        $obj = new Item('new');
        $this->object->addAt($obj, 7);

        $this->assertEquals(2, $this->object->count());
        $this->assertEquals($obj, $this->object->eq(1));

        $obj2 = new Item('new 2');
        $this->object->addAt($obj2, 1);

        $this->assertEquals(3, $this->object->count());
        $this->assertEquals($obj2, $this->object->eq(1));

        $this->object->addAt($obj2, 1);
        $this->assertEquals(4, $this->object->count());
        $this->assertEquals($this->object->eq(2), $this->object->eq(1));
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAtOther2().
     */
    public function testAddAtOther2()
    {
        $collection = new ObjectCollection();
        for ($index = 0; $index <= 10; $index ++ ) {
            $collection->addAt(new Item('new-' . $index));
        }
        $this->assertCount(11, $collection);

        $collection->addAt(new Item('add'), 2);
        $this->assertCount(12, $collection);
        $this->assertEquals('new-1', $collection->eq(1)->name);
        $this->assertEquals('add', $collection->eq(2)->name);
        $this->assertEquals('new-2', $collection->eq(3)->name);
        $collection->removeAt(2);

        $collection->addAt(new Item('add'), -1);
        $this->assertCount(12, $collection);
        $this->assertEquals('new-9', $collection->eq(9)->name);
        $this->assertEquals('add', $collection->eq(10)->name);
        $this->assertEquals('new-10', $collection->eq(11)->name);
        $collection->removeAt(10);

        $collection->addAt(new Item('add'), 99);
        $this->assertCount(12, $collection);
        $this->assertEquals('new-1', $collection->eq(1)->name);
        $this->assertEquals('new-10', $collection->eq(10)->name);
        $this->assertEquals('add', $collection->eq(11)->name);
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAtUniqe().
     */
    public function testAddAtUniqe()
    {
        $collection = new ObjectCollection();
        for ($index = 1; $index <= 10; $index ++ ) {
            $collection->addAt(new Item('new-' . $index));
        }
        $collection->unique();
        $this->assertCount(10, $collection);

        $obj = $collection->eq();
        $this->assertEquals('new-1', $obj->name);

        $collection->addAt($obj);
        $this->assertCount(10, $collection);

        $this->assertEquals($obj,     $collection->eq(9) );
        $this->assertEquals('new-2',  $collection->eq(0)->name);
        $this->assertEquals('new-10', $collection->eq(8)->name);
        $this->assertEquals('new-1',  $collection->eq(9)->name);
    }

    /**
     * @covers Fobia\ObjectCollection::filter
     */
    public function testFilter()
    {
        $this->object->addAt(new Item('new_1'));
        $this->object->addAt(new Item('new_2'));
        $this->object->addAt(new Item('new_3'));

        $obj = new Item('other');
        $this->object->addAt($obj);

        $param = 'other';
        $this->object->filter(function($obj) use ($param) {
            if ($obj->name === $param) {
                return false;
            } else {
                return true;
            }
        });

        $this->assertCount(4, $this->object);

        $this->object->addAt($obj);
        $this->assertCount(5, $this->object);

        $this->object->filter(function($obj) {
            if ($obj->name !== 'other') {
                return false;
            } else {
                return true;
            }
        });
        $this->assertCount(1, $this->object);
        $this->assertEquals($obj,     $this->object->eq());
    }

    /**
     * @covers Fobia\ObjectCollection::filter
     * @expectedException \Exception
     */
    public function testFilterError()
    {
        $this->setErrorHandler(E_USER_ERROR);
        $this->object->filter("no-callback");
        $this->restoreErrorHandler();
    }

    /**
     * @covers Fobia\ObjectCollection::find
     */
    public function testFindProperty()
    {
        $this->object->addAt(new Item('new_1'));
        $this->object->addAt(new Item('new_2'));
        $this->object->addAt(new Item('new_3'));

        $obj = new Item('other');
        $obj->otherKey = 17;
        $this->object->addAt($obj);

        $this->assertCount(5, $this->object);

        $resultFind = $this->object->find('otherKey');
        $this->assertInstanceOf('\Fobia\ObjectCollection', $resultFind);
        $this->assertCount(1, $resultFind);

        $this->assertEquals($obj, $resultFind->eq());
    }

    /**
     * @covers Fobia\ObjectCollection::find
     */
    public function testFindValue()
    {
        $this->object->addAt(new Item('new_1', 1));
        $this->object->addAt(new Item('new_1', 2));
        $this->object->addAt(new Item('new_2', 3));
        $this->object->addAt(new Item('new_3', 4));

        $resultFind = $this->object->find('name', 'new_1');
        $this->assertCount(2, $resultFind);
        $this->assertNotSame($resultFind->eq(0), $resultFind->eq(1));

        $this->assertEquals($this->object->eq(1), $resultFind->eq(0));
        $this->assertEquals($this->object->eq(2), $resultFind->eq(1));


        $resultFind->set('key', 'find');
        $this->assertEquals('find', $this->object->eq(1)->key);
        $this->assertEquals('find', $this->object->eq(2)->key);
    }

    /**
     * @covers Fobia\ObjectCollection::find
     */
    public function testFindCallback()
    {
        $this->object->addAt(new Item('new_1', 1));
        $this->object->addAt(new Item('new_1', 2));
        $this->object->addAt(new Item('new_2', 3));
        $this->object->addAt(new Item('new_3', 4));

        $resultFind = $this->object->find(function($obj, $key) {
            if ($obj->name == 'new_1') {
                return true;
            }
        });

        $this->assertCount(2, $resultFind);
        $this->assertEquals('new_1', $resultFind->eq(0)->name);
        $this->assertEquals($this->object->eq(2), $resultFind->eq(1));
    }

    /**
     * @covers Fobia\ObjectCollection::set
     */
    public function testSet()
    {
        // Remove the following lines when you implement this test.
        $this->object->addAt(new Item());

        $this->object->set('key', 'set value');
        foreach ($this->object as $obj) {
            $this->assertEquals('set value', $obj->key);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::get
     */
    public function testGet()
    {
        $collection = new ObjectCollection(array(
            new Item(0),
            new Item(1),
            new Item(2),
            new Item(3),
        ));

        $get = $collection->get('name');
        foreach ($get as $key => $value) {
            $this->assertEquals($key, $value);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::get
     */
    public function testGetWhithArray()
    {
        $objects = $this->createObjectCollection(5);

        $arr = array(
            array('name' => 'name_0', 'key' => 0),
            array('name' => 'name_1', 'key' => 1),
            array('name' => 'name_2', 'key' => 2),
            array('name' => 'name_3', 'key' => 3),
            array('name' => 'name_4', 'key' => 4),
        );
        $get = $objects->get(array('name', 'key'));
        $this->assertSame ($arr, $get);
    }

    /**
     * @covers Fobia\ObjectCollection::get
     */
    public function testGetWhithKeyname()
    {
        $objects = $this->createObjectCollection(5);

        $arr = array(
            'name_0'  => 0,
            'name_1'  => 1,
            'name_2'  => 2,
            'name_3'  => 3,
            'name_4'  => 4,
        );
        $this->assertSame($arr, $objects->get('name', 'key'));
    }

    /**
     * @covers Fobia\ObjectCollection::get
     */
    public function testGetWhithKeynameForArray()
    {
        $objects = $this->createObjectCollection(5);

        $arr = array(
            'name_0' => array('name' => 'name_0', 'key' => 0),
            'name_1' => array('name' => 'name_1', 'key' => 1),
            'name_2' => array('name' => 'name_2', 'key' => 2),
            'name_3' => array('name' => 'name_3', 'key' => 3),
            'name_4' => array('name' => 'name_4', 'key' => 4),
        );
        $this->assertSame($arr, $objects->get( 'name', array('name', 'key') ));
    }

    /**
     * @covers Fobia\ObjectCollection::get
     */
    public function testGetWhithArray2()
    {
        $objects = new ObjectCollection(array(
            new Item('name_0', 0),
            new Item('name_1', 1),
            new Item('name_2', 2),
            new Item('name_3', 3),
        ));

        $get = $objects->get(array('name', 'key'));

        $this->assertInternalType('array', $get);
        foreach ($get as $key => $value) {
            $this->assertEquals("name_{$key}", $value['name']);
            $this->assertEquals($key, $value['key']);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::removeAt
     * @todo   Implement testRemoveAt().
     */
    public function testRemoveAt()
    {
        $foo = new Item('foo');
        $this->object->addAt($foo);
        $this->assertCount(2, $this->object);

        $this->object->removeAt();
        $this->assertCount(1, $this->object);
        $this->assertNotEquals('foo', $this->object->eq()->name);

        $this->object->addAt($foo);
        $this->object->removeAt(0);
        $this->assertCount(1, $this->object);
        $this->assertEquals('foo', $this->object->eq()->name);
    }

    /**
     * @covers Fobia\ObjectCollection::remove
     * @todo   Implement testRemove().
     */
    public function testRemove()
    {
        $foo = new Item('foo');
        $this->object->addAt($foo);

        $this->object->remove($foo);
        $this->assertCount(1, $this->object);
        $this->assertNotEquals('foo', $this->object->eq()->name);
    }

    /**
     * @covers Fobia\ObjectCollection::each
     */
    public function testEach()
    {
        $collection = new ObjectCollection(array(
            new Item('new_4', 1),
            new Item('new_3', 2),
            new Item('new_2', 3),
            new Item('new_1', 4),
        ));

        $self = & $this;

        $collection->each(function($obj) {
            $obj->each = true;
        });

        foreach ($collection as $obj) {
            $self->assertTrue($obj->each);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::each
     */
    public function testEachCallback()
    {
        $this->object->addAt(new Item('new_1', 1));
        $this->object->addAt(new Item('new_2', 2));
        $this->object->addAt(new Item('new_3', 3));
        $this->object->addAt(new Item('new_4', 4));

        $this->object->each(function($obj) {
            $obj->name = "each";
            return false;
        });

        $this->assertEquals("each", $this->object->eq()->name);
        $this->assertEquals("new_1", $this->object->eq(1)->name);
    }

    /**
     * @covers Fobia\ObjectCollection::each
     * @expectedException \Exception
     */
    public function testEachError()
    {
        $this->setErrorHandler(E_USER_ERROR);
        $this->object->each("no-callback");
        $this->restoreErrorHandler();
    }

    /**
     * @covers Fobia\ObjectCollection::sort
     * @covers Fobia\ObjectCollection::_sort_property
     * @todo   Implement testSort().
     */
    public function testSort()
    {
        $this->object->addAt(new Item('new_1', 4));
        $this->object->addAt(new Item('new_3', 2));
        $this->object->addAt(new Item('new_2', 3));
        $this->object->addAt(new Item('new_4', 1));
        $this->object->eq()->key = 1000;

        $this->object->sort('key');
        $this->assertEquals(1, $this->object->eq()->key);

        $this->object->sort(function($a, $b) {
            return ($a->key != 4);
        });
        $this->assertEquals(4, $this->object->eq()->key);
    }

    /**
     * @covers Fobia\ObjectCollection::sort
     * @covers Fobia\ObjectCollection::_sort_property
     * @todo   Implement testSortError1().
     * @expectedException \Exception
     */
    public function testSortError1()
    {
        $this->setErrorHandler(E_USER_WARNING);
        $this->object->sort('');
        $this->restoreErrorHandler();
    }

    /**
     * @covers Fobia\ObjectCollection::sort
     * @todo   Implement testSortError()
     * @expectedException \Exception
     */
    public function testSortError2()
    {
        $this->setErrorHandler(E_USER_WARNING);
        $this->object->sort(array(1));
        $this->restoreErrorHandler();
    }


    /**
     * @covers Fobia\ObjectCollection::count
     */
    public function testCount()
    {
        $this->assertCount(1, $this->object);
        $this->assertEquals(1, $this->object->count());
    }

    /**
     * @covers Fobia\ObjectCollection::getIterator
     */
    public function testGetIterator()
    {
        $arr = $this->object->getIterator();
        $this->assertInternalType('object', $arr[0]);
        $this->assertInstanceOf('\\ArrayIterator', $arr);

        foreach ($this->object as $obj) {
            $this->assertEquals('default', $obj->name);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::toArray
     */
    public function testToArray()
    {
        $arr = $this->object->toArray();
        $this->assertInternalType('array', $arr);
        $this->assertInternalType('object', $arr[0]);
    }

    /**
     * @covers Fobia\ObjectCollection::unique
     * @todo   Implement testUnique().
     */
    public function testUnique()
    {
        $obj = new Item('add-1');
        $this->object->addAt($obj);
        $this->object->addAt($obj);
        $this->object->addAt($obj);

        $this->assertCount(4, $this->object);

        $this->object->unique();
        $this->assertCount(2, $this->object);

        $this->object->addAt($obj);
        $this->object->addAt($obj);
        $this->object->addAt($obj);
        $this->assertCount(2, $this->object);
    }

    /**
     * @covers Fobia\ObjectCollection::merge
     * @todo   Implement testMerge().
     */
    public function testMerge()
    {
        $obj = new Item('add-1');
        $collection = new ObjectCollection();
        $collection->addAt($obj);
        $collection->addAt($obj);
        $collection->addAt($obj);

        $this->object->merge($collection);
        $this->assertCount(4, $this->object);

        $this->object->unique();
        $this->assertCount(2, $this->object);
        $this->object->merge($collection);
        $this->assertCount(2, $this->object);
    }

    /**
     * @covers Fobia\ObjectCollection::merge
     * @expectedException \Exception
     */
    public function testMergeOther()
    {
        $this->setErrorHandler(E_USER_WARNING);

        $obj = 1;
        $this->object->merge($obj);

        $this->restoreErrorHandler();

        $this->assertCount(1, $this->object);
    }

    /**
     * @covers Fobia\ObjectCollection::merge
     */
    public function testMergeArrayItems()
    {
        $arr = array(
            $obj = new Item('add-1'),
            $obj = new Item('add-2')
        );

        $this->object->merge($arr);
        $this->assertCount(3, $this->object);
    }

    /**
     * @covers Fobia\ObjectCollection::merge
     * @todo   Implement testMergeObject().
     */
    public function testMergeObject()
    {
        $obj = new Item('add-1');
        $this->object->merge(array($obj));

        $this->assertCount(2, $this->object);
    }
}