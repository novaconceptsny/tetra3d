<?php

namespace App\Http\Livewire\Modals;

use App\Models\Spot;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Pusher\Pusher;
use Symfony\Component\Process\Process;

class KrpanoTools extends Component
{
    public Spot $spot;
    public $output;
    public $initialized = false;

    public function render()
    {
        return view('livewire.modals.krpano-tools');
    }

    public function mount(Spot $spot)
    {
        $this->spot = $spot;
    }

    public function runCommand()
    {
        if (!file_exists($this->spot->getFirstMediaPath('image_360'))){
            $this->output = "Invalid Image";
            return;
        }

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options'),
        );

        if (File::isDirectory($this->spot->tour_path)){
            File::deleteDirectory($this->spot->tour_path, true);
        }

        $command = $this->getKrpanoCommand();

        $process = new Process($command);

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput, $pusher) {
            $line = str($line)->replace("\r", "\n");
            $processOutput .= $line;
            $pusher->trigger('shell', 'newOutput', $line);
        };

        $process->setTimeout(null)->run($captureOutput);

        if ($process->getExitCode()) {
            $this->output = $processOutput;
        }

    }

    public function getKrpanoCommand()
    {
        $panos_path = $this->spot->tour_path;

        $command = [
            config('krpano.path'),
            'makepano',
            "-outputpath=$panos_path",
        ];

        // options
        foreach (config('krpano.config') as $key => $value){
            $command[] = "-{$key}={$value}";
        }

        // input file: 360 image
        $command[] = $this->spot->getFirstMediaPath('image_360');

        return $command;
    }
}
