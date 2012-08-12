<?php
class registry_test_case extends UnitTestCase
{
    public function registry_test_case()
    {
        $this->UnitTestCase();
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('registry'), 'Registry class exists');
    }

    public function testAccess()
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
