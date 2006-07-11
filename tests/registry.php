<?php
class registry_test_case extends UnitTestCase
{
    function registry_test_case()
    {
        $this->UnitTestCase();
    }

    function testClassExists()
    {
        $this->assertTrue(class_exists('registry'), 'Registry class exists');
    }

    function testAccess()
    {
        $registry = &new registry();
        $this->assertFalse($registry->is_entry('a'));
        $this->assertNull($registry->get('a'));
        $thing = 'thing';
        $registry->set('a', $thing);
        $this->assertTrue($registry->is_entry('a'));
        $this->assertReference($registry->get('a'), $thing);
    }
}
?>

