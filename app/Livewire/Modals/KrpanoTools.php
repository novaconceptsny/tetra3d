<?php

namespace App\Livewire\Modals;

use App\Enums\Spot\PanoStatus;
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
    public $confirmation_required = false;
    public $confirmed = false;

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
        set_time_limit(-1);

        $this->output = false;

        if (!file_exists($this->spot->getFirstMediaPath('image_360'))){
            $this->output = "No Image Found!";
            return;
        }

        if ($this->spot->panoStatus(true) !== PanoStatus::NOT_PRESENT && !$this->confirmed){
            $this->output = "Panos already exists";
            $this->confirmation_required = true;
            return;
        }

        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options'),
            );
        } catch (\Error|\Exception $exception){
            $this->output = $exception->getMessage();
            return;
        }

        if (File::isDirectory($this->spot->tour_path)){
            File::deleteDirectory("{$this->spot->tour_path}", true);
        }

        $command = $this->getKrpanoCommand();

        $process = new Process($command);
        $process->getStatus();

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

        if ($process->isSuccessful()){
            $this->cleanup();
        }
    }

    public function confirm(){
        $this->confirmed = true;
        $this->output = false;
        $this->runCommand();
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

    private function cleanup()
    {
        $spot = $this->spot;

        try {
            $objXmlDocument = simplexml_load_file("$spot->tour_path/tour.xml");
            $cubeAttributes = $objXmlDocument->scene->image->cube->attributes();
            $panos_url = (string) $cubeAttributes->url;

            $spot->update([
                'metadata' => [
                    'multires' => (string) $cubeAttributes->multires,
                    'panos_url' => $panos_url,
                ],
            ]);

            $spot->generateXml();

            // update cache
            $spot->panoStatus(true);

        } catch (\Exception $exception) {
            $this->output = 'tour.xml could not be found';
        }

        // remove skin and plugins folders
        if (File::isDirectory($this->spot->tour_path)){
            File::deleteDirectory("{$this->spot->tour_path}/plugins");
            File::deleteDirectory("{$this->spot->tour_path}/skin");
        }
    }
}
