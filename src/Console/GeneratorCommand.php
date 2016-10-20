<?php
/**
 * Created by PhpStorm.
 * User: ryanchan
 * Date: 28/12/2015
 * Time: 5:49 PM.
 */
namespace Riseno\Localizable\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class GeneratorCommand.
 */
class GeneratorCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'riseno:localizable:generate {table} {--m}';

    /**
     * @var string
     */
    protected $description = 'Generate localizable table';

    /**
     * @var string
     */
    protected $suffix = 'localization';

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
        parent::__construct();

        $this->file = $file;
    }


    public function handle()
    {
        $migrationPath = $this->getMigrationPath();

        $content = $this->file->get($this->getStubPath());

        $this->replaceClassName($content)
            ->replaceTableName($content)
            ->replaceParentClass($content);

        $this->file->put($migrationPath, $content);

        $this->output->success('Migration file : '.$migrationPath);

        if ($this->option('m')) {
            $modelPath = $this->getModelPath();

            $content = $this->file->get($this->getModelStubPath());

            $this->replaceModelClassName($content)
                ->replaceModelClassMethod($content)
                ->replaceModelParentClass($content)
                ->replaceModelField($content);

            $this->file->put($modelPath, $content);

            $this->output->success('Model file : '.$modelPath);
        }
    }

    /**
     * @return string
     */
    private function getModelPath()
    {
        return base_path().'/app/'.$this->getModelClassName().'.php';
    }

    /**
     * @return string
     */
    private function getModelClassName()
    {
        return studly_case($this->argument('table').'_'.$this->suffix);
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceModelClassName(&$stub)
    {
        $stub = str_replace('__class__', $this->getModelClassName(), $stub);

        return $this;
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceModelClassMethod(&$stub)
    {
        $stub = str_replace('__method__', strtolower($this->argument('table')), $stub);

        return $this;
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceModelField(&$stub)
    {
        $stub = str_replace('__field__', strtolower($this->argument('table')), $stub);

        return $this;
    }

    /**
     * @param $stub
     *
     * @return $this
     */
    private function replaceModelParentClass(&$stub)
    {
        $stub = str_replace('__parent_class__', ucfirst($this->argument('table')), $stub);

        return $this;
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
        return $this->argument('table').'_'.$this->suffix.'s';
    }

    /**
     * @return string
     */
    private function getTableClass()
    {
        return studly_case('create_'.$this->getTableName().'_table');
    }

    /**
     * @return string
     */
    private function getStubPath()
    {
        return __DIR__.'/../migrations/localizeTableStub.stub';
    }

    /**
     * @return string
     */
    private function getModelStubPath()
    {
        return __DIR__.'/../migrations/localizeModelStub.stub';
    }

    /**
     * @return string
     */
    private function getMigrationPath()
    {
        return base_path().'/database/migrations/'.date('Y_m_d', time()).'_'.substr((string) time(), 4, 6).'_create_'.$this->getTableName().'_table.php';
    }
}
