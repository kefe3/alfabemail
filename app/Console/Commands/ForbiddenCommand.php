<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ForbiddenCommand extends Command
{
    public function __construct(string $name)
    {
        parent::__construct();
        $this->signature = $name;
        $this->description = "Bu komut production ortamında çalıştırılamaz.";
    }

    public function handle(): int
    {
        $this->error(
            "`{$this->getName()}` komutu production ortamında çalıştırılamaz!"
        );
        return Command::FAILURE;
    }
}
