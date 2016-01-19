<?php

namespace App\Console\Commands;

use App\Model\QueuedEmail;

class EmailQueue extends \Illuminate\Console\Command {

    protected $signature = "emailqueue {num=1}";
    protected $description = "Process the email queue.";

    public function handle() {
        $num = $this->argument('num');
        $queuedEmails = QueuedEmail::query()->take($num)->get();
        foreach ($queuedEmails as $email) {
            $standardData = [
                'site_name' => config('app.site_name'),
                'user' => $email->recipient,
            ];
            $data = array_merge($standardData, $email->data);
            \Mail::send($email->template, $data, function (\Illuminate\Mail\Message $msg) use ($email) {
                $msg->from(config('app.site_email'), config('app.site_name'));
                if (!empty($email->recipient->email)) {
                    $msg->to($email->recipient->email, $email->recipient->name);
                } else {
                    // If the recipient doesn't have an email address recorded,
                    // forward this to the site administrator to figure out.
                    $msg->to(config('app.site_email'));
                    $transParams = [
                        'name' => $email->recipient->name,
                        'site_email' => config('app.site_email'),
                    ];
                    $newBody = trans('mail.no-recipient-address', $transParams)
                            . '<hr />' . $msg->getSwiftMessage()->getBody();
                    $msg->getSwiftMessage()->setBody($newBody);
                }
                $msg->subject($email->subject);
            });
            $email->delete();
        }
    }

}
