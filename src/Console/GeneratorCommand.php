<?php
/**
 * Created by PhpStorm.
 * User: ryanchan
 * Date: 28/12/2015
 * Time: 5:49 PM
 */

namespace Riseno\Localizable\Console;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class GeneratorCommand
 *
 * @package Riseno\Localizable\Console
 */
class GeneratorCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'riseno:localizable:generate {table}';

    /**
     * @var string
     */
    protected $description = 'Generate localizable table';

    /**
     * @var string
     */
    protected $suffix = 'localizations';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $file;

    /**
     * GeneratorCommand constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $file
     */
    public function __construct(Filesystem $file)
    {
        $this->file = $file;
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        $migrationPath = $this->getMigrationPath();

        $content = $this->file->get($this->getStubPath());

        $this->replaceClassName($content)
            ->replaceTableName($content)
            ->replaceParentClass($content);

        $this->file->put($migrationPath, $content);

        $this->output->success('Migration file : ' . $migrationPath);
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceClassName(&$stub)
    {
        $stub = str_replace('__table_class__', $this->getTableClass(), $stub);

        return $this;
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceParentClass(&$stub)
    {
        $stub = str_replace('__parent_class__', $this->argument('table'), $stub);

        return $this;
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceTableName(&$stub)
    {
        $stub = str_replace('__table__', $this->getTableName(), $stub);

        return $this;
    }

    /**
     * @return string
     */
    private function getTableName()
    {
        return $this->argument('table') . '_' . $this->suffix;
    }

    /**
     * @return string
     */
    private function getTableClass()
    {
        return studly_case('create_' . $this->getTableName() . '_table');
    }

    /**
     * @return string
     */
    private function getStubPath()
    {
        return __DIR__ . '/../migrations/localizeTableStub.stub';
    }

    /**
     * @return string
     */
    private function getMigrationPath()
    {
        return base_path() . '/database/migrations/' . date('Y_m_d', time()) . '_' . substr((string)time(), 4, 6) . '_create_' . $this->getTableName() . '_table.php';
    }
}