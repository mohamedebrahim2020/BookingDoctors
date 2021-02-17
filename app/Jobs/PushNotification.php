<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tokens;
    public $title;
    public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tokens, $title, $body)
    {
        $this->tokens = $tokens;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(FirebaseService::class)->pushNotification($this->tokens, $this->title, $this->body);
    }
}
