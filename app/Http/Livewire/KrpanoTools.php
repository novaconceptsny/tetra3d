<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Symfony\Component\Process\Process;

class KrpanoTools extends Component
{
    public $output;

    public function render()
    {
        return view('livewire.krpano-tools');
    }

    public function runCommand()
    {
        $cmd = 'D:\krpano-1.20.11\krpanotools makepano D:\krpano-1.20.11\templates\krpano.config D:\krpano-1.20.11\360\p48003.JPG';

        $process = Process::fromShellCommandline($cmd);

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
            $this->output = $line;
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        if ($process->getExitCode()) {
            $exception = new \Exception($cmd . " - " . $processOutput);
            report($exception);

            throw $exception;
        }

        $this->output = "Finished!";
    }
}
