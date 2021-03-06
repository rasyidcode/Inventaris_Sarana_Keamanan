<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;

class CreateNewControllerInsideModule extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Modules';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:module:controller';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Create a new controller inside a module.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:module:controller <module_name> <controller_name>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'module_name'    => 'Module name that we want to add the controller to.',
        'controller_name'    => 'ControllerName is the controller name'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        try {
            if (empty($params)) throw new Exception('No arguments specified');
            $moduleName = $params[0];
            $controllerName = $params[1];

            $baseModulePath = ROOTPATH.'app/Modules/'.$moduleName;

            if (!is_dir($baseModulePath)) throw new \Exception('Module not found!');

            $this->call('make:controller', ['App/Modules/'.$moduleName.'/Controllers/'.$controllerName]);
        } catch (\Exception $e) {
            CLI::write(CLI::color($e->getMessage(), 'red'));
        }
    }
}
