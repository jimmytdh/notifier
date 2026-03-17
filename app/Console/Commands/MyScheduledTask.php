<?php

namespace App\Console\Commands;

use Firebase\JWT\JWT;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MyScheduledTask extends Command
{
    protected $jwt_token;
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
        $this->jwt_token = "";
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $now = Carbon::now();

            // Part 1: Notify referrals that have been idle for 15 minutes
            $this->notifyIdleReferrals();

            // Part 2: Forward referrals idle for 30 minutes to EMED
            $this->forwardIdleReferralsToEmed();

        } catch (\Exception $exception) {
            Log::error("Notifier Error: " . $exception->getMessage());
        }

//        try {
//            $codes = DB::table('csmc_notifier.referrals as r')
//                ->join('csmc_notifier.departments as d', 'r.department', '=', 'd.department')
//                ->where(function ($query) {
//                    $query->where('status', '!=', 'DONE')
//                        ->orWhereNull('status');
//                })
//                ->groupBy('r.department')
//                ->select(DB::raw('CONCAT("referrals_", d.code) AS prefixed_code'))
//                ->pluck('prefixed_code')
//                ->toArray();
//
//
//
//
//            if (!empty($codes)) {
//                $curl = curl_init();
////                $bearer_token = 'Authorization: Bearer ' . $this->generateBearerToken();
//                $bearer_token = "Sample bearer Token";
//                $chunks = array_chunk($codes, 5); // Split codes into chunks of 5
//                foreach ($chunks as $chunk) {
//                    $condition = '';
//                    foreach ($chunk as $code) {
//                        $condition .= "'$code' in topics || ";
//                    }
//                    $condition = rtrim($condition, " || "); // Remove the trailing " || "
//
//                    // Updated payload structure to match FCM's requirements
//                    $payload = json_encode([
//                        "message" => [
//                            "condition" => $condition,
//                            "android" => [
//                                "priority" => "high"
//                            ],
//                            "notification" => [
//                                "title" => "Hi there",
//                                "body" => "You have a pending referral that requires action."
//                            ]
//                        ]
//                    ]);
//
//                    $contentLength = strlen($payload);
//
//                    curl_setopt_array($curl, [
//                        CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/notifier-5e4e8/messages:send',
//                        CURLOPT_RETURNTRANSFER => true,
//                        CURLOPT_ENCODING => '',
//                        CURLOPT_MAXREDIRS => 10,
//                        CURLOPT_TIMEOUT => 0,
//                        CURLOPT_FOLLOWLOCATION => true,
//                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                        CURLOPT_POST => true,
//                        CURLOPT_POSTFIELDS => $payload,
//                        CURLOPT_HTTPHEADER => [
//                            $bearer_token,
//                            'Content-Type: application/json',
//                            'Content-Length: ' . $contentLength  // Add Content-Length header
//                        ],
//                        CURLOPT_SSL_VERIFYHOST => 0,
//                        CURLOPT_SSL_VERIFYPEER => 0
//                    ]);
//
//                    $response = curl_exec($curl);
//                    if (curl_errno($curl)) {
//                        Log::error('FCM Error: ' . curl_error($curl));
//                    } else {
//                        Log::info('FCM Responses: ' . $response);
//                    }
//                    curl_close($curl);
//                }
//            }
//
//        } catch (\Exception $exception) {
//            Log::error("Notifier Error: " . $exception->getMessage());
//        }
        return 0;
    }

    private function notifyIdleReferrals()
    {
        $idleReferrals = DB::table('logs as l')
            ->join('csmc_notifier.referrals as r', 'l.referral_id', '=', 'r.id')
            ->join('csmc_notifier.departments as d', 'r.department', '=', 'd.department')
            ->where(function ($query) {
                $query->where('r.status', '!=', 'DONE')
                    ->orWhereNull('r.status');
            })
            ->where('r.type', '=', 'Manual')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, l.date_forwarded, ?) >= 15', [Carbon::now()])
            ->select(
                'r.department',
                DB::raw('MAX(r.id) as id'),
                DB::raw('MAX(d.code) as code'),
                DB::raw('MAX(l.date_forwarded) as date_forwarded') // Get the latest date_forwarded
            )
            ->groupBy('r.department')
            ->get();

        if(count($idleReferrals) < 1){
            Log::error("NOTHING");
            return 0;
        }

        $condtion = '';
        foreach ($idleReferrals as $referral) {
            $condtion .= "'$referral->code' in topics || ";
        }
        $condtion = rtrim($condtion, " || ");

        $payload = json_encode([
            "message" => [
                "condition" => $condtion,
                "android" => [
                    "priority" => "high"
                ],
                "notification" => [
                    "title" => "Hi there",
                    "body" => "You have a pending referral that requires action."
                ]
            ]
        ]);

        $this->sendPushNotification($payload, "notifyIdleReferrals");
    }

    private function forwardIdleReferralsToEmed()
    {
        // Fetch idle referrals where the latest `date_forwarded` exceeds 30 minutes
        $idleReferrals = DB::table('logs as l')
            ->join('csmc_notifier.referrals as r', 'l.referral_id', '=', 'r.id')
            ->join('csmc_notifier.departments as d', 'r.department', '=', 'd.department')
            ->where(function ($query) {
                $query->where('r.status', '!=', 'DONE')
                    ->orWhereNull('r.status');
            })
            ->where('r.type', '=', 'Referral')
            ->whereNotNull('l.date_forwarded') // Ensure date_forwarded exists
            ->select(
                'r.id',
                'd.code',
                'r.patient',
                'r.department',
                DB::raw('MAX(l.date_forwarded) as latest_date_forwarded') // Get the latest `date_forwarded`
            )
            ->groupBy('r.id', 'd.code', 'r.patient', 'r.department')
            ->havingRaw('TIMESTAMPDIFF(MINUTE, MAX(l.date_forwarded), ?) >= 30', [Carbon::now()]) // Check idle time
            ->where('r.department', '!=', 'EMED') // Exclude referrals already in EMED
            ->get()
            ->groupBy('department');

        if ($idleReferrals->isEmpty()) {
            Log::info("No idle referrals to forward to EMED.");
            return 0;
        }

        $condition = "'referrals_EMED' in topics || ";
        foreach ($idleReferrals as $department => $referrals) {
            $departmentCode = $referrals->first()->code;
            $condition .= "'referrals_$departmentCode' in topics || ";

            $referralIds = $referrals->pluck('id');
            DB::table('csmc_notifier.referrals')
                ->whereIn('id', $referralIds)
                ->update(['department' => 'EMED']);

            $logEntries = $referrals->map(function ($referral) {
                return [
                    'referral_id' => $referral->id,
                    'forwarded_by' => '99999999',
                    'tagged_by' => null,
                    'origin' => $referral->department,
                    'destination' => 'EMED',
                    'remarks' => 'Forwarded after 30 minutes of inactivity.',
                    'date_tagged' => null,
                    'date_forwarded' => Carbon::now(),
                ];
            })->toArray();

            DB::table('logs')->insert($logEntries);
        }

        $condition = rtrim($condition, " || ");
        $payload = json_encode([
            "message" => [
                "condition" => $condition,
                "android" => [
                    "priority" => "high"
                ],
                "notification" => [
                    "title" => "Referrals Forwarded to EMED",
                    "body" => "Some referrals have been idle for 30 minutes and have been forwarded to EMED."
                ]
            ]
        ]);

        $this->sendPushNotification($payload, "forwardIdleReferralsToEmed");
    }



    private function sendPushNotification(string $payload, string $from)
    {
        $curl = curl_init();
        $bearer_token = 'Authorization: Bearer ' . $this->generateBearerToken();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/notifier-5e4e8/messages:send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                $bearer_token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ],
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            Log::error($from . " ERROR =>" . curl_error($curl));
        } else {
            Log::error($from . " RESPONSE =>" . $response);
        }

        curl_close($curl);
    }

    public function generateBearerToken()
    {
        try {
            if (Cache::has('firebase_bearer_token')) {
                return Cache::get('firebase_bearer_token');
            }

            if (!$this->jwt_token) {
                $this->jwt_token = $this->createJwtToken();
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $this->jwt_token
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
                CURLOPT_SSL_VERIFYHOST => 0,  // Disable SSL host verification (only for development)
                CURLOPT_SSL_VERIFYPEER => 0   // Disable SSL certificate verification (only for development)
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                throw new \Exception(curl_error($curl));  // Catch curl errors
            }

            curl_close($curl);

            // Parse the response to extract the token and expiration time
            $responseArray = json_decode($response, true);
            if (isset($responseArray['access_token'])) {
                $bearerToken = $responseArray['access_token'];
                $expiresIn = $responseArray['expires_in'];  // Expiry time in seconds, usually 3600

                // Cache the token with an expiration time slightly shorter than its actual expiration
                Cache::put('firebase_bearer_token', $bearerToken, now()->addSeconds($expiresIn - 60));  // Subtract 1 minute for buffer

                return $bearerToken;
            } else {
                throw new \Exception('Failed to retrieve access token.');
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    public static function createJwtToken()
    {
        $serviceAccountPath = public_path('assets/notifier-5e4e8-firebase-adminsdk-ihhu7-65600f7d74.json');

        if (!File::exists($serviceAccountPath)) {
            throw new \Exception('Service account file not found');
        }

        $serviceAccount = json_decode(File::get($serviceAccountPath), true);
        $now = time();

        // Create JWT payload
        $payload = [
            "iss" => $serviceAccount['client_email'], // Issuer (client email)
            "scope" => "https://www.googleapis.com/auth/firebase.messaging", // Scope for Firebase Messaging
            "aud" => "https://oauth2.googleapis.com/token", // Audience for Google OAuth
            "iat" => $now, // Issued at time
            "exp" => $now + 3600 // Expire in 1 hour
        ];

        // Sign the JWT with the private key from the service account
        $privateKey = $serviceAccount['private_key'];
        return JWT::encode($payload, $privateKey, 'RS256'); // RS256 algorithm
    }
}
