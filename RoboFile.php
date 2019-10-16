<?php


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/codeception/codeception/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class RoboFile extends \Robo\Tasks
{
    use \Codeception\Task\MergeReports;
    use \Codeception\Task\SplitTestsByGroups;

    const NUMBER_OF_GROUPS = 2;

    public function parallelSplitTests()
    {
        $this->taskSplitTestFilesByGroups(self::NUMBER_OF_GROUPS)
            ->testsFrom('tests/acceptance')
            ->projectRoot('.')
            ->groupsTo('tests/_paracept/paracept_')
            ->run();
    }

    /**
     * @param $config string
     * @param $skipGroups array e.g. ['notFullyFunctioning', 'feedback']
     * @return \Robo\Result
     */
    public function parallelRun($config, $skipGroups)
    {
        $configFile = is_null($config) ? 'codeception.yml' : $config;
        $parallel = $this->taskParallelExec();
        for ($i = 1; $i <= self::NUMBER_OF_GROUPS; $i++) {
            $command = $this->taskCodecept(__DIR__ . '/vendor/bin/codecept')// use built-in Codecept task
            ->suite('acceptance')
                ->env($this->env)
                ->configFile($configFile)
                ->group("paracept_$i")// for all paracept_* groups
                ->html("result_paracept_$i.html");// save XML results
            if (!empty($skipGroups)) $command->optionList('--skip-group', $skipGroups);

            $parallel->process($command);
        }

        return $parallel->run();
    }

    public function parallelMergeResults()
    {
        $merge = $this->taskMergeHTMLReports();
        $num = self::NUMBER_OF_GROUPS;
        for ($i = 1; $i <= $num; $i++) {
            $merge->from(__DIR__ . "/tests/_output/result_paracept_$i.html");
        }
        $merge->into(__DIR__ . '/tests/_output/report.html')->run();
    }

    /**
     * @param $env string
     * @param $config string
     * @param $skipGroups string e.g skipGroups:notFullyFunctioning,feedback
     * @return \Robo\Result
     */
    public function parallelAll($env, $config, $skipGroups)
    {
        $this->env = $env;
        $this->startSeleniumServer();

        $this->codeceptClean();
        $this->parallelSplitTests();
        $skipGroupsArr = $this->processParam($skipGroups);
        $result = $this->parallelRun($config, $skipGroupsArr);
        $this->parallelMergeResults();

        $this->stopSeleniumServer();

        return $result;
    }

    /**
     * Recursively cleans output directory and generated code.
     * https://codeception.com/docs/reference/Commands#Clean
     */
    private function codeceptClean()
    {
        echo "Recursively cleans output directory and generated code.\n";
        $command = __DIR__ . '/vendor/bin/codecept clean';
        $process = new Process($command, __DIR__, null, null, null);
        $process->run();
    }

    /**
     * @param $param
     * @return array|void
     */
    private function processParam($param)
    {
        $tmp = explode(':', $param);
        if (count($tmp) < 2) return;

        $tmp = explode(',', $tmp[1]);
        if (count($tmp) == 0) return;

        return $tmp;
    }

    private function startSeleniumServer()
    {
        $envFilePath = __DIR__ . '/tests/_envs/' . $this->env . '.yml';
        $config = Yaml::parse(file_get_contents($envFilePath));
        if (is_null($config)) {
            exit(1);
        }

        $command = $config['modules']['config']['RunCommand']['cmd'];
        $sleep = $config['modules']['config']['RunCommand']['sleep'];

        $process = new Process($command, __DIR__, null, null, null);
        echo '===Start selenium server: ' . $command . "\n";
        $process->start();
        echo '===Process ID: ' . $process->getPid() . "\n";
        $this->processes[] = $process;
        sleep($sleep);
    }

    private function stopSeleniumServer()
    {
        foreach (array_reverse($this->processes) as $process) {
            /** @var $process Process  * */
            if (!$process->isRunning()) {
                continue;
            }
            echo '===Stop process: ' . $process->getPid() . "\n";
            $process->stop();
        }
        $this->processes = [];
    }

    protected $processes = [];
    protected $env;
}
