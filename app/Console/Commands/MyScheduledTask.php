<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MyScheduledTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies users every 15 minutes of non action taken referrals';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $codes = DB::table('csmc_notifier.referrals as r')
                ->join('csmc_notifier.departments as d', 'r.department', '=', 'd.department')
                ->where(function ($query) {
                    $query->where('status', '!=', 'DONE')
                        ->orWhereNull('status');
                })
                ->groupBy('r.department')
                ->select(DB::raw('CONCAT("referrals_", d.code) AS prefixed_code'))
                ->pluck('prefixed_code')
                ->toArray();

            if (!empty($codes)) {
                $chunks = array_chunk($codes, 5); // Split codes into chunks of 5
                foreach ($chunks as $chunk) {
                    $condition = '';
                    foreach ($chunk as $code) {
                        $condition .= "'$code' in topics || ";
                    }
                    $condition = rtrim($condition, " || "); // Remove the trailing " || "

                    $payload = json_encode(array(
                        "condition" => $condition,
                        "priority" => "high",
                        "notification" => array(
                            "title" => "Hi there",
                            "body" => "You have a pending referral that requires action."
                        )
                    ));

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $payload,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: key=AAAAU6ekIBA:APA91bEtfmASYObVAvEasSdtyaBqz6e0yi9gJrZ0J9fSxdYpDCdf6JWeN-Kbs7O-sEwEGoGxIn6cIw52RLi-Z2iRH2XfmHf2KH3xDdPWV4Of5C_GxJlq1rstQoNVCFzs_K_W3INFD0ks'
                        ),
                    ));

                    Log::info('CONDITION: ' . $condition);
                    $response = curl_exec($curl);
                    if (curl_errno($curl)) {
                        Log::error('FCM Error: ' . curl_error($curl));
                    } else {
                        Log::info('FCM Response: ' . $response);
                    }
                    curl_close($curl);
                }
            }
        } catch (\Exception $exception) {
            Log::error("Notifier Error: " . $exception->getMessage());
        }
        return 0;
    }
}
