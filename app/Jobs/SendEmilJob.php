<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Mail\MyMessage;
class SendEmilJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $users;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;
        // $this->queue = 'email';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // $users = User::simplePaginate(100, ['*'], 'page', $this->page)->toArray();
        // dd($users);
        // foreach ($users as $user) {
        //     \Log::error('the email was send to ' . json_encode($user));
        //   // \Mail::to($user->email)->send(new MyMessage('hello my name is hesham'));
        // }

        $items_limit = 1;
        $delay = 0;
        $data_to_process = [];
        $data_to_dispatch = [];

        if (count($this->users) > $items_limit) {
            $data_to_process = array_slice($this->users, 0, $items_limit);
            $data_to_dispatch = array_slice($this->users, $items_limit);
            // dd( $data_to_process,$data_to_dispatch );
             \Queue::later($delay, new SendEmilJob($data_to_dispatch));
        } else {
            $data_to_process = $this->users;
        }


        foreach($data_to_process as $item) {
            $email = filter_var($item['email'], FILTER_VALIDATE_EMAIL);
            if ($email === false) {
                continue;
            }

            \Log::error('the email was send to ' . json_encode($email));

            // \Mail::to($email)->send(new MyMessage('hello my name is hesham'));


        }

        // if(isset($data_to_dispatch) && count($data_to_dispatch) > 0) {
        //     dispatch(new SendEmilJob($data_to_dispatch));
        // }

    }
}
