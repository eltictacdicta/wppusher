<?php

use Pusher\Pusher;

require_once __DIR__ . '/../WpPusherTestCase.php';

class ContainerTest extends WpPusherTestCase
{
    /**
     * @var Pusher
     */
    private $pusher;

    public function setUp()
    {
        $this->pusher = new Pusher;
    }

    /**
     * @test
     */
    public function it_is_a_container()
    {
        $this->assertInstanceOf('Pusher\Container', $this->pusher);
    }

    /**
     * @test
     */
    public function it_can_resolve_a_class_with_no_dependencies()
    {
        $db = $this->pusher->make('DB');

        $this->assertInstanceOf('DB', $db);
    }

    /**
     * @test
     */
    public function it_can_resolve_a_class_with_nested_dependencies()
    {
        $manager = $this->pusher->make('UserManager');

        $this->assertInstanceOf('UserManager', $manager);
    }

    /**
     * @test
     */
    public function it_can_bind_an_alias()
    {
        $this->pusher->bind('UserRepository', 'DBUserRepository');

        $repository = $this->pusher->make('UserRepository');

        $this->assertInstanceOf('DBUserRepository', $repository);
    }

    /**
     * @test
     */
    public function it_can_bind_a_closure()
    {
        $closure = function(Pusher $pusher) {
            return new DB;
        };

        $this->pusher->bind('DB', $closure);

        $db = $this->pusher->make('DB');

        $this->assertInstanceOf('DB', $db);
    }

    /**
     * @test
     */
    public function it_can_bind_an_instance()
    {
        $dbInstance = new DB;

        $this->pusher->bind('DB', $dbInstance);

        $db = $this->pusher->make('DB');

        $this->assertInstanceOf('DB', $db);
    }

    /**
     * @test
     */
    public function it_can_bind_a_singleton()
    {
        $this->pusher->singleton('obj', 'StdClass');

        $this->assertSame(
            $this->pusher->make('obj'),
            $this->pusher->make('obj')
        );
    }
}

// Fixtures:
class DB {
    // Class with no dependencies
}

class EntityMapper {
    // Class with no dependencies
}

interface UserRepository {}

class DBUserRepository {
    public function __construct(DB $db, EntityMapper $em)
    {
        // Constructor stuff
    }
}

class UserManager {
    public function __construct(DBUserRepository $users)
    {
        // Constructor stuff
    }
}
