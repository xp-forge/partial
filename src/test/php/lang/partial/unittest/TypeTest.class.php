<?php namespace lang\partial\unittest;

use lang\mirrors\TypeMirror;
use lang\partial\InstanceCreation;
use lang\{ArrayType, Primitive, Type, Value, XPClass};
use unittest\Test;

class TypeTest extends PartialTest {
  private $fixture;

  /** @return void */
  public function setUp() {
    $this->fixture= new TypeMirror($this->declareType([Value::class], '{
      use <T>\is\lang\partial\Value;
      use <T>\with\lang\partial\Equals;
      use <T>\with\lang\partial\Builder;

      /** @type int */
      private $id;

      /** @type lang.partial.unittest.Named */
      private $name;

      /** @type string[] */
      private $skills;
    }'));
  }

  #[Test]
  public function primitive_accessor() {
    $this->assertEquals(Primitive::$INT, $this->fixture->methods()->named('id')->returns());
  }

  #[Test]
  public function primitive_parameter() {
    $this->assertEquals(Primitive::$INT, $this->fixture->constructor()->parameters()->named('id')->type());
  }

  #[Test]
  public function primitive_builder() {
    $this->assertEquals(Primitive::$INT, (new TypeMirror(InstanceCreation::typeOf($this->fixture)))->methods()->named('id')->parameters()->first()->type());
  }

  #[Test]
  public function object_accessor() {
    $this->assertEquals(new XPClass(Named::class), $this->fixture->methods()->named('name')->returns());
  }

  #[Test]
  public function object_parameter() {
    $this->assertEquals(new XPClass(Named::class), $this->fixture->constructor()->parameters()->named('name')->type());
  }

  #[Test]
  public function object_builder() {
    $this->assertEquals(new XPClass(Named::class), (new TypeMirror(InstanceCreation::typeOf($this->fixture)))->methods()->named('name')->parameters()->first()->type());
  }

  #[Test]
  public function array_accessor() {
    $this->assertEquals(new ArrayType(Primitive::$STRING), $this->fixture->methods()->named('skills')->returns());
  }

  #[Test]
  public function array_parameter() {
    $this->assertEquals(new ArrayType(Primitive::$STRING), $this->fixture->constructor()->parameters()->named('skills')->type());
  }

  #[Test]
  public function array_builder() {
    $this->assertEquals(new ArrayType(Primitive::$STRING), (new TypeMirror(InstanceCreation::typeOf($this->fixture)))->methods()->named('skills')->parameters()->first()->type());
  }

  #[Test]
  public function builder() {
    $this->assertEquals(XPClass::forName($this->fixture->name()), (new TypeMirror(InstanceCreation::typeOf($this->fixture)))->methods()->named('create')->returns());
  }

  #[Test]
  public function hashCode_type() {
    $this->assertEquals(Primitive::$STRING, $this->fixture->methods()->named('hashCode')->returns());
  }

  #[Test]
  public function toString_type() {
    $this->assertEquals(Primitive::$STRING, $this->fixture->methods()->named('hashCode')->returns());
  }

  #[Test]
  public function compareTo_types() {
    $compareTo= $this->fixture->methods()->named('compareTo');
    $this->assertEquals(
      [Type::$VAR, Primitive::$INT],
      [$compareTo->parameters()->first()->type(), $compareTo->returns()]
    );
  }

  #[Test]
  public function equals_types() {
    $compareTo= $this->fixture->methods()->named('equals');
    $this->assertEquals(
      [Type::$VAR, Primitive::$BOOL],
      [$compareTo->parameters()->first()->type(), $compareTo->returns()]
    );
  }
}