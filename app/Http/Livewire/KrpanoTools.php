<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Pusher\Pusher;
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
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options'),
        );

        /*$panos_path = */

        $cmd = 'D:\krpano-1.20.11\krpanotools makepano D:\krpano-1.20.11\templates\krpano.configs D:\krpano-1.20.11\360\p48003.JPG';

        $process = Process::fromShellCommandline($cmd);

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput, $pusher) {
            $line = str($line)->replace("\r", "\n");
            $processOutput .= $line;
            $pusher->trigger('shell', 'newOutput', $line);
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        if ($process->getExitCode()) {
            $exception = new \Exception($cmd . " - " . $processOutput);
            report($exception);

            throw $exception;
        }

    }
}
