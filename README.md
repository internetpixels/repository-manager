# Repository manager with simple SQL Query builder
Manage your entities and repositories with this simple repository manager. An all in one solution for building repositories and entities.


[![License](https://camo.githubusercontent.com/cf76db379873b010c163f9cf1b5de4f5730b5a67/68747470733a2f2f6261646765732e66726170736f66742e636f6d2f6f732f6d69742f6d69742e7376673f763d313032)](https://github.com/internetpixels/repository-manager)
[![Build Status](https://travis-ci.org/internetpixels/repository-manager.svg)](https://travis-ci.org/internetpixels/repository-manager)
[![Maintainability](https://api.codeclimate.com/v1/badges/d0d817a21ca7243433b3/maintainability)](https://codeclimate.com/github/internetpixels/repository-manager)

## Basic examples

### Create your first entity

The entity will contain the data for a specific object (Also known as DAO) and will mediate between the application and the database. An entity has to use getters and setters.

    <?php
    
    namespace YourProject\People;
    
    use InternetPixels\RepositoryManager\Entities\AbstractEntity;
    use InternetPixels\RepositoryManager\Entities\EntityInterface;
    
    class PersonEntity extends AbstractEntity implements EntityInterface
    {
    
        /**
         * @var string
         */
        private $name;
    
        /**
         * @var int
         */
        private $age;

        /**
         * @return string
         */
        public function getName(): string
        {
            return $this->name;
        }
    
        /**
         * @param string $name
         */
        public function setName(string $name)
        {
            $this->name = $name;
        }

        /**
         * @return int
         */
        public function getAge(): int
        {
            return $this->age;
        }
    
        /**
         * @param int $age
         */
        public function setAge(int $age)
        {
            $this->age = $age;
        }
    }

### Create your first repository

A repository will handle all data transfers between an entity and your database. The repository will build queries to execute those actions.

**Note:** The ``entityName`` needs to map to your database table name.

    <?php
    
    namespace YourProject\People;
    
    use InternetPixels\RepositoryManager\Entities\AbstractEntity;
    use InternetPixels\RepositoryManager\Factories\EntityFactory;
    use InternetPixels\RepositoryManager\Repositories\AbstractRepository;

    class PeopleRepository extends AbstractRepository
    {
    
        protected $entityName = 'people';

        public function update(AbstractEntity $entity)
        {
            $query = $this->queryBuilder->new($this->entityName)
                ->update([
                    'name' => $this->dataManager->sanitize($entity->getName()),
                    'age' => $this->dataManager->sanitize($entity->getAge()),
                ])
                ->where(['id' => $entity->getId()])
                ->limit(1)
                ->get();
    
            return $this->executeQuery($query);
        }
        
        /**
         * @param array $data
         * @return PersonEntity
         */
        protected function dataToEntity(array $data): PersonEntity
        {    
            /** @var PersonEntity $entity */
            $entity = EntityFactory::create('people');
    
            $entity->setName($data['name']);
            $entity->setAge($data['age']);
            
            return $entity;
        }
    }

### Register the Data manager

You only need to register the ``RepositoryDataManager`` and the new entity in the ``EntityFactory``. The Data manager needs an (existing) ``Mysqli`` connection.

    $mysqliConnection = new \Mysqli(
        $config['mysql.host'],
        $config['mysql.user'],
        $config['mysql.password'],
        $config['mysql.database']
    );
    
    $repositoryDataManager = new \InternetPixels\RepositoryManager\Managers\RepositoryDataManager($mysqliConnection);
    
    // Add all your entities:
    \InternetPixels\RepositoryManager\Factories\EntityFactory::register('people', new PersonEntity());
    
    $peopleRepository = new PeopleRepository($repositoryDataManager);
    
## Usage of the repository

In a service of your application you can implement the ``PeopleRepository`` and use them for basic CRUD actions or your custom implementations.

    $peopleRepository = new PeopleRepository($repositoryDataManager);
    
    // Get all records:
    $people = $peopleRepository->read();
    
    // Update a person
    $person = EntityFactory::create('people');
    $person->setId(1);
    $person->setName('Person name');
    $person->setAge(26); // update the age
    
    $peopleRepository->update($person);
    
    // Delete a person
    $person = EntityFactory::create('people');
    $person->setId(1);
    
    $peopleRepository->delete($person);
    
## Usage of the SQL Query builder

This package contains a simple query builder. Please use the functionality to prevent basic SQL issues.

    // Sanitize input in a repository before pushing to the database:
    $safe = $this->dataManager->sanitize($entity->getName());
    
    // Build a new select query in a repository
    $query = $this->queryBuilder->new($this->entityName)
                ->select()
                ->get();
                
    // Build a new select query in a repository with a limit
    $query = $this->queryBuilder->new($this->entityName)
                ->select()
                ->limit(2)
                ->get();
                
    // Build a new select query in a repository with a where clause
    $query = $this->queryBuilder->new($this->entityName)
                ->select()
                ->where(['age' => 25])
                ->get();