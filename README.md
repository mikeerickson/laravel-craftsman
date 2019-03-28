# laravel-craftsman

## Description

Laravel Craftsman provides a suite of scaffolding command using a project agnostic CLI

## Installation

**Using Composer**

```bash
> composer global require codedungeon/laravel-craftsman 
```

**Using curl/wget**

```bash
curl ...

or

wget ...
```

## Usage

```bash
> laravel-craftsman <command> [options] [arguments]

> laravel-craftsman craft:all Post --model App/Models/Post --tablename posts --rows 50 

> laravel-craftsman craft:class App/TestClass --constructor

> laravel-craftsman craft:controller PostController --model App/Models/Post

> laravel-craftsman craft:factory PostFactory --model App/Models/Post

> laravel-craftsman craft:migration create_posts_table --model App/Models/Post --tablename posts

> laravel-craftsman craft:model App/Models/Post --tablename posts 

> laravel-craftsman craft:seed PostTableSeeder --model App/Models/Post --rows 100

```

## Commands

| Command          | Name / Option       | Description                                                      |
|------------------|---------------------|------------------------------------------------------------------|
| craft:all        | base name           | Creates all assets (Controller, Factory, Migration, Model, Seed) |
|                  | --model, -m         | Path to model (eg App/Models/Post)                               |
|                  | --tablename, -t     | Tablename used in database (will set $tablename in Model)        |
|                  |                     | _If not supplied, default table will be pluralized model name_   |
|                  | --rows, -r          | Number of rows used by seed when using Factory                   |
|                  | --no-controller, -c | Do not create controller                                         |
|                  | --no-factory, -f    | Do not create factory                                            |
|                  | --no-migration, -g  | Do not create migration                                          |
|                  | --no-model, -o      | Do not create model                                              |
|                  | --no-seed, -s       | Do not create seed                                               |
| craft:class      | class name          | Creates empty class                                              |
|                  | --constructor, -c   | Include constructor method                                       |
| craft:controller | controller name     | Create controller using supplied options                         |
|                  | --model, -m         | Path to model (eg App/Models/Post)                               |
|                  | --validation, -l    | Create validation blocks where appropriate                       |
|                  | --api, -a           | Create API controller (skips create and update methods)          |
|                  | --empty, -e         | Create empty controller                                          |
| craft:factory    | factory name        | Creates factory using supplied options                           |
|                  | --model, -m         | Path to model (eg App/Models/Post)                               |
| craft:migration  | migration name      | Creates migration using supplied options                         |
|                  | --model, -m         | Path to model (eg App/Models/Post)                               |
|                  | --tablename, -t     | Tablename used in database (will set $tablename in Model)        |
|                  |                     | _If not supplied, default table will be pluralized model name_   |
|                  | --fields, -f        | List of fields (optional)                                        |
|                  |                     | _eg. --fields first_name:string(30), last_name:string(50)_       |
|                  | --down, -d          |  Include down methods (skipped by default)                       |
| craft:model      | model name          | Creates model using supplied options                             |
|                  | --tablename, -t     | Tablename used in database (will set $tablename in Model)        |
|                  |                     | _If not supplied, default table will be pluralized model name_   |
| craft:seed       | base seed name      | Creates seed file using supplied options                         |
|                  | --model, -m         | Path to model (eg App/Models/Post)                               |
|                  | --rows, -r          | Number of rows to use in factory call (Optional)                 |


## Custom Templates
Laravel Craftsman provides support for creating custom templates.  

### Customizing Templates
If you wish to create derivatives of the supported templates, you can customize the `config.php` located in the `laravel-craftsman` directory

```php
    'templates' => [
            'class' => 'user_templates/class.mustache.php',
            'api-controller' => 'user_templates/api-controller.mustache.php',
            'empty-controller' => 'user_templates/empty-controller.mustache.php',
            'controller' => 'user_templates/controller.mustache.php',
            'factory' => 'user_templates/factory.mustache.php',
            'migration' => 'user_templates/migration.mustache.php',
            'model' => 'user_templates/model.mustache.php',
            'seed' => 'user_templates/seed.mustache.php',
        ],
```
    
### List of available variables
The following variables can be used in any of the supported templates (review the `templates` directory for a basis of how to create custom templates)

* fields
* model
* model_path
* name
* namespace
* num_rows
* tablename

## License

Copyright &copy; 2019 Mike Erickson
Released under the MIT license

## Credits

laravel-craftsman written by Mike Erickson

E-Mail: [codedungeon@gmail.com](mailto:codedungeon@gmail.com)

Twitter: [@codedungeon](http://twitter.com/codedungeon)

Website: [codedungeon.io](http://codedungeon.io)
