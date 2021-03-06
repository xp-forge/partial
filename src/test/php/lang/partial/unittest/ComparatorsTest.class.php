<?php namespace lang\partial\unittest;

use unittest\{Test, Values};
use util\{Comparator, Date};

class ComparatorsTest extends \unittest\TestCase {

  #[Test]
  public function byFirstName() {
    $this->assertInstanceOf(Comparator::class, Person::byFirstName());
  }

  #[Test]
  public function byLastName() {
    $this->assertInstanceOf(Comparator::class, Person::byLastName());
  }

  #[Test]
  public function byBirthDate() {
    $this->assertInstanceOf(Comparator::class, Person::byBirthDate());
  }

  #[Test, Values([['Same', 'Same'], ['Same', 'Different'], ['Different', 'Same']])]
  public function compare_using_byFirstName($lastA, $lastB) {
    $this->assertEquals(-1, Person::byFirstName()->compare(
      new Person('A', $lastA, 1977),
      new Person('B', $lastB, 1977)
    ));
  }

  #[Test, Values([['Same', 'Same'], ['Same', 'Different'], ['Different', 'Same']])]
  public function compare_using_byFirstName_object($lastA, $lastB) {
    $this->assertEquals(-1, Person::byFirstName()->compare(
      new Person(new Named('A'), $lastA, 1977),
      new Person(new Named('B'), $lastB, 1977)
    ));
  }

  #[Test]
  public function compare_using_byFirstName_then_byLastName() {
    $this->assertEquals(-1, Person::byFirstName()->then(Person::byLastName())->compare(
      new Person('Same', 'A', 1977),
      new Person('Same', 'B', 1977)
    ));
  }

  #[Test]
  public function compare_using_byLastName_reverse() {
    $this->assertEquals(1, Person::byLastName()->reverse()->compare(
      new Person('Same', 'A', 1977),
      new Person('Same', 'B', 1977)
    ));
  }

  #[Test]
  public function usort_byDate() {
    $wall= new Wall('name', 'open', [
      new Post('thekid', 'Hello World', new Date('2015-04-01')),
      new Post('thekid', 'Version 0.1.0', new Date('2015-04-04')),
      new Post('thekid', 'Added Comparators::reverse()', new Date('2015-04-06'))
    ]);
    $posts= $wall->posts();
    usort($posts, [Post::byDate(), 'compare']);
    $this->assertEquals(
      ['Added Comparators::reverse()', 'Version 0.1.0', 'Hello World'],
      array_map(function($post) { return $post->text(); }, $posts)
    );
  }
}