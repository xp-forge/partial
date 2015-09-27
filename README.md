Partials: Compile-time metaprogramming
======================================

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/partial.svg)](http://travis-ci.org/xp-forge/partial)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_5plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/partial/version.png)](https://packagist.org/packages/xp-forge/partial)

For situations where more logic than just "compiler-assisted copy&paste" using [PHP's traits](http://php.net/traits) is necessary, this library provides a syntax that expand dynamically based on the containing class at compile time.

Partial flavors
---------------
The partials provided by this library's are divided in two flavors: Kinds and composeables.

* **Kinds** define the general concept of a type. You can say, e.g.: This type ***is*** a list of something, or a reference to something. Or, to use more concrete examples: The `Customers` class is a list of customers (encapsulated by `Customer` instances), and `Name` is a reference to (a string) containing a name.
* **Composeables** can be used alone or in combination to extend a base type or a kind. You can say, e.g. This type comes ***with*** a certain functionality. Again, using a realistic use-case: The `Person` class comes with `toString()`, `compareTo()` and `hashCode()` methods.

Regardless of their flavor, some partials are implemented by a regular PHP trait, others are dynamically created at runtime. However, the syntax for both is `use [Containing-Type]\[is-or-with]\[Partial-Name];`.

Walk-through
------------
The `Identity` trait creates a value object wrapping around exactly one member. It creates a one-arg constructor, and a `value()` for retrieving the value, and includes appropriate `equals()` and `toString()` implementations. 

<table><tr><td width="360" valign="top">
Writing this:
<pre lang="php">
namespace example;

use lang\partial\Identity;

class Name extends \lang\Object {
  use Name\is\Identity;

  public function personal() {
    return '~' === $this->value{0};
  }
}
</pre>
</td><td width="360" valign="top">
...is equivalent to:
<pre lang="php">
namespace example;

class Name extends \lang\Object {
  private $value;

  public function __construct($value) {
    $this->value= $value;
  }

  public function value() {
    return $this->value;
  }

  public function personal() {
    return '~' === $this->value{0};
  }
}
</pre>
</td></tr></table>

The parametrized `ValueObject` trait creates accessors for all instance members and ensures `equals()` and `toString()` are implemented for this value object in a generic way, using the util.Objects class to compare the objects memberwise. All we need to do is to add a constructor (*this is not generated as we might want to add default values and custom verification logic*).

<table><tr><td width="360" valign="top">
Writing this:
<pre lang="php">
namespace example;

use lang\partial\ValueObject;

class Wall extends \lang\Object {
  use Wall\with\ValueObject;

  private $name, $type, $posts;

  public function __construct(
    Name $name,
    Type $type,
    Posts $posts
  ) {
    $this->name= $name;
    $this->type= $type;
    $this->posts= $posts;
  }
}
</pre>
</td><td width="360" valign="top">
...is equivalent to:
<pre lang="php">
namespace example;

class Wall extends \lang\Object {
  private $name, $type, $posts;

  public function __construct(
    Name $name,
    Type $type,
    Posts $posts
  ) {
    $this->name= $name;
    $this->type= $type;
    $this->posts= $posts;
  }

  public function name() {
    return $this->name;
  }

  public function type() {
    return $this->type;
  }

  public function posts() {
    return $this->posts;
  }

  public function equals($cmp) {
    // omitted for brevity
  }

  public function toString() {
    // omitted for brevity
  }
}
</pre>
</td></tr></table>

If the constructor consists solely of assignments, you can include the `Constructor` trait and remove it. The parameters will be declared in the order the fields are declared: top to bottom, left to right in the source code.

<table><tr><td width="360" valign="top">
Writing this:
<pre lang="php">
namespace example;

use lang\partial\Constructor;

class Author extends \lang\Object {
  use Author\with\Constructor;

  private $handle, $name;
}
</pre>
</td><td width="360" valign="top">
...is equivalent to:
<pre lang="php">
namespace example;

class Author extends \lang\Object {
  private $handle, $name;

  public function __construct($handle, $name) {
    $this->handle= $handle;
    $this->name= $name;
  }
}
</pre>
</td></tr></table>

The `ListOf` trait creates a list of elements which can be accessed by their offset, iterated by `foreach`, and offers `equals()` and `toString()` default implementations.

<table><tr><td width="360" valign="top">
Writing this:
<pre lang="php">
namespace example;

use lang\partial\ListOf;

class Posts extends \lang\Object
  implements \IteratorAggregate {
  use Posts\is\ListOf;
}
</pre>
</td><td width="360" valign="top">
...is equivalent to:
<pre lang="php">
namespace example;

class Posts extends \lang\Object
  implements \IteratorAggregate {
  private $backing;

  public function __construct(...$elements) {
    $this->backing= $elements;
  }

  public function present() {
    return !empty($this->backing);
  }

  public function size() {
    return sizeof($this->backing);
  }

  public function at($offset) {
    if (isset($this->backing[$offset])) {
      return $this->backing[$offset];
    }
    throw new ElementNotFoundException(…);
  }

  public function first() {
    if (empty($this->backing)) {
      throw new ElementNotFoundException(…);
    }
    return $this->backing[0];
  }

  public function getIterator() {
    foreach ($this->backing as $element) {
      yield $element;
    }
  }

  public function equals($cmp) {
    // omitted for brevity
  }

  public function toString() {
    // omitted for brevity
  }
}
</pre>
</td></tr></table>

The `WithCreation` trait will add a static `with()` method to your class, generating a fluent interface to create instances. This is especially useful in situation where there are a lot of constructor parameters.

The `Comparators` trait adds static `by[Member]` methods returning util.Comparator instances for each member. These instances can be combined using *then* (`Post::byDate()->then(Post::byAuthor())`) or reversed (`Post::byDate()->reverse()`).

```php
namespace example;

use lang\partial\ValueObject;
use lang\partial\WithCreation;
use lang\partial\Comparators;

class Post extends \lang\Object {
  use Wall\with\ValueObject;
  use Wall\with\WithCreation;
  use Wall\with\Comparators;

  private $author, $text, $date;

  public function __construct($author, $text, Date $date) {
    $this->author= $author;
    $this->text= $text;
    $this->date= $date;
  }
}
```

The `ListIndexedBy` trait creates a list of elements which can be queried by name, also overloading iteration and creating `equals()` and `toString()` in a sensible manner. The class needs to implement the abstract `index` method and return a string representing the name.

```php
namespace example;

use lang\partial\ListIndexedBy;

class Walls extends \lang\Object implements \IteratorAggregate {
  use Walls\is\ListIndexedBy;

  protected function index($wall) { return $wall->name()->value(); }
}
```

Putting it all together, we can see the API:

```php
use util\data\Sequence;

$post= Post::with()->author('Timm')->text('Hello World!')->date(Date::now())->create();

$walls= new Walls(
  new Wall(new Name('one'), Type::$OPEN, new Posts()),
  new Wall(new Name('two'), Type::$CLOSED, new Posts($post))
);

$walls->present();        // TRUE, list is not empty
$walls->size();           // 2
$walls->provides('one');  // TRUE, wall named one found
$walls->provides('zero'); // FALSE, no such wall
$walls->first();          // Wall(name => Name("one"), type => OPEN)
$walls->named('two');     // Wall(name => Name("two"), type => CLOSED)
$walls->named('three');   // ***ElementNotFoundException

foreach ($walls as $wall) {
  Console::writeLine('== ', $wall->name()->value(), ' wall (', $wall->type(), ') ==');
  Sequence::of($wall->posts())->sorted(Post::byDate())->each(function($post) {
    Console::writeLine('Written by ', $post->author(), ' on ', $post->date());
    Console::writeLine($post->text());
    Console::writeLine();
  });
}
```

See also
--------
* http://groovy-lang.org/metaprogramming.html
* http://projectlombok.org/
