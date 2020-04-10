<?php namespace lang\partial\unittest;

use lang\{IllegalArgumentException, XPClass};
use lang\partial\InstanceCreation;

class InstanceCreationTest extends \unittest\TestCase {

  #[@test]
  public function of_with_fqcn() {
    $this->assertInstanceOf(
      'lang.partial.InstanceCreation',
      InstanceCreation::of('lang.partial.unittest.Author')
    );
  }

  #[@test]
  public function of_with_class() {
    $this->assertInstanceOf(
      'lang.partial.InstanceCreation',
      InstanceCreation::of(XPClass::forName('lang.partial.unittest.Author'))
    );
  }

  #[@test]
  public function typeOf_with_fqcn() {
    $this->assertInstanceOf(
      'lang.XPClass',
      InstanceCreation::typeOf('lang.partial.unittest.Author')
    );
  }

  #[@test]
  public function typeOf_with_class() {
    $this->assertInstanceOf(
      'lang.XPClass',
      InstanceCreation::typeOf(XPClass::forName('lang.partial.unittest.Author'))
    );
  }

  #[@test]
  public function returned_creationtype_name_in_same_namespace_as_type() {
    $this->assertEquals(
      'lang.partial.unittest.AuthorCreation',
      InstanceCreation::typeOf('lang.partial.unittest.Author')->getName()
    );
  }

  #[@test, @expect(IllegalArgumentException::class), @values([
  #  ['interfaces', 'lang.Generic'],
  #  ['enums', 'lang.partial.unittest.Coin'],
  #  ['abstract classes', 'lang.partial.unittest.Entity'],
  #  ['without constructor', 'lang.partial.unittest.WithoutConstructor']
  #])]
  public function of_does_not_accept($reason, $class) {
    InstanceCreation::of($class);
  }

  #[@test, @expect(IllegalArgumentException::class), @values([
  #  ['interfaces', 'lang.Generic'],
  #  ['enums', 'lang.partial.unittest.Coin'],
  #  ['abstract classes', 'lang.partial.unittest.Entity'],
  #  ['without constructor', 'lang.partial.unittest.WithoutConstructor']
  #])]
  public function typeOf_does_not_accept($reason, $class) {
    InstanceCreation::typeOf($class);
  }

  #[@test]
  public function keywords() {
    $instance= InstanceCreation::of(XPClass::forName('lang.partial.unittest.Event'))
      ->name('Test')
      ->class('public')
      ->create()
    ;
    $this->assertEquals('public', $instance->class());
  }
}